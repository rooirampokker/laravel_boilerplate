<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected string $newUserFirstName;
    protected string $newUserLastName;

    protected string $password;
    protected User $user;
    protected User $admin;
    protected Role $adminRole;
    public $mockConsoleOutput = false;
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

        $this->artisan('passport:install', ['--no-interaction' => true]);
        $this->artisan('db:seed');

        $this->admin = User::factory()->create();
        $this->adminRole = Role::findByName('admin', 'api');

        $this->admin->assignRole($this->adminRole);

        $this->user = User::factory()->create();
        $userRole   = Role::findByName('manager', 'api');
        $this->user->assignRole($userRole);
    }

    /**
     * @param $message
     * @return void
     */
    protected function apiResponse($success, $code, $message)
    {
        return [
            'success' => $success,
            'code' => $code,
            'message' => $message,
        ];
    }
    /**
     * @param $email
     * @return \Illuminate\Testing\TestResponse
     */
    protected function createUserWithAdditionalData($email = null)
    {
        $email = $email ? $email : $this->faker->email();
        $response = $this->actingAs($this->admin, 'api')->postJson('api/users', [
            'email' => $email,
            'password' => $this->password,
            'c_password' => $this->password,
            'roles' => [1],
            'data' => [
                'first_name' => $this->newUserFirstName,
                'last_name' => $this->newUserLastName
            ]
        ]);

        return $response;
    }
}
