<?php

namespace App\Http\Controllers;

use App\Models\Customer as CustomerModel;
use App\Models\Member as MemberModel;
use App\Models\MembershipType as MembershipTypeModel;
use Illuminate\Http\Request;

class Member extends Controller
{
    public function index()
    {
        return view('members.index', ['members' => MemberModel::query()->with(['customer', 'membershipType'])->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_id' => ['required', 'string', 'max:50', 'unique:members,membership_id'],
            'member_number' => ['required', 'string', 'max:50', 'unique:members,member_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'membership_type_id' => ['required', 'exists:membership_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'join_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        MemberModel::query()->create($validated);

        return redirect()->route('members.index')->with('success', 'Member created.');
    }

    public function update(Request $request, MemberModel $member)
    {
        $validated = $request->validate([
            'membership_id' => ['required', 'string', 'max:50', 'unique:members,membership_id,' . $member->id],
            'member_number' => ['required', 'string', 'max:50', 'unique:members,member_number,' . $member->id],
            'customer_id' => ['required', 'exists:customers,id'],
            'membership_type_id' => ['required', 'exists:membership_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'join_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Member updated.');
    }

    public function destroy(MemberModel $member)
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted.');
    }
}
