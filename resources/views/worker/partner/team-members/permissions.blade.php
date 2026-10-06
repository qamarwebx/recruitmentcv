{{-- Team member permissions, one collapsible group per module (the Website
Config sections' collapse: .wp-wc-section + partner-website-config-collapse.js).
Same modules/actions (App\Support\PartnerTeam::modules()) and the same
permissions[module][] / permissions[module.section][] inputs as before.
$editable: checkboxes (Edit form) or read-only status (details page).
$granted: the member's permissions (form: including old() input).
$open: every group starts open (default: collapsed); its header still
opens/closes only that group.
data-perm-item / data-perm-part: hooks for the Permission page's live search
(team-member-permission-search.js) - display only, inputs unchanged. --}}
@php
    $team = \App\Support\PartnerTeam::class;
    $open = $open ?? false;
@endphp
<div class="wp-perm-groups" data-perm-groups>
    @foreach ($modules as $module => $actions)
        @php
            $sections = array_keys($team::SECTIONS[$module] ?? []);
            $grantedActions = array_values(array_intersect($actions, (array) ($granted[$module] ?? [])));
            $grantedSections = array_values(array_filter($sections, fn ($section) => in_array('view', (array) ($granted[$module . '.' . $section] ?? []), true)));
            $selected = count($grantedActions) + count($grantedSections);
            $total = count($actions) + count($sections);
            $bodyId = 'wp-perm-' . ($editable ? 'edit' : 'view') . '-' . \Illuminate\Support\Str::slug($module);
            $moduleLabel = $team::moduleLabel($module);
        @endphp
        <div class="wp-wc-section wp-perm-group{{ $open ? ' is-open' : '' }}" data-wc-collapse data-perm-module="{{ $module }}">
            <button type="button" class="wp-wc-section-header" data-wc-collapse-toggle aria-expanded="{{ $open ? 'true' : 'false' }}" aria-controls="{{ $bodyId }}">
                <span class="wp-wc-section-title">{{ $moduleLabel }}</span>
                <span class="wp-perm-group-meta">
                    @if ($editable)
                        <span class="wp-perm-dot" aria-hidden="true"></span>
                    @else
                        <span class="wp-badge {{ $selected ? 'is-success' : 'is-neutral' }}">{{ $selected }} / {{ $total }}</span>
                    @endif
                    <svg class="wp-wc-section-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </span>
            </button>
            <div class="wp-wc-section-body" id="{{ $bodyId }}" data-wc-collapse-body>
                <div class="wp-wc-section-body-inner">
                    <div class="wp-perm-label" data-perm-part="actions">{{ __('locale.Module') }}</div>
                    <div class="wp-perm-actions" data-perm-part="actions">
                        @foreach ($team::ACTIONS as $action)
                            @continue (!in_array($action, $actions, true))
                            @if ($editable)
                                <label class="wp-perm-check" data-perm-item="actions">
                                    <input type="checkbox" name="permissions[{{ $module }}][]" value="{{ $action }}" aria-label="{{ $moduleLabel }} - {{ $action }}"
                                        @checked(in_array($action, $grantedActions, true))>
                                    <span>{{ __('locale.' . ucfirst($action)) }}</span>
                                </label>
                            @else
                                <span class="wp-perm-check" data-perm-item="actions">
                                    <span class="wp-badge {{ in_array($action, $grantedActions, true) ? 'is-success' : 'is-neutral' }}">{{ in_array($action, $grantedActions, true) ? '✓' : '✕' }}</span>
                                    <span>{{ __('locale.' . ucfirst($action)) }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>

                    {{-- Tabs / actions inside this module (PartnerTeam::SECTIONS): one
                    switch each; only effective while the module itself is granted. --}}
                    @if ($sections)
                        @php $sectionOnly = in_array($module, $team::SECTION_ONLY, true); @endphp
                        <div class="wp-perm-label" data-perm-part="sections">{{ $sectionOnly ? __('locale.Actions') : __('locale.Tabs') }}</div>
                        <p class="wp-field-hint wp-perm-hint" data-perm-part="sections">{{ $sectionOnly
                            ? __('locale.Allowed action (needs :module)', ['module' => $moduleLabel])
                            : __('locale.Tab access (saving also needs Update on :module)', ['module' => $moduleLabel]) }}</p>
                        <ul class="wp-perm-children" data-perm-part="sections">
                            @foreach ($sections as $section)
                                @php
                                    $sectionKey = $module . '.' . $section;
                                    $isGranted = in_array($section, $grantedSections, true);
                                @endphp
                                <li data-perm-item="sections">
                                    <span class="wp-perm-arrow" aria-hidden="true">&rarr;</span>
                                    @if ($editable)
                                        <label class="wp-perm-check">
                                            <input type="checkbox" name="permissions[{{ $sectionKey }}][]" value="view" aria-label="{{ $team::permissionLabel($sectionKey) }}" @checked($isGranted)>
                                            <span>{{ $team::sectionLabel($module, $section) }}</span>
                                        </label>
                                    @else
                                        <span class="wp-perm-check">
                                            <span class="wp-badge {{ $isGranted ? 'is-success' : 'is-neutral' }}">{{ $isGranted ? '✓' : '✕' }}</span>
                                            <span>{{ $team::sectionLabel($module, $section) }}</span>
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
