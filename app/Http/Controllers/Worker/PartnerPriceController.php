<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\PartnerPrice;
use App\Models\Profession;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Partner Portal -> Settings -> Price Update: the logged-in partner's own
 * Service Price + Departure Days per Experience Type + Profession (shown on
 * candidate detail pages via PartnerPrice::labelsFor()). Every query is
 * scoped to the partner guard's id - never a partner_id from the request.
 */
class PartnerPriceController extends Controller
{
    private function partnerId(): int
    {
        return Auth::guard('partner')->id();
    }

    public function index()
    {
        $prices = PartnerPrice::with('profession:id,eng_name,ar_name')
            ->where('partner_id', $this->partnerId())
            ->orderBy('proff_id')
            ->orderBy('exp_type')
            ->get();

        $professions = Profession::orderBy('eng_name')->get(['id', 'eng_name', 'ar_name']);
        $experienceTypes = Candidate::EXPERIENCE_TYPES;

        return view('worker.partner.prices', compact('prices', 'professions', 'experienceTypes'));
    }

    public function store(Request $request)
    {
        $partnerId = $this->partnerId();

        $data = $request->validate([
            'exp_type' => ['required', 'integer', Rule::in(array_keys(Candidate::EXPERIENCE_TYPES))],
            'proff_id' => [
                'required', 'integer', 'exists:professions,id',
                Rule::unique('partner_prices', 'proff_id')->where(fn ($q) => $q
                    ->where('partner_id', $partnerId)
                    ->where('exp_type', (int) $request->input('exp_type'))),
            ],
        ] + $this->valueRules(), [
            'proff_id.unique' => __('locale.A price for this Experience Type and Profession already exists. Edit it below.'),
        ], $this->attributeNames());

        try {
            PartnerPrice::create($data + ['partner_id' => $partnerId]);
        } catch (QueryException $e) {
            // Unique index backstop for a concurrent duplicate submit.
            if (($e->errorInfo[1] ?? null) === 1062) {
                return back()->withInput()->withErrors(['proff_id' => __('locale.A price for this Experience Type and Profession already exists. Edit it below.')]);
            }
            throw $e;
        }

        return redirect()->route('worker.partner.prices')->with('success', __('locale.Price added successfully.'));
    }

    public function update(Request $request, $id)
    {
        $price = PartnerPrice::where('partner_id', $this->partnerId())->findOrFail($id);

        $price->update($request->validate($this->valueRules(), [], $this->attributeNames()));

        return redirect()->route('worker.partner.prices')->with('success', __('locale.Price updated successfully.'));
    }

    public function destroy($id)
    {
        PartnerPrice::where('partner_id', $this->partnerId())->findOrFail($id)->delete();

        return redirect()->route('worker.partner.prices')->with('success', __('locale.Price removed. The default price applies again.'));
    }

    private function attributeNames(): array
    {
        return [
            'exp_type' => __('locale.Experience Type'),
            'proff_id' => __('locale.Profession'),
            'cost' => __('locale.Service Price'),
            'days' => __('locale.Departure Days'),
        ];
    }

    private function valueRules(): array
    {
        return PartnerPrice::VALUE_RULES;
    }
}
