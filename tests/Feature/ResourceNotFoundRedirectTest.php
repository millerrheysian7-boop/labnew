<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceNotFoundRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_product_redirects_to_the_products_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/products/not-a-real-product');

        $response->assertRedirect(route('products.index', absolute: false));
    }

    public function test_missing_brand_redirects_to_the_brands_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/brands/not-a-real-brand');

        $response->assertRedirect(route('brands.index', absolute: false));
    }

    public function test_missing_category_redirects_to_the_categories_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/categories/not-a-real-category');

        $response->assertRedirect(route('categories.index', absolute: false));
    }

    public function test_missing_user_redirects_to_the_users_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/users/not-a-real-user');

        $response->assertRedirect(route('users.index', absolute: false));
    }

    public function test_random_words_on_products_url_redirect_to_the_products_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/products/random/extra/words');

        $response->assertRedirect(route('products.index', absolute: false));
    }

    public function test_random_words_on_each_section_url_redirect_to_its_own_dashboard(): void
    {
        $sections = [
            '/dashboard/random/words' => 'dashboard',
            '/brands/random/words' => 'brands.index',
            '/categories/random/words' => 'categories.index',
            '/users/random/words' => 'users.index',
            '/trash/random/words' => 'trash.index',
        ];

        foreach ($sections as $url => $route) {
            $response = $this->actingAs(User::factory()->create())->get($url);

            $response->assertRedirect(route($route, absolute: false));
        }
    }

    public function test_unknown_section_redirects_to_the_main_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/random/words');

        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
