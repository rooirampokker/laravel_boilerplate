<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected string $newUserFirstName;
    protected string $newUserLastName;

    protected string $password;
    protected User $user;
    protected User $superAdmin;
    protected Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setup();
    }

    /**
     * @return void
     */
    protected function seedDatabase()
    {
        $this->password         = '1234';
        $this->newUserFirstName = $this->faker->firstName();
        $this->newUserLastName  = $this->faker->lastName();

        $this->artisan("passport:install");
        $this->artisan('db:seed');

        $this->superAdmin = User::factory()->create();
        $this->superAdminRole = Role::findByName('super-admin', 'api');

        $this->superAdmin->assignRole($this->superAdminRole);

        $this->user = User::factory()->create();
        $userRole   = Role::findByName('user', 'api');
        $this->user->assignRole($userRole);
    }

    protected function createUserWithAdditionalData($email = null)
    {
        $email = $email ? $email : $this->faker->email();
        $response = $this->actingAs($this->superAdmin, 'api')->postJson('api/users', [
            'email' => $email,
            'password' => $this->password,
            'c_password' => $this->password,
            'data' => [
                'first_name' => $this->newUserFirstName,
                'last_name' => $this->newUserLastName
            ]
        ]);

        return $response;
    }
}
