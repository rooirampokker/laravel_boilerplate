<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $email,
        $password,
        $user,
        $superAdmin,
        $superAdminRole;
    protected function setUp(): void
    {
        parent::setup();
    }

    /**
     * @return void
     */
    protected function seedDatabase() {
        $this->password = '1234';
        $this->artisan("passport:install");
        $this->artisan('db:seed');

        $this->superAdmin = User::factory()->create();
        $this->superAdminRole = Role::findByName('super-admin', 'api');

        $this->superAdmin->assignRole($this->superAdminRole);

        $this->user = User::factory()->create();
        $userRole   = Role::findByName('user', 'api');
        $this->user->assignRole($userRole);
    }
}
