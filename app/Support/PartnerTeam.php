<?php

namespace App\Support;

use App\Models\PartnerTeamMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Partner Portal team members: session + permissions.
 *
 * A team member signs in through the existing Partner Login endpoints; the
 * `partner` guard then holds the PARENT partner (so every existing portal
 * query stays scoped to that one partner) and the session holds which team
 * member is acting (SESSION_KEY). Permissions are derived from the Partner
 * Portal routes themselves (worker.partner.* behind worker.partner.auth):
 * module = the route name's first segment, action = view (GET) / create /
 * update / delete - so a new portal page is covered automatically and is
 * denied until a partner grants it. Checked on every request by
 * EnforcePartnerTeamPermissions (never only in the UI), read fresh from the
 * database each time.
 */
class PartnerTeam
{
    public const SESSION_KEY = 'partner_team_member';

    public const ACTIONS = ['view', 'create', 'update', 'delete'];

    /** Portal routes only the partner owner may use (team management, the owner's own login details). */
    public const OWNER_ONLY = ['team-members', 'account.password', 'account.email.send', 'account.email.verify', 'mobile.verify'];

    /**
     * Portal routes every signed-in team member may use. My Account
     * (account / account.update, old profile URL) is self-service: it shows
     * and saves the signed-in account itself (accountHolder()), so it is not
     * a grantable module.
     */
    public const ALWAYS = ['lang.switch', 'logout', 'google.complete', 'account', 'account.update', 'profile'];

    /**
     * Sub-permissions (tabs) inside a module: module => [section => the
     * portal route names (or name prefixes) that belong to it]. Stored in
     * the same permissions JSON as "module.section" => ['view'] (e.g.
     * "website.smtp"). A sectioned module's page itself needs the module +
     * at least one section; every other route of it must map to a section
     * (an unmapped one is denied to team members - fail closed), and a save
     * also needs the module's own action (e.g. website: update).
     */
    public const SECTIONS = [
        'website' => [
            'company_profile' => ['settings.company'],
            'branding' => ['settings.logo'],
            'whatsapp' => ['settings.whatsapp'],
            'website_configuration' => ['website-config'],
            'smtp' => ['settings.smtp'],
            'domain' => ['settings.domain'],
        ],
        // Orders actions. "=orders" = exactly the list route (not orders.*).
        // Sections without a route of their own are checked where they act:
        // search_filter / view_active / view_cancelled in
        // PartnerPortalController::orders()/orderShow(), card_view /
        // table_view by which list panels are rendered, view_candidate /
        // download_cv via CROSS_GRANTS below.
        'orders' => [
            'view' => ['=orders'],
            'search_filter' => [],
            'view_order' => ['orders.show'],
            'assign_visa' => ['orders.visa.store'],
            'view_candidate' => [],
            'download_cv' => [],
            'cancel_booking' => ['orders.cancel'],
            'view_active' => [],
            'view_cancelled' => [],
            'card_view' => [],
            'table_view' => [],
        ],
    ];

    /**
     * Sectioned modules whose sections replace the module's create/update/
     * delete actions: the module grants only "view" (the master switch) and
     * every action is its own section.
     */
    public const SECTION_ONLY = ['orders'];

    /**
     * Another module's route that a section also opens - only for a record
     * of this partner's own orders (see crossGranted()): portal route =>
     * [module, section].
     */
    public const CROSS_GRANTS = [
        'candidates.show' => ['orders', 'view_candidate'],
        'candidates.passport' => ['orders', 'view_candidate'],   // the candidate page's blurred passport image
        'candidates.activity.visit' => ['orders', 'view_candidate'],       // the candidate page's activity reports
        'candidates.activity.heartbeat' => ['orders', 'view_candidate'],
        'candidates.activity.click' => ['orders', 'view_candidate'],
        'candidates.cv' => ['orders', 'download_cv'],
    ];

    /** Each module's own page route (sidebar link / landing page). */
    private const PAGES = [
        'dashboard' => 'dashboard', 'candidates' => 'candidates', 'orders' => 'orders',
        'employer' => 'employer', 'payment' => 'payment', 'customers' => 'customers',
        'website-visitors' => 'website-visitors', 'prices' => 'prices', 'website' => 'website',
    ];

    /** Route-name segments that belong to another module. */
    private const MODULE_ALIASES = ['profile' => 'account', 'website-config' => 'website', 'settings' => 'website'];

    private static array $memo = [];

    /** The acting team member (validated: active, still belongs to the signed-in partner), or null for the owner. */
    public static function current(): ?PartnerTeamMember
    {
        $session = session();
        $data = $session->get(self::SESSION_KEY);
        if (!is_array($data)) {
            return null;
        }

        $partnerId = Auth::guard('partner')->id();
        $memoKey = $data['id'] . ':' . $partnerId;
        if (array_key_exists($memoKey, self::$memo)) {
            return self::$memo[$memoKey];
        }

        $member = $partnerId ? PartnerTeamMember::find($data['id']) : null;
        $valid = $member && $member->status && (int) $member->partner_id === (int) $partnerId && (int) ($data['partner_id'] ?? 0) === (int) $partnerId;

        return self::$memo[$memoKey] = $valid ? $member : null;
    }

    /** A session marked as a team member's, whether or not still valid. */
    public static function hasSession(): bool
    {
        return is_array(session()->get(self::SESSION_KEY));
    }

    public static function start(PartnerTeamMember $member): void
    {
        session()->put(self::SESSION_KEY, ['id' => $member->id, 'partner_id' => $member->partner_id]);
        self::$memo = [];
        $member->forceFill(['last_login_at' => now()])->save();
        // Live Partners: this login session is the team member's.
        PartnerLoginTracker::markTeamMember($member);
    }

    /** Drops the per-request lookup cache (called at the start of each request). */
    public static function forgetMemo(): void
    {
        self::$memo = [];
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
        self::$memo = [];
    }

    /**
     * Whose account "My Account" shows and saves: the signed-in team member,
     * else the partner itself. Resolved from the session/guard only - never
     * from an id in the request.
     */
    public static function accountHolder(): \App\Models\Partner|PartnerTeamMember|null
    {
        return self::current() ?? Auth::guard('partner')->user();
    }

    /**
     * Validates a team member's sign-in identity (name, username, email,
     * mobile) - shared by the partner's Team Members form and the member's
     * own My Account. Unique across team members (except $ignoreId) and
     * partners; at least one identifier. Returns [validator, country code,
     * local mobile or null]; trims username/email on $request.
     */
    public static function identityValidator(\Illuminate\Http\Request $request, ?int $ignoreId, array $extraRules = []): array
    {
        $code = preg_replace('/\D+/', '', (string) $request->input('country_code'));
        $local = $request->filled('mobile') ? PhoneNumber::local($code, (string) $request->input('mobile')) : null;
        $request->merge(['username' => $request->filled('username') ? trim($request->username) : null, 'email' => $request->filled('email') ? trim($request->email) : null]);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), array_merge([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:120', 'regex:/^[^\s@]+$/u', \Illuminate\Validation\Rule::unique('partner_team_members', 'username')->ignore($ignoreId), \Illuminate\Validation\Rule::unique('partners', 'username')],
            'email' => ['nullable', 'email', 'max:255', \Illuminate\Validation\Rule::unique('partner_team_members', 'email')->ignore($ignoreId), \Illuminate\Validation\Rule::unique('partners', 'email')],
            'country_code' => ['nullable', 'string', 'max:5'],
            'mobile' => ['nullable', 'string', 'max:20'],
        ], $extraRules), [
            'username.regex' => __('locale.The username cannot contain spaces or @.'),
            'username.unique' => __('locale.This username is already taken.'),
            'email.unique' => __('locale.This email is already used by another account.'),
        ], [
            'full_name' => __('locale.Full Name'),
            'password' => __('locale.Password'),
        ]);

        $validator->after(function ($v) use ($request, $code, $local, $ignoreId) {
            if (!$request->filled('username') && !$request->filled('email') && !$local) {
                $v->errors()->add('username', __('locale.Enter at least a username, email or mobile number to sign in with.'));
            }
            if ($local) {
                if ($code === '') {
                    $v->errors()->add('country_code', __('locale.Please choose the country code.'));
                } elseif (PartnerMobile::query($code, $local)->exists()
                    || PartnerTeamMember::where('mobile', $local)->where('country_code', $code)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
                    $v->errors()->add('mobile', __('locale.This mobile number is already used by another account.'));
                }
            }
        });

        return [$validator, $code, $local];
    }

    /** Owner = always; team member = only what was granted. */
    public static function allows(string $module, string $action = 'view'): bool
    {
        $member = self::current();

        return !$member || $member->allows($module, $action);
    }

    /** Owner = always; team member = the module (view) AND that section. */
    public static function allowsSection(string $module, string $section): bool
    {
        $member = self::current();

        return !$member || $member->allowsSection($module, $section);
    }

    /** May open a module's page (the same check as a request to that page). */
    public static function canOpen(string $module, ?PartnerTeamMember $member = null): bool
    {
        $member = $member ?? self::current();
        if (!$member) {
            return true;
        }
        $resolved = isset(self::PAGES[$module]) ? self::resolve('worker.partner.' . self::PAGES[$module], 'GET') : [$module, 'view', null];

        return is_array($resolved) && self::permits($member, $resolved);
    }

    /** Whether $member may use a resolve()d [module, action, section] route. */
    public static function permits(PartnerTeamMember $member, array $resolved): bool
    {
        [$module, $action, $section] = $resolved + [null, null, null];
        if (!$member->allows($module, in_array($module, self::SECTION_ONLY, true) ? 'view' : $action)) {
            return false;
        }
        if ($section !== null) {
            return $member->allowsSection($module, $section);
        }
        if (isset(self::SECTIONS[$module])) {
            // The module's page: needs at least one of its sections.
            foreach (array_keys(self::SECTIONS[$module]) as $s) {
                if ($member->allowsSection($module, $s)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * A CROSS_GRANTS route opened through its section: the member holds that
     * section and the record (route {id} = candidate slug) is in one of
     * this partner's own orders, in an order state the member may see.
     */
    public static function crossGranted(PartnerTeamMember $member, ?string $routeName, \Illuminate\Http\Request $request): bool
    {
        $grant = self::CROSS_GRANTS[Str::after((string) $routeName, 'worker.partner.')] ?? null;
        if (!$grant || !$member->allowsSection($grant[0], $grant[1])) {
            return false;
        }
        $slug = (string) optional($request->route())->parameter('id');
        if ($slug === '') {
            return false;
        }

        $states = self::orderStates($member);
        $statuses = \Illuminate\Support\Facades\DB::table('bookings')
            ->join('candidates', 'candidates.id', '=', 'bookings.cand_id')
            ->where('bookings.partner_id', $member->partner_id)
            ->where('candidates.slug_text', $slug)
            ->pluck('bookings.booking_status');

        return $statuses->contains(fn ($status) => in_array((int) $status === 2 ? 'cancelled' : 'active', $states, true));
    }

    /**
     * Order groups (active / cancelled) the account may list or open. A team
     * member: the granted ones; with neither granted, the default Active
     * list (what Orders shows without a choice).
     */
    public static function orderStates(?PartnerTeamMember $member = null): array
    {
        $member = $member ?? self::current();
        if (!$member) {
            return ['active', 'cancelled'];
        }
        $states = array_values(array_filter(['active', 'cancelled'], fn ($state) => $member->allowsSection('orders', 'view_' . $state)));

        return $states ?: ['active'];
    }

    /**
     * Orders page tabs in display order: Active + Deployed (deployed orders
     * were part of the Active list - same "View Active Orders" permission)
     * and Cancelled, as orderStates() allows.
     */
    public static function orderTabs(?PartnerTeamMember $member = null): array
    {
        $states = self::orderStates($member);

        return array_values(array_merge(
            in_array('active', $states, true) ? ['active', 'deployed'] : [],
            in_array('cancelled', $states, true) ? ['cancelled'] : []
        ));
    }

    /** Order list layouts (card / table) shown; neither granted = the default Card view. */
    public static function orderViews(?PartnerTeamMember $member = null): array
    {
        $member = $member ?? self::current();
        if (!$member) {
            return ['card', 'table'];
        }
        $views = array_values(array_filter(['card', 'table'], fn ($view) => $member->allowsSection('orders', $view . '_view')));

        return $views ?: ['card'];
    }

    /**
     * Stops the request with the same "no permission" response the
     * middleware gives (JSON 403 for AJAX/JSON, else 403) unless $allowed.
     */
    public static function authorize(bool $allowed): void
    {
        if ($allowed) {
            return;
        }
        $message = __('locale.You do not have permission to access this page.');
        $request = request();
        if ($request->expectsJson() || $request->ajax()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json(['status' => 'error', 'message' => $message], 403));
        }
        abort(403, $message);
    }

    public static function isOwner(): bool
    {
        return self::current() === null;
    }

    /**
     * [module, action, section|null] for a Partner Portal route name,
     * 'owner' / 'always', or null when it is not a portal route. Section:
     * see SECTIONS ('?' = a sectioned module's route mapped to no section).
     */
    public static function resolve(?string $routeName, string $method)
    {
        if (!$routeName || !str_starts_with($routeName, 'worker.partner.')) {
            return null;
        }

        $name = Str::after($routeName, 'worker.partner.');
        if (in_array($name, self::ALWAYS, true)) {
            return 'always';
        }
        foreach (self::OWNER_ONLY as $owner) {
            if ($name === $owner || str_starts_with($name, $owner . '.')) {
                return 'owner';
            }
        }

        $segment = Str::before($name, '.');
        $module = self::MODULE_ALIASES[$segment] ?? $segment;

        // Candidate Detail activity reports (presence / clicks) only record
        // viewing, so they count as "view" like the page itself.
        if (in_array(strtoupper($method), ['GET', 'HEAD'], true) || preg_match('/\.filter\.(save|reset)$/', $name) || preg_match('/\.activity\.(visit|heartbeat|click)$/', $name)) {
            $action = 'view';
        } elseif (preg_match('/\.(store|hire)$/', $name)) {
            $action = 'create';
        } elseif (preg_match('/\.(delete|destroy|unlink|cancel)$/', $name)) {
            $action = 'delete';
        } else {
            $action = 'update';
        }

        $section = null;
        $exact = isset(self::SECTIONS[$module]) && collect(self::SECTIONS[$module])->flatten()->contains('=' . $name);
        if (isset(self::SECTIONS[$module]) && ($name !== $module || $exact)) {
            $section = '?';
            foreach (self::SECTIONS[$module] as $key => $prefixes) {
                foreach ($prefixes as $prefix) {
                    $matches = str_starts_with($prefix, '=')
                        ? $name === substr($prefix, 1)
                        : ($name === $prefix || str_starts_with($name, $prefix . '.'));
                    if ($matches) {
                        $section = $key;
                        break 2;
                    }
                }
            }
        }

        return [$module, $action, $section];
    }

    /**
     * Every grantable module => its actions, built from the registered
     * Partner Portal routes (worker.partner.auth group).
     */
    public static function modules(): array
    {
        $modules = [];
        foreach (Route::getRoutes() as $route) {
            if (!in_array('worker.partner.auth', $route->gatherMiddleware(), true)) {
                continue;
            }
            foreach ($route->methods() as $method) {
                if ($method === 'HEAD') {
                    continue;
                }
                $resolved = self::resolve($route->getName(), $method);
                if (is_array($resolved)) {
                    $modules[$resolved[0]][$resolved[1]] = true;
                }
            }
        }

        $order = ['dashboard', 'candidates', 'orders', 'employer', 'payment', 'customers', 'website-visitors', 'prices', 'website', 'account'];
        uksort($modules, fn ($a, $b) => (array_search($a, $order, true) === false ? 99 : array_search($a, $order, true)) <=> (array_search($b, $order, true) === false ? 99 : array_search($b, $order, true)));

        foreach (self::SECTION_ONLY as $module) {
            if (isset($modules[$module])) {
                $modules[$module] = ['view' => true];   // its actions are sections
            }
        }

        return array_map(fn ($actions) => array_values(array_filter(self::ACTIONS, fn ($a) => isset($actions[$a]))), $modules);
    }

    public static function moduleLabel(string $module): string
    {
        $labels = [
            'dashboard' => 'Dashboard', 'candidates' => 'Worker CV', 'orders' => 'Orders', 'employer' => 'Employer',
            'payment' => 'Payment', 'customers' => 'Customers', 'website-visitors' => 'Website Visitor',
            'prices' => 'Price Update', 'website' => 'Website', 'account' => 'My Account',
        ];

        return isset($labels[$module]) ? __('locale.' . $labels[$module]) : Str::headline($module);
    }

    /** Label of a SECTIONS entry (Website: the page's own tab names; Orders: its action names). */
    public static function sectionLabel(string $module, string $section): string
    {
        $labels = [
            'website' => [
                'company_profile' => 'Company Profile', 'branding' => 'Branding', 'whatsapp' => 'WhatsApp',
                'website_configuration' => 'Website Config', 'smtp' => 'SMTP', 'domain' => 'Domain',
            ],
            'orders' => [
                'view' => 'View Orders', 'search_filter' => 'Search/Filter Orders', 'view_order' => 'View Order',
                'assign_visa' => 'Assign Visa', 'view_candidate' => 'View Candidate', 'download_cv' => 'Download CV',
                'cancel_booking' => 'Cancel Booking', 'view_active' => 'View Active Orders', 'view_cancelled' => 'View Cancelled Orders',
                'card_view' => 'Card View', 'table_view' => 'Table View',
            ],
        ];

        return isset($labels[$module][$section]) ? __('locale.' . $labels[$module][$section]) : Str::headline($section);
    }

    /** Label of a stored permission key: "module" or "module.section". */
    public static function permissionLabel(string $key): string
    {
        if (str_contains($key, '.')) {
            [$module, $section] = explode('.', $key, 2);

            return self::moduleLabel($module) . ' › ' . self::sectionLabel($module, $section);
        }

        return self::moduleLabel($key);
    }

    /** Keeps only real modules/actions; any granted action implies view. */
    public static function sanitize($input): array
    {
        $modules = self::modules();
        $clean = [];
        foreach ((array) $input as $module => $actions) {
            if (!isset($modules[$module])) {
                continue;
            }
            $actions = array_values(array_intersect($modules[$module], (array) $actions));
            if ($actions && !in_array('view', $actions, true) && in_array('view', $modules[$module], true)) {
                array_unshift($actions, 'view');
            }
            if ($actions) {
                $clean[$module] = $actions;
            }
        }

        // Sections ("website.smtp" => ['view']): kept only under a granted module.
        foreach (self::SECTIONS as $module => $sections) {
            if (!isset($clean[$module])) {
                continue;
            }
            foreach (array_keys($sections) as $section) {
                $key = $module . '.' . $section;
                if (in_array('view', (array) (((array) $input)[$key] ?? []), true)) {
                    $clean[$key] = ['view'];
                }
            }
        }

        return $clean;
    }

    /** Where a team member lands: Dashboard when granted, else the first granted page; null = nothing granted. */
    public static function homeUrl(?PartnerTeamMember $member = null, bool $absolutePath = true): ?string
    {
        $member = $member ?? self::current();
        foreach (self::PAGES as $module => $route) {
            if (!$member || self::canOpen($module, $member)) {
                return route('worker.partner.' . $route, [], $absolutePath);
            }
        }

        // Nothing granted: their own My Account (always available).
        return route('worker.partner.account', [], $absolutePath);
    }
}
