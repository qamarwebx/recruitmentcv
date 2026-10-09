<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Support\PartnerLoginTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Live Partners presence (public/worker/js/partner-presence.js): every open
 * portal/website tab of a signed-in partner / team member sends
 *
 * - state=visible  heartbeat of the tab the user is looking at (also counts
 *                  as activity: last activity + active time)
 * - state=hidden   heartbeat of a background tab (keeps it present, no
 *                  activity)
 * - state=left     the tab is closing or navigating away (sendBeacon)
 *
 * with the tab's own random id. Who it is comes only from the server-side
 * session (PartnerLoginTracker::presence() writes rows of this session's
 * login row only), and every expiry is server time - the browser can't mark
 * anyone else, or itself beyond a short TTL. The session rules of the portal
 * apply here too: the global middlewares already end a revoked / expired
 * Pending / removed team member session before this runs; a Rejected
 * registration, and a session left unused (no request or visible tab) for
 * longer than its lifetime, end here.
 *
 * Presence signals never write the session data back: every save ages the
 * flash data, so a heartbeat or a closing tab's beacon landing between a
 * form's redirect and the next page would wipe that page's success / error
 * message (and could overwrite a concurrent request's session changes).
 * A visible heartbeat still keeps the session alive, as the presence ping
 * always did - by refreshing the session file's time, not its contents.
 * Only a signal whose request had to change the session (a resumed
 * tracking row, a session ended here) saves it normally.
 */
class PartnerPresenceController extends Controller
{
    public function __invoke(Request $request)
    {
        $guard = Auth::guard('partner');
        if (!$guard->check()) {
            return response()->json(['status' => 'signed_out'], 401);
        }

        $state = in_array($request->input('state'), ['visible', 'hidden', 'left'], true) ? $request->input('state') : 'visible';
        $tabId = (string) $request->input('tab');
        if (!preg_match('/^[A-Za-z0-9_-]{8,64}$/', $tabId)) {
            // Tabs still running the previous script (no tab id): one shared "legacy" tab.
            $tabId = 'legacy';
        }

        $ended = null;
        if ($guard->user()->isRegistrationRejected()) {
            $ended = 'rejected';
        } elseif ($state !== 'visible' && PartnerLoginTracker::idleExpired()) {
            $ended = 'expired';
        }
        if ($ended) {
            PartnerLoginTracker::close($ended);
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['status' => 'signed_out'], 401);
        }

        $trackingRow = $request->session()->get(PartnerLoginTracker::SESSION_KEY);
        PartnerLoginTracker::presence($tabId, $state);
        if ($state === 'visible') {
            PartnerLoginTracker::touch();
        }
        if ($request->session()->get(PartnerLoginTracker::SESSION_KEY) === $trackingRow) {
            $this->skipSessionSave($request, $state === 'visible');
        }

        return response()->noContent();
    }

    /**
     * The session middleware's save at the end of this request writes
     * nowhere. $keepAlive: refresh the session's lifetime instead (file
     * sessions: the file time; other drivers: a normal save, as before).
     */
    private function skipSessionSave(Request $request, bool $keepAlive): void
    {
        $session = $request->session();
        if ($keepAlive) {
            if (config('session.driver') !== 'file') {
                return;
            }
            $file = rtrim((string) config('session.files'), '/') . '/' . $session->getId();
            if (!is_file($file)) {
                return;   // nothing stored yet: let the normal save create it
            }
            @touch($file);
        }
        $handler = $session->getHandler();
        $session->setHandler(new \Illuminate\Session\NullSessionHandler());
        // The store object is per request here; put the real handler back once the response is done.
        app()->terminating(fn () => $session->setHandler($handler));
    }
}
