<?php

namespace App\Http\Controllers;

use App\Models\Customer as CustomerModel;
use App\Models\Member as MemberModel;
use App\Models\MembershipType as MembershipTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Member extends Controller
{
    private function ensureDefaultMembershipTypes(): void
    {
        if (MembershipTypeModel::query()->count() === 0) {
            MembershipTypeModel::query()->create([
                'name' => 'Standard',
                'discount_percentage' => 0,
                'reward_points' => 1,
                'minimum_purchase' => 0,
                'is_active' => true,
            ]);
            MembershipTypeModel::query()->create([
                'name' => 'Silver',
                'discount_percentage' => 5,
                'reward_points' => 2,
                'minimum_purchase' => 500,
                'is_active' => true,
            ]);
            MembershipTypeModel::query()->create([
                'name' => 'Gold',
                'discount_percentage' => 10,
                'reward_points' => 5,
                'minimum_purchase' => 1000,
                'is_active' => true,
            ]);
            MembershipTypeModel::query()->create([
                'name' => 'Platinum',
                'discount_percentage' => 15,
                'reward_points' => 10,
                'minimum_purchase' => 2500,
                'is_active' => true,
            ]);
        }
    }

    public function index(Request $request)
    {
        $this->ensureDefaultMembershipTypes();

        $query = MemberModel::query()->with(['customer', 'membershipType']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('membership_id', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($tierId = $request->input('membership_type_id')) {
            $query->where('membership_type_id', $tierId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $members = $query->latest()->paginate(15)->withQueryString();
        $membershipTypes = MembershipTypeModel::query()->where('is_active', true)->get();
        $customers = CustomerModel::query()->where('status', 'active')->orderBy('name')->get();

        return view('members.index', [
            'members' => $members,
            'membershipTypes' => $membershipTypes,
            'customers' => $customers,
            'selectedCustomerId' => $request->input('customer_id'),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureDefaultMembershipTypes();

        $validated = $request->validate([
            'membership_id' => ['nullable', 'string', 'max:50', 'unique:members,membership_id'],
            'member_number' => ['nullable', 'string', 'max:50', 'unique:members,member_number'],
            'customer_id' => ['nullable', 'string'],
            'membership_type_id' => ['nullable', 'exists:membership_types,id'],
            'name' => ['required_without:customer_id', 'nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'join_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'points' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive,suspended'],
        ]);

        // Customer resolution / creation
        $customer = null;
        if (! empty($validated['customer_id']) && $validated['customer_id'] !== 'new') {
            $customer = CustomerModel::query()->find($validated['customer_id']);
        }

        $name = $validated['name'] ?? ($customer ? $customer->name : null);
        if (! $name) {
            return back()->withErrors(['name' => 'Member or Customer name is required.'])->withInput();
        }

        $phone = $validated['phone'] ?? $customer?->phone;
        $email = $validated['email'] ?? $customer?->email;
        $address = $validated['address'] ?? $customer?->address;
        $dob = $validated['date_of_birth'] ?? $customer?->date_of_birth;

        if (! $customer) {
            do {
                $customerIdStr = 'CUS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (CustomerModel::query()->where('customer_id', $customerIdStr)->exists());

            $customer = CustomerModel::query()->create([
                'customer_id' => $customerIdStr,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'date_of_birth' => $dob,
                'status' => 'active',
            ]);
        }

        // Membership Type resolution
        $membershipTypeId = $validated['membership_type_id'] ?? null;
        if (! $membershipTypeId) {
            $defaultType = MembershipTypeModel::query()->where('is_active', true)->first();
            $membershipTypeId = $defaultType ? $defaultType->id : 1;
        }

        // Auto-generate Membership ID if not provided
        $membershipId = $validated['membership_id'] ?? null;
        if (empty($membershipId)) {
            $nextId = (MemberModel::query()->max('id') ?? 0) + 1;
            do {
                $membershipId = 'MEM-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                $nextId++;
            } while (MemberModel::query()->where('membership_id', $membershipId)->exists());
        }

        // Auto-generate Member Number if not provided
        $memberNumber = $validated['member_number'] ?? null;
        if (empty($memberNumber)) {
            $nextNum = 1000 + (MemberModel::query()->max('id') ?? 0) + 1;
            do {
                $memberNumber = 'M-' . $nextNum;
                $nextNum++;
            } while (MemberModel::query()->where('member_number', $memberNumber)->exists());
        }

        $joinDate = ! empty($validated['join_date']) ? $validated['join_date'] : now()->toDateString();
        $expiryDate = ! empty($validated['expiry_date']) ? $validated['expiry_date'] : null;
        $points = isset($validated['points']) ? (int) $validated['points'] : 0;
        $status = $validated['status'] ?? 'active';

        $member = MemberModel::query()->create([
            'membership_id' => $membershipId,
            'member_number' => $memberNumber,
            'customer_id' => $customer->id,
            'membership_type_id' => $membershipTypeId,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'date_of_birth' => $dob,
            'join_date' => $joinDate,
            'expiry_date' => $expiryDate,
            'points' => $points,
            'status' => $status,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Member registered successfully with Member #{$member->member_number}",
                'member' => $member->load('membershipType', 'customer'),
            ]);
        }

        return redirect()->route('members.index')->with('success', "Member registered successfully! Membership ID: {$member->membership_id}, Member #: {$member->member_number}");
    }

    public function update(Request $request, MemberModel $member)
    {
        $validated = $request->validate([
            'membership_id' => ['required', 'string', 'max:50', 'unique:members,membership_id,' . $member->id],
            'member_number' => ['required', 'string', 'max:50', 'unique:members,member_number,' . $member->id],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'membership_type_id' => ['required', 'exists:membership_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'join_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'points' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive,suspended'],
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', "Member {$member->member_number} updated successfully.");
    }

    public function destroy(MemberModel $member)
    {
        $memberNumber = $member->member_number;
        $member->delete();

        return redirect()->route('members.index')->with('success', "Member {$memberNumber} deleted successfully.");
    }

    public function card(MemberModel $member)
    {
        $member->load(['customer', 'membershipType']);

        return view('members.card', [
            'member' => $member,
        ]);
    }

    public function pointHistory(MemberModel $member)
    {
        $member->load(['customer', 'membershipType']);
        $pointLogs = $member->pointLogs()->with('user')->latest()->paginate(20);

        return view('members.points', [
            'member' => $member,
            'pointLogs' => $pointLogs,
        ]);
    }
}
