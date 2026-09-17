{{-- Repeatable Profession + Openings + Monthly Salary rows, shared by the
Add and Edit Employer modals - mirrors the admin's dispItemApp/dispItemAppEd
table (addVisaDelegation/addEditVisaDelegation JS) so a partner can attach
more than one profession/openings/salary combination to a single Employer
record. Monthly Salary lives here (one value per profession row) rather
than as a standalone single field, since a multi-profession Employer can
reasonably pay different salaries per profession; storage mirrors
proff_id/openings exactly - implode(',', proff_id)/implode(',', openings)/
implode(',', salary), same index across all three, written by
PartnerPortalController@employerStore/employerUpdate.

Expects (all optional):
- $professionOptions: Collection of Profession, required
- $prefillRows: array of ['proff_id' => int|string, 'openings' => string, 'salary' => string] pairs;
  defaults to a single empty row --}}
@php
    $prefillRows = $prefillRows ?? [['proff_id' => '', 'openings' => '', 'salary' => '']];
    if (empty($prefillRows)) {
        $prefillRows = [['proff_id' => '', 'openings' => '', 'salary' => '']];
    }
@endphp
<table class="w-visa-rows-table" data-visa-rows>
    <thead>
        <tr>
            <th>{{ __('locale.Profession') }}</th>
            <th>{{ __('locale.Openings') }}</th>
            <th>{{ __('locale.Monthly Salary') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($prefillRows as $row)
            <tr data-visa-row>
                <td>
                    <select name="proff_id[]" class="w-select w-select2" data-placeholder="{{ __('locale.Select Profession') }}">
                        <option value=""></option>
                        @foreach ($professionOptions as $profession)
                            <option value="{{ $profession->id }}" @selected(($row['proff_id'] ?? null) == $profession->id)>{{ $profession->display_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="openings[]" class="w-input" placeholder="{{ __('locale.Enter number of vacancies') }}" value="{{ $row['openings'] ?? '' }}">
                </td>
                <td>
                    <input type="text" name="salary[]" class="w-input" placeholder="{{ __('locale.Enter salary...') }}" value="{{ $row['salary'] ?? '' }}">
                </td>
                <td>
                    <button type="button" class="w-btn w-btn-outline w-btn-sm" data-remove-visa-row style="{{ count($prefillRows) <= 1 ? 'visibility:hidden;' : '' }}">{{ __('locale.Remove') }}</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<button type="button" class="w-btn w-btn-outline w-btn-sm" data-add-visa-row style="margin-top:8px;">{{ __('locale.Add Profession') }}</button>

{{-- Pristine template row, never Select2-initialized, cloned by
employer-widgets.js each time a row is added so the live Select2 instances
on existing rows are never disturbed. --}}
<template data-visa-row-template>
    <tr data-visa-row>
        <td>
            <select name="proff_id[]" class="w-select w-select2" data-placeholder="{{ __('locale.Select Profession') }}">
                <option value=""></option>
                @foreach ($professionOptions as $profession)
                    <option value="{{ $profession->id }}">{{ $profession->display_name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" name="openings[]" class="w-input" placeholder="{{ __('locale.Enter number of vacancies') }}">
        </td>
        <td>
            <input type="text" name="salary[]" class="w-input" placeholder="{{ __('locale.Enter salary...') }}">
        </td>
        <td>
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-remove-visa-row>{{ __('locale.Remove') }}</button>
        </td>
    </tr>
</template>
