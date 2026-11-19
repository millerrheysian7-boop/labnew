<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_moved_to_trash_and_restored(): void
    {
        $user = User::factory()->create();
        $product = $this->product();

        $this->actingAs($user)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->actingAs($user)
            ->get(route('trash.index'))
            ->assertSee($product->name);

        $this->actingAs($user)
            ->post(route('trash.restore', ['type' => 'products', 'id' => $product->id]))
            ->assertRedirect(route('trash.index', absolute: false));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    public function test_product_can_be_permanently_deleted_from_trash(): void
    {
        $user = User::factory()->create();
        $product = $this->product();

        $this->actingAs($user)->delete(route('products.destroy', $product));

        $this->actingAs($user)
            ->delete(route('trash.force-delete', ['type' => 'products', 'id' => $product->id]))
            ->assertRedirect(route('trash.index', absolute: false));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_brands_and_categories_cannot_be_deleted_or_restored_from_trash(): void
    {
        $user = User::factory()->create();
        $brand = Brand::create(['name' => 'Test Brand '.Str::random(4)]);
        $category = Category::create(['name' => 'Test Category '.Str::random(4)]);

        $this->actingAs($user)
            ->delete(route('brands.destroy', $brand))
            ->assertSessionHas('error');

        $this->actingAs($user)
            ->delete(route('categories.destroy', $category))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);

        $this->actingAs($user)
            ->post(route('trash.restore', ['type' => 'brands', 'id' => $brand->id]))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('trash.restore', ['type' => 'categories', 'id' => $category->id]))
            ->assertNotFound();
    }

    public function test_admin_can_restore_a_deleted_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->delete(route('users.destroy', $user));

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $this->actingAs($admin)
            ->get(route('trash.index'))
            ->assertSee($user->email);

        $this->actingAs($admin)
            ->post(route('trash.restore', ['type' => 'users', 'id' => $user->id]))
            ->assertRedirect(route('trash.index', absolute: false));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    public function test_admin_can_permanently_delete_a_deleted_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->delete(route('users.destroy', $user));

        $this->actingAs($admin)
            ->delete(route('trash.force-delete', ['type' => 'users', 'id' => $user->id]))
            ->assertRedirect(route('trash.index', absolute: false));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_admin_cannot_restore_or_permanently_delete_users(): void
    {
        $actor = User::factory()->create(['role' => 'user']);
        $user = User::factory()->create(['role' => 'user']);
        $user->delete();

        $this->actingAs($actor)
            ->post(route('trash.restore', ['type' => 'users', 'id' => $user->id]))
            ->assertForbidden();

        $this->actingAs($actor)
            ->delete(route('trash.force-delete', ['type' => 'users', 'id' => $user->id]))
            ->assertForbidden();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Test Product',
            'sku' => 'TEST-'.Str::random(6),
            'category' => 'Tools',
            'quantity' => 5,
            'reorder_level' => 2,
            'unit_price' => 10,
        ], $attributes));
    }
}
