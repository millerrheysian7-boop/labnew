<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_can_view_dashboard_only(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        foreach (['products.index', 'brands.index', 'categories.index', 'users.index', 'trash.index'] as $route) {
            $this->actingAs($user)
                ->get(route($route))
                ->assertRedirect(route('dashboard', absolute: false));
        }

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertDontSee('href="'.route('products.index', absolute: false).'"', false);
    }

    public function test_user_role_cannot_add_records(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('products.store'), [])
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_and_staff_can_still_manage_products(): void
    {
        foreach (['admin', 'staff'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('products.index'))
                ->assertOk();
        }
    }
}
