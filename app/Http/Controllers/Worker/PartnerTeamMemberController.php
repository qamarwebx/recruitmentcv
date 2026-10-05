<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerTeamMember;
use App\Support\PartnerTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Partner Portal -> Team Members (partner owner only: the routes are
 * owner-only in App\Support\PartnerTeam, so a team member can never reach
 * them). Every query is scoped to the signed-in partner; ids from the
 * request are only ever looked up inside that partner's own members.
 */
class PartnerTeamMemberController extends Controller
{
    private function partnerId(): int
    {
        return (int) Auth::guard('partner')->id();
    }

    private function members()
    {
        return PartnerTeamMember::where('partner_id', $this->partnerId());
    }

    private function member($id): PartnerTeamMember
    {
        return $this->members()->findOrFail((int) $id);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $members = $this->members()
            ->when($search !== '', function ($q) use ($search) {
                $term = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('full_name', 'like', $term)->orWhere('username', 'like', $term)->orWhere('email', 'like', $term)->orWhere('mobile', 'like', $term));
            })
            ->when(in_array($request->query('status'), ['0', '1'], true), fn ($q) => $q->where('status', (int) $request->query('status')))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('worker.partner.team-members.index', compact('members', 'search'));
    }

    public function create()
    {
        return view('worker.partner.team-members.form', ['member' => new PartnerTeamMember(['status' => true, 'country_code' => '966']), 'modules' => PartnerTeam::modules()]);
    }

    public function store(Request $request)
    {
        $member = new PartnerTeamMember(['partner_id' => $this->partnerId()]);

        return $this->save($request, $member);
    }

    public function show($id)
    {
        return view('worker.partner.team-members.show', ['member' => $this->member($id), 'modules' => PartnerTeam::modules()]);
    }

    public function edit($id)
    {
        return view('worker.partner.team-members.form', ['member' => $this->member($id), 'modules' => PartnerTeam::modules()]);
    }

    public function update(Request $request, $id)
    {
        return $this->save($request, $this->member($id));
    }

    public function destroy($id)
    {
        $this->member($id)->delete();

        return redirect()->route('worker.partner.team-members')->with('success', __('locale.Team member deleted.'));
    }

    /**
     * Create / update. Identifiers must be unique across partners AND team
     * members (the Partner Login resolves one account per identifier); at
     * least one way to sign in is required.
     */
    private function save(Request $request, PartnerTeamMember $member)
    {
        $id = $member->id;
        [$validator, $code, $local] = PartnerTeam::identityValidator($request, $id, [
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'status' => ['nullable', 'in:0,1'],
            'permissions' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        $member->fill([
            'full_name' => trim($request->full_name),
            'username' => $request->username,
            'email' => $request->email,
            'country_code' => $local ? $code : null,
            'mobile' => $local ?: null,
            'status' => $request->input('status', '1') === '1',
            'permissions' => PartnerTeam::sanitize($request->input('permissions', [])),
        ]);
        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }
        $member->partner_id = $this->partnerId();   // never from the request
        $member->save();

        return redirect()->route('worker.partner.team-members.show', $member->id)
            ->with('success', $id ? __('locale.Team member updated.') : __('locale.Team member added.'));
    }
}
