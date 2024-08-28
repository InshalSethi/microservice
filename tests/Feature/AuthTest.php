<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Role;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the admin role with permissions
        $permissionsArray = explode(',', "DASHBOARD_FILTERS,DASHBOARD_DATA,PRODUCT_VIEW,PRODUCT_ADD,PRODUCT_EDIT,PRODUCT_DELETE,ADMIN_VIEW,ADMIN_ADD,ADMIN_EDIT,ADMIN_DELETE,ADMIN_SITE_SETTINGS,ADMIN_SECURITY,ADMIN_PROFILE_UPDATE,ROLE_VIEW,ROLE_ADD,ROLE_EDIT,ROLE_DELETE,ROLE_PERMISSIONS,PERMISSION_VIEW,PERMISSION_ADD,PERMISSION_EDIT,PERMISSION_DELETE,PERMISSION_UPDATE,SITE_SETTINGS_VIEW,SITE_SETTINGS_ADD,SITE_SETTINGS_EDIT,SITE_SETTINGS_DELETE");

        Role::create([
            'role_name' => 'admin',
            'role_guard' => 'admin',
            'permissions' => $permissionsArray
        ]);
    }

    /**
     * Test admin registration.
     */
    public function test_admin_can_register(): void
    {
        // Arrange: prepare the request data
        $data = [
            'name' => 'Test User',
            'email' => 'testuser8@example.com', // Dynamic unique email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act: make a POST request to the register endpoint
        $response = $this->postJson('/api/admin/register', $data);

        // Assert: check the response status and the structure
        $response->assertStatus(200) // Updated to 200 instead of 201
                ->assertJsonStructure([
                    'userAbilityRules',
                    'accessToken',
                    'userData' => [
                        'id',
                        'fullName',
                        'email',
                        'role',
                        'avatar',
                    ],
                    'permissions'
                ]);

        // Additional Assertions: check if the user was created
        $this->assertDatabaseHas('admins', [
            'email' => $data['email']
        ]);
    }


    /**
     * Test admin login.
     */
    public function test_admin_can_login(): void
    {
        // Arrange: create a role and an admin user
        $role = Role::where('role_name', 'admin')->firstOrFail();

        $admin = Admin::create([
            'name' => 'Test User',
            'email' => 'testuser8@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);

        // Act: make a POST request to the login endpoint
        $response = $this->postJson('/api/admin/login', [
            'email' => 'testuser8@example.com',
            'password' => 'password123',
        ]);

        // Assert: check the response status and the structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'userAbilityRules',
                     'accessToken',
                     'userData' => [
                         'id',
                         'fullName',
                         'username',
                         'avatar',
                         'email',
                         'role',
                     ],
                     'permissions'
                 ]);
    }

    /**
     * Test admin cannot register with invalid data.
     */
    public function test_admin_registration_fails_with_invalid_data(): void
    {
        // Act: make a POST request to the register endpoint with invalid data
        $response = $this->postJson('/api/admin/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'pass',
            'password_confirmation' => 'not-matching'
        ]);

        // Assert: check the response status and error messages
        $response->assertStatus(422)
                ->assertJson([
                    'name' => ['The name field is required.'],
                    'email' => ['The email field must be a valid email address.'],
                    'password' => [
                        'The password field confirmation does not match.',
                        'The password field must be at least 8 characters.'
                    ]
                ]);
    }



    /**
     * Test admin cannot login with incorrect credentials.
     */
    public function test_admin_login_fails_with_incorrect_credentials(): void
    {
        // Arrange: create a role and an admin user
        $role = Role::where('role_name', 'admin')->firstOrFail();

        $admin = Admin::create([
            'name' => 'Test User',
            'email' => 'testuser8@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);

        // Act: make a POST request to the login endpoint with incorrect credentials
        $response = $this->postJson('/api/admin/login', [
            'email' => 'testuser8@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert: check the response status and error message
        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Invalid email or password'
                 ]);
    }
}
