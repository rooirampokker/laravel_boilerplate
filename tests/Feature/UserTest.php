<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Tests\TestCase;

use App\Models\User;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  protected $email,
    $password,
    $user,
    $superAdmin,
    $user,
    $superAdminRole;

  /**
   *
   */
    public function setUp() :void {
    parent::setUp();

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

    protected function login($email, $password) {
      $response = $this->postJson('/api/user/login', [
          'email' => $email,
          'password' => $password
      ]);

      return $response;
  }
    /**
     * LOGIN SUCCESS
     */
    public function testSuccessfulLogin() {
        $response = $this->login($this->superAdmin->email, $this->password);

        $response->assertStatus(200);
    }
    /**
     * LOGIN FAILURE
     */
    public function testFailedLogin() {
        $response = $this->login('user_does_not_exist@email.com', $this->password);

        $response->assertStatus(401);
    }
    /**
     * SUPER ADMIN CAN CREATE NEW USER
     */
    public function testSuperAdminCanCreateUser() {
      $response = $this->actingAs($this->superAdmin, 'api')->postJson('api/user', [
          'email' => $this->faker->email(),
          'password' => $this->password,
          'c_password' => $this->password
      ]);

      $response->assertJsonStructure([
          'success' => [
              'token',
              'id',
              'email'
          ]]);
  }
    /**
     * SUPER ADMIN CAN DELETE NEW USER
     */
    public function testSuperAdminCanDeleteUser() {
        $response = $this->actingAs($this->superAdmin, 'api')->deleteJson('api/user/2');

        $response->assertStatus(200);
    }
    /**
     * SUPER ADMIN CAN RESTORE A DELETED USER
     */
    public function testSuperAdminCanRestoreUser() {
        //DELETE FIRST, THEN RESTORE
        $deleteResponse  = $this->actingAs($this->superAdmin, 'api')->deleteJson('api/user/2');
        $restoreResponse = $this->actingAs($this->superAdmin, 'api')->putJson('api/user/2/restore');

        $restoreResponse->assertStatus(200);
    }

    /**
     * USER CAN'T CREATE NEW USER
     */
    public function testUserCantCreateUser() {
        $response = $this->actingAs($this->user, 'api')->postJson('api/user', [
            'email' => $this->faker->email(),
            'password' => $this->password,
            'c_password' => $this->password
        ]);

        $response->assertStatus(403);
    }
    /**
     * USER CAN'T DELETE NEW USER
     */
    public function testUserCantDeleteUser() {
        $response = $this->actingAs($this->user, 'api')->deleteJson('api/user/2');

        $response->assertStatus(403);
    }
    /**
     * user CAN'T RESTORE A DELETED USER
     */
    public function testUserCantRestoreUser() {
        //DELETE FIRST, THEN RESTORE
        $deleteResponse  = $this->actingAs($this->user, 'api')->deleteJson('api/user/2');
        $restoreResponse = $this->actingAs($this->user, 'api')->putJson('api/user/2/restore');

        $deleteResponse->assertStatus(403);
        $restoreResponse->assertStatus(403);
    }
    /**
     * USER CAN'T UPDATE SOMEONE ELSE'S PROFILE
     */
    public function testUserCantUpdateOtherProfile() {
        $oldEmail = $this->superAdmin->email;
        $newEmail = $this->faker->email();
        $response = $this->actingAs($this->user, 'api')->putJson('api/user/'.$this->superAdmin->id, [
            'email' => $newEmail,
        ]);

        //return failure - user can't change super-admin profile
        $response->assertStatus(403);
    }
    /**
     * USER CAN UPDATE OWN PROFILE
     */
    public function testUserCanUpdateOwnProfile() {
        $oldEmail = $this->user->email;
        $newEmail = $this->faker->email();
        $response = $this->actingAs($this->user, 'api')->putJson('api/user/'.$this->user->id, [
            'email' => $newEmail,
        ]);
        //return success - user may update their own profile
        $response->assertStatus(200);
    }
}
