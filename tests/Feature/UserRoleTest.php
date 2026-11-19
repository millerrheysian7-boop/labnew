<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_form_offers_admin_staff_and_user_roles(): void
    {
        $admin = $this->user('admin');
        $user = $this->user('user');

        $this->actingAs($admin)
            ->get(route('users.edit', $user))
            ->assertSee('admin')
            ->assertSee('staff')
            ->assertSee('user');
    }

    public function test_admin_can_change_a_user_role(): void
    {
        $admin = $this->user('admin');
        $user = $this->user('user');

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'staff',
            ])
            ->assertRedirect(route('users.index', absolute: false));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'staff']);
    }

    public function test_non_admin_cannot_change_a_role(): void
    {
        $actor = $this->user('user');
        $user = $this->user('user');

        $this->actingAs($actor)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'admin',
            ]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'user']);
    }

    public function test_admin_can_create_a_user_with_a_selected_role(): void
    {
        $admin = $this->user('admin');
        $email = 'new-'.Str::random(5).'@example.com';

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'New User',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'staff',
            ])
            ->assertRedirect(route('users.index', absolute: false));

        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'staff']);
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'password' => Hash::make('password123'),
        ]);
    }
}
