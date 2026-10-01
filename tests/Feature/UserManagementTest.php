<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('hrd.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_hrd_can_view_user_management_page(): void
    {
        $hrd = User::where('role', 'hrd')->first();

        $response = $this->actingAs($hrd)->get(route('hrd.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Tim HRD & Interviewer');
        $response->assertSee('Daftar Akun Tim HRD');
        $response->assertSee($hrd->name);
    }

    public function test_hrd_can_create_new_hrd_user(): void
    {
        $hrd = User::where('role', 'hrd')->first();

        $response = $this->actingAs($hrd)->post(route('hrd.users.store'), [
            'name' => 'HRD Interviewer 2',
            'email' => 'hrd2@example.com',
            'password' => 'Password123!',
            'role' => 'hrd',
            'phone' => '081299998888',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('hrd.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'HRD Interviewer 2',
            'email' => 'hrd2@example.com',
            'role' => 'hrd',
            'phone' => '081299998888',
            'is_active' => true,
        ]);

        $createdUser = User::where('email', 'hrd2@example.com')->first();
        $this->assertTrue(Hash::check('Password123!', $createdUser->password));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CREATE_USER',
            'user_id' => $hrd->id,
        ]);
    }

    public function test_hrd_can_update_user_details(): void
    {
        $hrd = User::where('role', 'hrd')->first();

        $targetUser = User::create([
            'name' => 'Staff Tester',
            'email' => 'staff@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'hrd',
            'phone' => '0811111111',
            'is_active' => true,
        ]);

        $response = $this->actingAs($hrd)->put(route('hrd.users.update', $targetUser->id), [
            'name' => 'Staff Senior Tester',
            'email' => 'staff_senior@example.com',
            'role' => 'hrd',
            'phone' => '0822222222',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('hrd.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Staff Senior Tester',
            'email' => 'staff_senior@example.com',
            'phone' => '0822222222',
        ]);
    }

    public function test_hrd_cannot_delete_own_account(): void
    {
        $hrd = User::where('role', 'hrd')->first();

        $response = $this->actingAs($hrd)->delete(route('hrd.users.destroy', $hrd->id));

        $response->assertRedirect(route('hrd.users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $hrd->id,
            'email' => $hrd->email,
        ]);
    }

    public function test_hrd_cannot_deactivate_own_account(): void
    {
        $hrd = User::where('role', 'hrd')->first();

        $response = $this->actingAs($hrd)->put(route('hrd.users.update', $hrd->id), [
            'name' => $hrd->name,
            'email' => $hrd->email,
            'role' => 'hrd',
            'phone' => $hrd->phone,
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('hrd.users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $hrd->id,
            'is_active' => true,
        ]);
    }

    public function test_hrd_can_delete_other_user(): void
    {
        $hrd = User::where('role', 'hrd')->first();

        $otherUser = User::create([
            'name' => 'User To Delete',
            'email' => 'todelete@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'hrd',
            'phone' => '0833333333',
            'is_active' => true,
        ]);

        $response = $this->actingAs($hrd)->delete(route('hrd.users.destroy', $otherUser->id));

        $response->assertRedirect(route('hrd.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $otherUser->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'DELETE_USER',
            'user_id' => $hrd->id,
        ]);
    }
}
