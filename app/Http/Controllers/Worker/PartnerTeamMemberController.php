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
        return view('worker.partner.team-members.form', ['member' => new PartnerTeamMember(['status' => true, 'country_code' => '966'])]);
    }

    public function store(Request $request)
    {
        // Added without permissions (nothing granted); they are set afterwards
        // on the member's Permission page.
        $member = new PartnerTeamMember(['partner_id' => $this->partnerId(), 'permissions' => []]);

        return $this->save($request, $member);
    }

    public function show($id)
    {
        return view('worker.partner.team-members.show', ['member' => $this->member($id)]);
    }

    public function edit($id)
    {
        return view('worker.partner.team-members.form', ['member' => $this->member($id)]);
    }

    /**
     * Team Members -> ⋮ -> Permission: the member's permissions on their own
     * page (same modules/actions/sections UI and the same stored JSON as
     * before). Only this partner's own members (member() -> 404 otherwise).
     */
    public function permissions($id)
    {
        return view('worker.partner.team-members.permission-edit', ['member' => $this->member($id), 'modules' => PartnerTeam::modules()]);
    }

    public function updatePermissions(Request $request, $id)
    {
        $member = $this->member($id);
        $request->validate(['permissions' => ['nullable', 'array']]);

        // Same rules as before: only real modules/actions/sections, any action implies view.
        $member->permissions = PartnerTeam::sanitize($request->input('permissions', []));
        $member->save();

        return redirect()->route('worker.partner.team-members.permissions', $member->id)
            ->with('success', __('locale.Team member permissions updated.'));
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
     * least one way to sign in is required. Permissions are not part of this
     * form - saved permissions are left exactly as they are
     * (updatePermissions() is the only place that changes them).
     */
    private function save(Request $request, PartnerTeamMember $member)
    {
        $id = $member->id;
        [$validator, $code, $local] = PartnerTeam::identityValidator($request, $id, [
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'status' => ['nullable', 'in:0,1'],
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
        ]);
        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }
        $member->partner_id = $this->partnerId();   // never from the request
        $member->save();

        // New member -> the listing, where ⋮ -> Permission sets what they can do.
        return ($id
            ? redirect()->route('worker.partner.team-members.show', $member->id)
            : redirect()->route('worker.partner.team-members'))
            ->with('success', $id ? __('locale.Team member updated.') : __('locale.Team member added.'));
    }
}
