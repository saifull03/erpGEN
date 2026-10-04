<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_index_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/members');

        $response->assertStatus(200);
        $response->assertSee('Membership Management');
        $response->assertSee('Register New Membership');
    }

    public function test_a_user_can_register_a_new_member_and_auto_create_customer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/members', [
            'name' => 'Alice Rahman',
            'phone' => '+8801711223344',
            'email' => 'alice@example.com',
            'address' => 'Dhaka, Bangladesh',
        ]);

        $response->assertRedirect('/members');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'Alice Rahman',
            'phone' => '+8801711223344',
            'email' => 'alice@example.com',
        ]);

        $customer = Customer::where('email', 'alice@example.com')->first();
        $this->assertNotNull($customer);

        $this->assertDatabaseHas('members', [
            'name' => 'Alice Rahman',
            'customer_id' => $customer->id,
            'phone' => '+8801711223344',
            'email' => 'alice@example.com',
            'status' => 'active',
        ]);

        $member = Member::where('customer_id', $customer->id)->first();
        $this->assertNotNull($member->membership_id);
        $this->assertNotNull($member->member_number);
    }

    public function test_a_user_can_register_a_member_linked_to_an_existing_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_id' => 'CUS-TEST-01',
            'name' => 'Bob Smith',
            'phone' => '+8801811223344',
            'email' => 'bob@example.com',
        ]);
        $tier = MembershipType::create([
            'name' => 'Gold Tier',
            'discount_percentage' => 10,
            'reward_points' => 5,
            'minimum_purchase' => 1000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/members', [
            'customer_id' => (string) $customer->id,
            'membership_type_id' => $tier->id,
            'membership_id' => 'MEM-GOLD-001',
            'member_number' => 'M-8899',
            'name' => 'Bob Smith',
        ]);

        $response->assertRedirect('/members');

        $this->assertDatabaseHas('members', [
            'customer_id' => $customer->id,
            'membership_type_id' => $tier->id,
            'membership_id' => 'MEM-GOLD-001',
            'member_number' => 'M-8899',
            'name' => 'Bob Smith',
        ]);
    }

    public function test_a_user_can_quick_register_a_member_via_ajax_from_pos(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson('/members', [
                'name' => 'POS Fast Customer',
                'phone' => '+8801900000000',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('member.name', 'POS Fast Customer');

        $this->assertDatabaseHas('members', [
            'name' => 'POS Fast Customer',
            'phone' => '+8801900000000',
        ]);
    }

    public function test_a_user_can_update_member_details(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_id' => 'CUS-0099',
            'name' => 'Old Name',
        ]);
        $tier = MembershipType::create([
            'name' => 'Standard',
            'is_active' => true,
        ]);
        $member = Member::create([
            'membership_id' => 'MEM-0099',
            'member_number' => 'M-0099',
            'customer_id' => $customer->id,
            'membership_type_id' => $tier->id,
            'name' => 'Old Name',
            'join_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->put("/members/{$member->id}", [
            'membership_id' => 'MEM-0099',
            'member_number' => 'M-0099',
            'membership_type_id' => $tier->id,
            'name' => 'Updated Member Name',
            'phone' => '+8801555555555',
            'email' => 'updated@example.com',
            'join_date' => now()->toDateString(),
            'status' => 'active',
            'points' => 150,
        ]);

        $response->assertRedirect('/members');

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'name' => 'Updated Member Name',
            'phone' => '+8801555555555',
            'email' => 'updated@example.com',
            'points' => 150,
        ]);
    }

    public function test_a_user_can_delete_a_member(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['customer_id' => 'CUS-DEL-01', 'name' => 'Delete Me']);
        $tier = MembershipType::create(['name' => 'Standard', 'is_active' => true]);
        $member = Member::create([
            'membership_id' => 'MEM-DEL',
            'member_number' => 'M-DEL',
            'customer_id' => $customer->id,
            'membership_type_id' => $tier->id,
            'name' => 'Delete Me',
            'join_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->delete("/members/{$member->id}");

        $response->assertRedirect('/members');
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }
}
