<?php

namespace App\Services\Search;

use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\Candidate;
use App\Models\DealPipeline;
use App\Models\Lead;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Backs the navbar's "Global Search" (resources/views/layout/admin/
 * admin_layout.blade.php — the existing `.search-input` inside
 * `.navbar-search-wrapper`). Two things are searched, both scoped to what
 * the current user is actually allowed to see:
 *
 *  - Modules/pages: discovered from the live route table (built from
 *    routes/web.php), never a hand-maintained list — see discoverModules().
 *  - Records: a small, explicit set of core CRM modules (Leads, Todo,
 *    Candidate, Contacts, Deal Pipeline), each queried through its own
 *    Eloquent model reusing that module's own existing staff-scoping rule
 *    (the same column/condition its own controller already filters
 *    listings by) so a search never surfaces a record its module's own
 *    list page would hide from this user.
 */
class GlobalSearchService
{
    /** Route-name segments that mark a utility/action endpoint rather than a page a user would navigate to and browse. */
    private const NOISE_SEGMENTS = [
        'get', 'check', 'store', 'update', 'delete', 'destroy', 'create', 'add', 'remove',
        'export', 'download', 'upload', 'print', 'pdf', 'json', 'ajax', 'search', 'filter',
        'sync', 'callback', 'webhook', 'toggle', 'change', 'cancel', 'confirm', 'verify',
        'resend', 'send', 'test', 'status', 'report', 'log', 'bulk', 'assign', 'unassign',
        'approve', 'reject', 'unpublish', 'preview', 'rename', 'move', 'redirect',
        'login', 'logout', 'edit', 'view', 'pending', 'count', 'data', 'load',
    ];

    /** Trailing route-name segments that mark a route as a module's own "landing" page. */
    private const LANDING_SEGMENTS = ['list', 'index', 'top', 'home'];

    private const MODULE_CACHE_KEY = 'global_search_module_index_v1';

    private const MAX_MODULE_RESULTS = 8;
    private const MAX_RECORDS_PER_MODULE = 5;
    private const MAX_TOTAL_RECORDS = 20;

    /**
     * @return array{modules: array<int, array{label: string, url: string}>, records: array<int, array{module: string, title: string, subtitle: ?string, url: string}>}
     */
    public function search(Admin $user, string $query): array
    {
        $needle = trim($query);
        if (Str::length($needle) < 2) {
            return ['modules' => [], 'records' => []];
        }

        $modules = $this->visibleModules($user);
        $allowedModuleKeys = array_unique(array_column($modules, 'module_key'));

        return [
            'modules' => $this->matchModules($modules, Str::lower($needle)),
            'records' => $this->searchRecords($user, $needle, $allowedModuleKeys),
        ];
    }

    // =====================================================================
    // Modules (page/route search)
    // =====================================================================

    /**
     * @param array<int, array{name: string, label: string, module_key: string, route_uri: string, haystack: string}> $modules
     * @return array<int, array{label: string, url: string}>
     */
    private function matchModules(array $modules, string $needle): array
    {
        $matches = array_values(array_filter(
            $modules,
            fn (array $m) => Str::contains($m['haystack'], $needle)
        ));

        usort($matches, fn (array $a, array $b) => $this->rank($a['label'], $needle) <=> $this->rank($b['label'], $needle));

        // Built from route_uri here, at request time, rather than cached as a
        // ready-made absolute URL: discoverModules()'s result is cached for
        // an hour, and if that cache were ever (re)populated outside a real
        // HTTP request (a console command, a queued job, artisan tinker),
        // url() has no request to read the host from and falls back to
        // config('app.url') — silently baking the wrong domain into every
        // module result for the rest of that cache's lifetime. Resolving it
        // here means it's always built from the current request's actual
        // host, no matter which context populated the cache.
        return array_map(
            fn (array $m) => ['label' => $m['label'], 'url' => url($m['route_uri'])],
            array_slice($matches, 0, self::MAX_MODULE_RESULTS)
        );
    }

    private function rank(string $label, string $needle): int
    {
        $label = Str::lower($label);

        if ($label === $needle) {
            return 0;
        }

        if (Str::startsWith($label, $needle)) {
            return 1;
        }

        return 2;
    }

    /**
     * @return array<int, array{name: string, label: string, module_key: string, route_uri: string, haystack: string}>
     */
    private function visibleModules(Admin $user): array
    {
        $all = $this->discoverModules();

        // Super admins (user_type == 1) see every discovered module, matching
        // the sidebar's unconditional `@if (user_type == 1)` menu branch.
        if ((int) $user->user_type === 1) {
            return $all;
        }

        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if ($permission && (bool) $permission->full_access) {
            return $all;
        }

        return array_values(array_filter($all, function (array $module) use ($permission) {
            // The dashboard itself is always reachable — it's where every user lands.
            if ($module['name'] === 'admin.dashboard') {
                return true;
            }

            $column = $this->permissionColumnFor($module['module_key']);

            return $column && $permission && (bool) $permission->{$column};
        }));
    }

    /**
     * Resolves a route's module key to an actual `adminpermissions` column,
     * if one by that (singular/plural-tolerant) name exists — no manual
     * module-to-permission map is kept. A module whose key matches no real
     * column is treated as not grantable and stays hidden for restricted
     * staff, which is the safe default.
     */
    private function permissionColumnFor(string $key): ?string
    {
        static $resolved = [];

        if (array_key_exists($key, $resolved)) {
            return $resolved[$key];
        }

        foreach (array_unique([$key, Str::plural($key), Str::singular($key)]) as $candidate) {
            if (Schema::hasColumn('adminpermissions', $candidate)) {
                return $resolved[$key] = $candidate;
            }
        }

        return $resolved[$key] = null;
    }

    /**
     * @return array<int, array{name: string, label: string, module_key: string, route_uri: string, haystack: string}>
     */
    private function discoverModules(): array
    {
        return Cache::remember(self::MODULE_CACHE_KEY, now()->addHour(), function () {
            $modules = [];

            foreach (RouteFacade::getRoutes() as $route) {
                if (!in_array('GET', $route->methods(), true)) {
                    continue;
                }

                $name = $route->getName();
                if (!$name || !Str::startsWith($name, 'admin.') || $name === 'admin.') {
                    continue;
                }

                // Scope strictly to the authenticated CRM area. This also keeps
                // unauthenticated `admin.*`-named utility routes (e.g. device
                // auto-login) from ever surfacing here.
                if (!in_array('auth:admin', $route->gatherMiddleware(), true)) {
                    continue;
                }

                if (preg_match('/\{[^?}]+\}/', $route->uri())) {
                    continue; // needs a record id we can't supply generically
                }

                $segments = explode('.', Str::after($name, 'admin.'));
                $first = Str::lower($segments[0]);
                $last = Str::lower(end($segments));

                if (in_array($first, self::NOISE_SEGMENTS, true)) {
                    continue;
                }

                $isLanding = count($segments) === 1 || in_array($last, self::LANDING_SEGMENTS, true);
                if (!$isLanding) {
                    continue;
                }

                if (count($segments) > 1 && in_array($last, self::LANDING_SEGMENTS, true)) {
                    $labelSegments = array_slice($segments, 0, -1);
                } else {
                    $labelSegments = $segments;
                }

                $label = $this->humanize(implode(' ', $labelSegments));
                if ($label === '') {
                    continue;
                }

                // Multiple route names can point at the same URI (aliases) —
                // keep the first one discovered.
                $modules[$route->uri()] ??= [
                    'name' => $name,
                    'label' => $label,
                    'module_key' => Str::snake($segments[0]),
                    'route_uri' => $route->uri(),
                    'haystack' => Str::lower($label . ' ' . str_replace(['/', '-', '_'], ' ', $route->uri())),
                ];
            }

            return array_values($modules);
        });
    }

    private function humanize(string $raw): string
    {
        $spaced = preg_replace('/(?<!^)(?<![A-Z])[A-Z]/', ' $0', $raw);
        $spaced = str_replace(['_', '-'], ' ', $spaced);
        $spaced = trim(preg_replace('/\s+/', ' ', $spaced));

        return Str::title($spaced);
    }

    // =====================================================================
    // Records (in-module search)
    // =====================================================================

    /**
     * @param string[] $allowedModuleKeys
     * @return array<int, array{module: string, title: string, subtitle: ?string, url: string}>
     */
    private function searchRecords(Admin $user, string $needle, array $allowedModuleKeys): array
    {
        $isAdmin = (int) $user->user_type === 1;
        $permission = $isAdmin ? null : Adminpermission::where('staff_id', $user->id)->first();

        $results = [];

        foreach ($this->recordDefinitions() as $definition) {
            if (count($results) >= self::MAX_TOTAL_RECORDS) {
                break;
            }

            if (!in_array($definition['module_key'], $allowedModuleKeys, true)) {
                continue; // this user's own module toggle already hides the module itself
            }

            /** @var Builder $query */
            $query = $definition['model']::query();
            $definition['applyScope']($query, $user, $permission, $isAdmin);
            $definition['applySearch']($query, $needle);

            $records = $query->latest('id')
                ->limit(self::MAX_RECORDS_PER_MODULE)
                ->get();

            foreach ($records as $record) {
                $results[] = [
                    'module' => $definition['moduleLabel'],
                    'title' => $definition['title']($record) ?: ('#' . $record->getKey()),
                    'subtitle' => $definition['subtitle']($record),
                    'url' => $definition['url']($record),
                ];

                if (count($results) >= self::MAX_TOTAL_RECORDS) {
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Explicit, reused wiring per core CRM module: the same model, the same
     * staff-scoping column/condition its own controller already filters
     * listings by, and the same existing "open this record" route each
     * module's own UI (e.g. the dashboard action center) already links to.
     *
     * @return array<int, array{
     *   module_key: string,
     *   moduleLabel: string,
     *   model: class-string,
     *   applyScope: callable(Builder, Admin, ?Adminpermission, bool): void,
     *   applySearch: callable(Builder, string): void,
     *   title: callable(object): string,
     *   subtitle: callable(object): ?string,
     *   url: callable(object): string,
     * }>
     */
    private function recordDefinitions(): array
    {
        $like = fn (Builder $q, array $columns, string $needle) => $q->where(function (Builder $q) use ($columns, $needle) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', '%' . $needle . '%');
            }
        });

        return [
            // Leads — LeadController@jsonData scopes non-admin, non-"leads_view" staff to their own leadassign_id.
            [
                'module_key' => 'leads',
                'moduleLabel' => 'Leads',
                'model' => Lead::class,
                'applyScope' => function (Builder $q, Admin $user, ?Adminpermission $permission, bool $isAdmin) {
                    if (!$isAdmin && optional($permission)->leads_view == 0) {
                        $q->where('leadassign_id', $user->id);
                    }
                },
                'applySearch' => fn (Builder $q, string $needle) => $like($q, ['cand_name', 'mob_no', 'whatsapp_no', 'email', 'company_name', 'crm_id'], $needle),
                'title' => fn ($r) => (string) $r->cand_name,
                'subtitle' => fn ($r) => $r->mob_no ?: $r->email,
                'url' => fn ($r) => route('admin.leads.list.view', ['leadId' => $r->id]),
            ],
            // Todo — TodoController scopes non-admin/non-full_access staff to tasks they're assigned to (comma-list column).
            [
                'module_key' => 'todo',
                'moduleLabel' => 'Todo',
                'model' => Todo::class,
                'applyScope' => function (Builder $q, Admin $user, ?Adminpermission $permission, bool $isAdmin) {
                    if (!$isAdmin && !optional($permission)->full_access) {
                        $q->whereRaw('FIND_IN_SET(?, assignto_id)', [$user->id]);
                    }
                },
                'applySearch' => fn (Builder $q, string $needle) => $like($q, ['task_title', 'task_description'], $needle),
                'title' => fn ($r) => (string) $r->task_title,
                'subtitle' => fn ($r) => $r->finish_on ? 'Due ' . $r->finish_on : null,
                'url' => fn ($r) => route('admin.todo.list.view', ['todoId' => $r->id]),
            ],
            // Candidate — CandidateController scopes non-admin/non-full_access staff to their own careoff_id.
            [
                'module_key' => 'candidate',
                'moduleLabel' => 'Candidate',
                'model' => Candidate::class,
                'applyScope' => function (Builder $q, Admin $user, ?Adminpermission $permission, bool $isAdmin) {
                    $q->where('isdelete', 0);
                    if (!$isAdmin && !optional($permission)->full_access) {
                        $q->where('careoff_id', $user->id);
                    }
                },
                'applySearch' => fn (Builder $q, string $needle) => $like($q, ['cand_name', 'contact_no', 'mobile_no', 'email', 'pass_no', 'reference_no'], $needle),
                'title' => fn ($r) => (string) $r->cand_name,
                'subtitle' => fn ($r) => $r->contact_no ?: $r->mobile_no ?: $r->email,
                'url' => fn ($r) => route('admin.candidate.show', ['id' => $r->id]),
            ],
            // Contacts (Allcontact) — AllContactController@jsonData scopes non-admin staff without allcontact_view to their own careoff_id.
            [
                'module_key' => 'allcontact',
                'moduleLabel' => 'Contacts',
                'model' => Allcontact::class,
                'applyScope' => function (Builder $q, Admin $user, ?Adminpermission $permission, bool $isAdmin) {
                    if (!$isAdmin && optional($permission)->allcontact_view == 0) {
                        $q->where('careoff_id', $user->id);
                    }
                },
                'applySearch' => fn (Builder $q, string $needle) => $like($q, ['full_name', 'email', 'primary_no_wsp', 'company_name', 'mobile_no1_wsp'], $needle),
                'title' => fn ($r) => (string) $r->full_name,
                'subtitle' => fn ($r) => $r->primary_no_wsp ?: $r->email,
                'url' => fn ($r) => route('admin.allcontact.show', ['id' => $r->id]),
            ],
            // Deal Pipeline — DealPipelineController scopes non-admin staff without deal_view to their own care_of.
            [
                'module_key' => 'deal_pipeline',
                'moduleLabel' => 'Deal Pipeline',
                'model' => DealPipeline::class,
                'applyScope' => function (Builder $q, Admin $user, ?Adminpermission $permission, bool $isAdmin) {
                    if (!$isAdmin && optional($permission)->deal_view != 1) {
                        $q->where('care_of', $user->id);
                    }
                },
                'applySearch' => fn (Builder $q, string $needle) => $like($q, ['deal_name', 'name', 'company', 'contact', 'email', 'candidate'], $needle),
                'title' => fn ($r) => (string) ($r->deal_name ?: $r->name),
                'subtitle' => fn ($r) => $r->company ?: $r->contact,
                'url' => fn ($r) => route('admin.dealPipeline.list.view', ['dealId' => $r->id]),
            ],
        ];
    }
}
