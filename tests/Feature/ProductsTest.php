<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $permissionsArray = explode(',', "DASHBOARD_FILTERS,DASHBOARD_DATA,PRODUCT_VIEW,PRODUCT_ADD,PRODUCT_EDIT,PRODUCT_DELETE,ADMIN_VIEW,ADMIN_ADD,ADMIN_EDIT,ADMIN_DELETE,ADMIN_SITE_SETTINGS,ADMIN_SECURITY,ADMIN_PROFILE_UPDATE,ROLE_VIEW,ROLE_ADD,ROLE_EDIT,ROLE_DELETE,ROLE_PERMISSIONS,PERMISSION_VIEW,PERMISSION_ADD,PERMISSION_EDIT,PERMISSION_DELETE,PERMISSION_UPDATE,SITE_SETTINGS_VIEW,SITE_SETTINGS_ADD,SITE_SETTINGS_EDIT,SITE_SETTINGS_DELETE");

        Role::create([
            'role_name' => 'admin',
            'role_guard' => 'admin',
            'permissions' => $permissionsArray
        ]);

        // Register an admin user
        $response = $this->registerAdmin();
        $this->token = $response['accessToken'];
        $this->admin = Admin::find($response['userData']['id']);
    }

    private function registerAdmin()
    {
        $data = [
            'name' => 'Test Admin',
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/admin/register', $data);
        $response->assertStatus(200);

        return $response->json();
    }

    public function test_admin_can_register(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser' . $this->faker->unique()->numberBetween(1, 1000) . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/admin/register', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'userAbilityRules' => [
                    '*' => ['action', 'subject']
                ],
                'accessToken',
                'userData' => [
                    'id',
                    'fullName',
                    'avatar',
                    'email',
                    'role'
                ],
                'permissions' => [
                    '*' => [
                        'text',
                        'permissions' => [
                            '*' => ['text', 'ability']
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('admins', [
            'email' => $data['email']
        ]);
    }

    public function test_can_create_product()
    {
        $productData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/admin/products', $productData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success'
            ]);

        $this->assertDatabaseHas('products', $productData);
    }

    public function test_can_get_products_list()
    {
        Product::factory()->count(5)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/admin/products-list');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'draw',
                'recordsTotal',
                'recordsFiltered'
            ]);
    }

    

    public function test_can_get_product_by_id()
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson("/api/admin/product/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => $product->toArray()
            ]);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create();
        $updatedData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/admin/products/{$product->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success'
            ]);

        $this->assertDatabaseHas('products', $updatedData);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/admin/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success'
            ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}