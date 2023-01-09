<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  /**
   *
   */
    public function setUp() :void {
    parent::setUp();
    $this->seedDatabase();
  }

    protected function login($email, $password) {
      $response = $this->postJson('/api/users/login', [
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
      $response = $this->actingAs($this->superAdmin, 'api')->postJson('api/users', [
          'email' => $this->faker->email(),
          'password' => $this->password,
          'c_password' => $this->password
      ]);

      $response->assertJsonStructure([
          'success' => [
              'email',
              'created_at',
              'updated_at',
              'id',
          ]]);
  }
    /**
     * SUPER ADMIN CAN DELETE NEW USER
     */
    public function testSuperAdminCanDeleteUser() {
        $response = $this->actingAs($this->superAdmin, 'api')->deleteJson('api/users/'.$this->user->id);
        $deletedUser = $this->actingAs($this->superAdmin, 'api')->getJson('api/users/'.$this->user->id);

        $response->assertStatus(200);
        $deletedUser->assertStatus(404);
    }
    /**
     * DELETED RECORDS ARE NOT INCLUDED IN INDEX
     */
    public function testIndexDoesNotReturnDeletedUsers() {
        $response1 = $this->actingAs($this->superAdmin, 'api')->GETJson('api/users');
        $totalRecords = count($response1['success']);

        $this->actingAs($this->superAdmin, 'api')->deleteJson('api/users/'.$this->user->id);
        $response2 = $this->actingAs($this->superAdmin, 'api')->GETJson('api/users');

        $this->assertCount( $totalRecords-1, $response2['success']);
    }
    /**
     * ALL RECORDS, INCLUDING DELETED ARE NOT RETURNED WITH INDEXALL
     */
    public function testIndexAllReturnsDeletedUsers() {
        $response1 = $this->actingAs($this->superAdmin, 'api')->GETJson('api/users');
        $totalRecords = count($response1['success']);

        $this->actingAs($this->superAdmin, 'api')->deleteJson('api/users/'.$this->user->id);
        $response2 = $this->actingAs($this->superAdmin, 'api')->GETJson('api/users/all');

        $this->assertCount( $totalRecords, $response2['success']);
    }
    /**
     * ONLY DELETED RECORDS ARE INCLUDED IN INDEXTRASHED
     */
    public function testIndexTrashedDoesReturnDeletedUsers() {
        $this->actingAs($this->superAdmin, 'api')->deleteJson('api/users/'.$this->user->id);
        $response2 = $this->actingAs($this->superAdmin, 'api')->GETJson('api/users/trashed');

        $this->assertCount(1, $response2['success']);
    }
    /**
     * SUPER ADMIN CAN RESTORE A DELETED USER
     */
    public function testSuperAdminCanRestoreUser() {
        //DELETE FIRST, THEN RESTORE
        $deleteResponse  = $this->actingAs($this->superAdmin, 'api')->deleteJson('api/users/'.$this->user->id);
        $restoreResponse = $this->actingAs($this->superAdmin, 'api')->patchJson('api/users/'.$this->user->id);
        $restoreResponse->assertStatus(200);
    }

    /**
     * USER CAN'T CREATE NEW USER
     */
    public function testUserCantCreateUser() {
        $response = $this->actingAs($this->user, 'api')->postJson('api/users', [
            'email' => $this->faker->email(),
            'password' => $this->password,
            'c_password' => $this->password
        ]);

        $response->assertStatus(401);
    }
    /**
     * USER CAN'T DELETE NEW USER
     */
    public function testUserCantDeleteUser() {
        $response = $this->actingAs($this->user, 'api')->deleteJson('api/users/2');

        $response->assertStatus(401);
    }
    /**
     * user CAN'T RESTORE A DELETED USER
     */
    public function testUserCantRestoreUser() {
        //DELETE FIRST, THEN RESTORE
        $deleteResponse  = $this->actingAs($this->user, 'api')->deleteJson('api/users/2');
        $restoreResponse = $this->actingAs($this->user, 'api')->putJson('api/users/2');

        $deleteResponse->assertStatus(401);
        $restoreResponse->assertStatus(401);
    }
    /**
     * USER CAN'T UPDATE SOMEONE ELSE'S PROFILE
     */
    public function testUserCantUpdateOtherProfile() {
        $oldEmail = $this->superAdmin->email;
        $newEmail = $this->faker->email();
        $response = $this->actingAs($this->user, 'api')->putJson('api/users/'.$this->superAdmin->id, [
            'email' => $newEmail,
        ]);

        //return failure - user can't change super-admin profile
        $response->assertStatus(401);
    }
    /**
     * USER CAN UPDATE OWN PROFILE
     */
    public function testUserCanUpdateOwnProfile() {
        $oldEmail = $this->user->email;
        $newEmail = $this->faker->email();
        $response = $this->actingAs($this->user, 'api')->putJson('api/users/'.$this->user->id, [
            'email' => $newEmail,
        ]);

        //return success - user may update their own profile
        $response->assertStatus(200);
    }
}
