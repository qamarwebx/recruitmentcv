<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\WebsiteSmtpSetting;
use App\Support\GoogleSmtpException;
use App\Support\GoogleSmtpOAuth;
use App\Support\GoogleSmtpPending;
use App\Support\SmtpSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Partner Website -> SMTP -> "Add SMTP With Google": Gmail SMTP with Google
 * OAuth 2.0 (XOAUTH2 - Google gives no SMTP password). Flow:
 *
 * 1. start() (signed-in partner, SMTP permission, POST + CSRF): a random
 *    state + PKCE verifier kept server-side (cache, keyed by the state's
 *    hash, with the partner and the host to return to) and the state's hash
 *    in this session; the browser goes to Google's consent page.
 * 2. callback() (the one registered redirect URI, any host): the state must
 *    exist (single use, 10 minutes); the code is exchanged server-side and
 *    the account checked (verified email, Gmail scope, refresh token); the
 *    result goes back to the partner's own host as a single-use handoff
 *    (2 minutes) - an own domain does not share this host's session.
 * 3. complete() (back on the partner's host, signed in): the handoff must
 *    be this partner's and match this session's state; the connected
 *    account then waits in the session (GoogleSmtpPending, token encrypted)
 *    and the SMTP tab opens its prefilled Google form.
 * 4. save() ("Add SMTP" - the form has no separate test step): validates
 *    the fields, checks the XOAUTH2 login server-side (no email is sent),
 *    and only then adds the SMTP last in the order, in one transaction
 *    (SmtpSettings::saveGoogle()). Any failure - invalid fields, refused
 *    login, timeout, database error - saves nothing, so the list and which
 *    SMTP sends first stay as they were, and the connected account stays
 *    for another try. (test() remains as a login check on its own.)
 *
 * Tokens never reach the browser or the logs; every action works on the
 * signed-in partner's own list only.
 */
class PartnerSmtpGoogleController extends Controller
{
    private const STATE_KEY = 'google-smtp-state:';
    private const HANDOFF_KEY = 'google-smtp-handoff:';

    public function start(Request $request)
    {
        $partner = $this->partner();

        $rateKey = 'partner-smtp-google:' . $partner->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return $this->error(__('locale.Too many attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($rateKey)]), 429);
        }
        RateLimiter::hit($rateKey, 600);

        if (!GoogleSmtpOAuth::canAuthorize()) {
            return $this->error(self::messages()['google_not_configured']);
        }
        if (SmtpSettings::list($partner->id)->count() >= WebsiteSmtpSetting::MAX_PER_WEBSITE) {
            return $this->error(__('locale.You can add at most :max SMTPs.', ['max' => WebsiteSmtpSetting::MAX_PER_WEBSITE]));
        }

        $state = Str::random(48);
        $verifier = GoogleSmtpOAuth::newCodeVerifier();
        Cache::put(self::STATE_KEY . hash('sha256', $state), [
            'partner_id' => (int) $partner->id,
            'host' => strtolower($request->getHost()),
            'verifier' => Crypt::encryptString($verifier),
        ], now()->addSeconds(GoogleSmtpPending::FLOW_TTL));
        GoogleSmtpPending::startFlow($state, (int) $partner->id);

        return response()->json(['status' => 'success', 'redirect' => GoogleSmtpOAuth::authorizationUrl($state, $verifier)]);
    }

    /** Google's redirect (no session needed - the state is the proof). */
    public function callback(Request $request)
    {
        $state = (string) $request->query('state', '');
        $flow = $state !== '' && strlen($state) <= 128 ? Cache::pull(self::STATE_KEY . hash('sha256', $state)) : null;
        if (!is_array($flow) || empty($flow['host']) || empty($flow['partner_id'])) {
            return response()->view('worker.partner.smtp-google-expired', [], 400);
        }

        if ($request->filled('error') || !$request->filled('code')) {
            // access_denied (cancelled), admin_policy_enforced (Workspace), ...
            $result = ['error' => GoogleSmtpException::DENIED];
        } else {
            try {
                $connection = GoogleSmtpOAuth::connect((string) $request->query('code'), Crypt::decryptString((string) $flow['verifier']));
                $result = ['email' => $connection['email'], 'token' => Crypt::encryptString($connection['refresh_token'])];
            } catch (GoogleSmtpException $e) {
                $result = ['error' => $e->reason];
            } catch (\Throwable $e) {
                $result = ['error' => GoogleSmtpException::EXCHANGE_FAILED];
            }
        }

        $handoff = Str::random(48);
        Cache::put(self::HANDOFF_KEY . hash('sha256', $handoff), $result + [
            'partner_id' => (int) $flow['partner_id'],
            'state' => hash('sha256', $state),
        ], now()->addMinutes(2));

        // Back to the host the partner started on (server-side value, never from this request).
        return redirect()->away('https://' . $flow['host'] . route('worker.partner.settings.smtp.google.complete', ['handoff' => $handoff], false));
    }

    public function complete(Request $request)
    {
        $partner = $this->partner();
        $back = redirect()->to(route('worker.partner.website', ['tab' => 'smtp']) . '#smtp');

        $handoff = (string) $request->query('handoff', '');
        $data = $handoff !== '' && strlen($handoff) <= 128 ? Cache::pull(self::HANDOFF_KEY . hash('sha256', $handoff)) : null;
        $valid = is_array($data)
            && (int) ($data['partner_id'] ?? 0) === (int) $partner->id
            && GoogleSmtpPending::finishFlow((string) ($data['state'] ?? ''), (int) $partner->id);

        if (!$valid) {
            // Not this session's sign-in: an authorization it carried is never used.
            if (is_array($data) && !empty($data['token'])) {
                try {
                    SmtpSettings::revokeGoogleUnlessInUse(Crypt::decryptString((string) $data['token']), (string) ($data['email'] ?? ''));
                } catch (\Throwable $e) {
                }
            }

            return $back->with('smtp_google_notice', ['error', __('locale.This Google sign-in has expired or did not start here. Please click "Add SMTP With Google" again.')]);
        }

        if (!empty($data['error'])) {
            return $back->with('smtp_google_notice', ['error', self::reasonMessage((string) $data['error'])]);
        }

        GoogleSmtpPending::put((int) $partner->id, (string) $data['email'], Crypt::decryptString((string) $data['token']));

        return $back->with('smtp_google_notice', ['success', __('locale.Google account :email connected. Review the details, then click Add SMTP - the connection is tested automatically.', ['email' => $data['email']])]);
    }

    /** XOAUTH2 login check of the connected account - no email is sent. */
    public function test(Request $request)
    {
        $partner = $this->partner();

        $rateKey = 'partner-smtp-google-test:' . $partner->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return $this->error(__('locale.Too many attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($rateKey)]), 429);
        }
        RateLimiter::hit($rateKey, 600);

        $pending = GoogleSmtpPending::get((int) $partner->id);
        if (!$pending) {
            return $this->error(__('locale.Connect a Google account first.'));
        }

        $draft = new WebsiteSmtpSetting(array_merge(GoogleSmtpOAuth::smtpDefaults(), [
            'partner_id' => (int) $partner->id,
            'mailer' => WebsiteSmtpSetting::MAILER_GOOGLE,
            'username' => $pending['email'],
            'from_address' => $pending['email'],
            'status' => true,
        ]));
        $draft->password = $pending['refresh_token'];

        if ($error = SmtpSettings::connectionError($draft, ['messages' => self::messages()])) {
            return $this->error($error);
        }
        GoogleSmtpPending::markTested((int) $partner->id);

        return response()->json(['status' => 'success', 'message' => __('locale.Connection successful: Gmail accepted the sign-in for :email. You can add the SMTP now.', ['email' => $pending['email']])]);
    }

    /** Adds the tested Google SMTP (last in the sending order). */
    public function save(Request $request)
    {
        $partner = $this->partner();

        $rateKey = 'partner-smtp-save:' . $partner->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return $this->error(__('locale.Too many attempts. Please try again in a minute.'), 429);
        }
        RateLimiter::hit($rateKey, 60);

        // One add at a time per partner: a second click / tab while the first
        // is still testing + saving is refused (no duplicate SMTP).
        $lock = Cache::lock('partner-smtp-google-save:' . $partner->id, WebsiteSmtpSetting::TIMEOUT + 40);
        if (!$lock->get()) {
            return $this->error(__('locale.This SMTP is already being added. Please wait.'), 409);
        }

        try {
            $pending = GoogleSmtpPending::get((int) $partner->id);
            if (!$pending) {
                return $this->error(__('locale.Connect a Google account first.'));
            }

            // Validation, then the connection test, then the save - nothing is
            // written unless the test passed and the save succeeds.
            try {
                SmtpSettings::saveGoogle((int) $partner->id, null, $request->only(['name', 'from_name', 'status']), (int) $partner->id, [
                    'messages' => self::messages(),
                    'attributes' => ['name' => __('locale.Name'), 'from_name' => __('locale.From Name')],
                ], ['email' => $pending['email'], 'refresh_token' => $pending['refresh_token']]);
            } catch (ValidationException $e) {
                return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
            } catch (\Throwable $e) {
                // E.g. the database refused the save after a passed test: nothing was added.
                \Illuminate\Support\Facades\Log::error('Google SMTP could not be saved', ['partner_id' => $partner->id, 'exception' => get_class($e)]);

                return $this->error(__('locale.The SMTP could not be saved, so nothing was changed. Please try again.'), 500);
            }
            GoogleSmtpPending::forget(false);

            return $this->listResponse((int) $partner->id, __('locale.SMTP connection verified and configuration added successfully.'));
        } finally {
            $lock->release();
        }
    }

    /** Cancel: the connected account is dropped (and its authorization revoked when unused). */
    public function discard(Request $request)
    {
        $partner = $this->partner();
        GoogleSmtpPending::forget(true);

        return $this->listResponse((int) $partner->id, __('locale.Google connection cancelled.'));
    }

    /** Translated SmtpSettings messages for the Partner Portal (manual + Google SMTPs). */
    public static function messages(): array
    {
        return [
            'host.regex' => __('locale.Enter a valid SMTP host, e.g. smtp.example.com.'),
            'port.in' => __('locale.Use one of the standard SMTP ports: :ports.'),
            'host_not_found' => __('locale.This SMTP host could not be found.'),
            'host_not_allowed' => __('locale.This SMTP host is not allowed.'),
            'duplicate' => __('locale.This SMTP (same host, port and username) is already in your list.'),
            'limit' => __('locale.You can add at most :max SMTPs.'),
            'auth_failed' => __('locale.The SMTP server rejected the username or password.'),
            'connect_failed' => __('locale.Could not connect to the SMTP server. Please check the host, port and encryption.'),
            'google_not_configured' => __('locale.Google sign-in for SMTP is not set up yet. Please contact support.'),
            'google_revoked' => __('locale.Google no longer allows sending with this account (access removed or expired). Please connect the account again.'),
            'google_unavailable' => __('locale.Google could not be reached. Please try again in a moment.'),
            'google_auth_failed' => __('locale.Gmail refused the sign-in for this account. For a Google Workspace account, the administrator may need to allow SMTP access for it.'),
        ];
    }

    /** The message for a failed connection (GoogleSmtpException reason). */
    public static function reasonMessage(string $reason): string
    {
        return match ($reason) {
            GoogleSmtpException::NOT_CONFIGURED => self::messages()['google_not_configured'],
            GoogleSmtpException::DENIED => __('locale.Google sign-in was cancelled or not allowed for this account. Nothing was changed.'),
            GoogleSmtpException::SCOPE_MISSING => __('locale.Gmail sending permission was not granted. Please try again and allow RecruitmentCV to send email as you (the "Read, compose, send and permanently delete all your email from Gmail" permission Google shows) - Gmail SMTP cannot be used without it.'),
            GoogleSmtpException::NO_REFRESH_TOKEN => __('locale.Google did not grant offline access, which sending emails later needs. Please try again and approve the access.'),
            GoogleSmtpException::EMAIL_UNVERIFIED => __('locale.Google did not confirm a verified email address for this account.'),
            GoogleSmtpException::REFRESH_FAILED => self::messages()['google_unavailable'],
            default => __('locale.The Google sign-in could not be completed. Please try again.'),
        };
    }

    private function partner(): Partner
    {
        return Auth::guard('partner')->user();
    }

    private function error(string $message, int $status = 422)
    {
        return response()->json(['status' => 'error', 'message' => $message], $status);
    }

    /** Success + this partner's re-rendered SMTP list (as PartnerPortalController::smtpResponse). */
    private function listResponse(int $partnerId, string $message)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'html' => view('worker.partner.partials.smtp-list', ['smtps' => SmtpSettings::list($partnerId)])->render(),
        ]);
    }
}
