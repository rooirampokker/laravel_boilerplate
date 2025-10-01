<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

  /**
   *
   */
    public function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

    protected function login($email, $password)
    {
        $response = $this->postJson($this->apiVersion . 'users/login', [
          'email' => $email,
          'password' => $password
        ]);

        return $response;
    }
    /**
     * LOGIN SUCCESS
     */
    public function testSuccessfulLogin()
    {
        $response = $this->login($this->admin->email, $this->password);

        $response->assertStatus(200);
    }
    /**
     * LOGIN FAILURE
     */
    public function testFailedLogin()
    {
        $response = $this->login('user_does_not_exist@email.com', $this->password);

        $response->assertStatus(401);
    }
    /**
     * SUPER ADMIN CAN CREATE NEW USER
     */
    public function testSuperAdminCanCreateUserWithAdditionalData()
    {
        $response = $this->createUserWithAdditionalData();

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.store.success'),
        ));
    }
    /**
     * SUPER ADMIN CAN DELETE NEW USER
     */
    public function testSuperAdminCanDeleteUser()
    {
        $response = $this->actingAs($this->admin, 'api')->deleteJson($this->apiVersion . 'users/' . $this->user->id);
        $deletedUser = $this->actingAs($this->admin, 'api')->getJson($this->apiVersion . 'users/' . $this->user->id);

        $response->assertStatus(200);
        $deletedUser->assertStatus(404);
    }
    /**
     * DELETED RECORDS ARE NOT INCLUDED IN INDEX
     */
    public function testIndexDoesNotReturnDeletedUsers()
    {
        $this->actingAs($this->admin, 'api')->deleteJson($this->apiVersion . 'users/' . $this->user->id);
        $response    = $this->actingAs($this->admin, 'api')->GETJson($this->apiVersion . 'users');
        $userIdArray = array_column($response['data']['users'], 'id');

        $response->assertStatus(200);
        $this->assertNotContains($this->user->id, $userIdArray);
    }
    /**
     * ALL RECORDS, INCLUDING DELETED ARE NOT RETURNED WITH INDEXALL
     */
    public function testIndexAllReturnsDeletedUsers()
    {
        $this->actingAs($this->admin, 'api')->deleteJson($this->apiVersion . 'users/' . $this->user->id);

        $response    = $this->actingAs($this->admin, 'api')->getJson($this->apiVersion . 'users/all');
        $userIdArray = array_column($response['data']['users'], 'id');

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.index.success')
        ));
        $this->assertContains($this->user->id, $userIdArray);
    }
    /**
     * ONLY DELETED RECORDS ARE INCLUDED IN INDEXTRASHED
     */
    public function testIndexTrashedDoesReturnDeletedUsers()
    {
        $this->actingAs($this->admin, 'api')->deleteJson($this->apiVersion . 'users/' . $this->user->id);
        $response = $this->actingAs($this->admin, 'api')->getJson($this->apiVersion . 'users/trashed');
        $userIdArray = array_column($response['data']['users'], 'id');

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.index.success', ['id' => $this->user->id])
        ));
        $this->assertContains($this->user->id, $userIdArray);
        $this->assertCount(1, $response['data']['users']);
    }
    /**
     * DELETE/PATCH ..api/users/:user_id
     * SUPER ADMIN CAN RESTORE A DELETED USER
     */
    public function testSuperAdminCanRestoreUser()
    {
        //DELETE FIRST, THEN RESTORE
        $this->actingAs($this->admin, 'api')->deleteJson($this->apiVersion . 'users/' . $this->user->id);
        $response = $this->actingAs($this->admin, 'api')->patchJson($this->apiVersion . 'users/' . $this->user->id);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.restore.success', ['id' => $this->user->id])
        ));
    }

    /**
     * POST ..api/users
     * USER CAN'T CREATE NEW USER
     */
    public function testUserCantCreateUser()
    {
        $response = $this->actingAs($this->user, 'api')->postJson($this->apiVersion . 'users', [
            'email' => $this->faker->email(),
            'password' => $this->password,
            'c_password' => $this->password
        ]);

        $response->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));
    }
    /**
     * DELETE ..api/users/:user_id
     * USER CAN'T DELETE ANOTHER USER
     */
    public function testUserCantDeleteUser()
    {
        $response = $this->actingAs($this->user, 'api')->deleteJson($this->apiVersion . 'users/' . $this->admin->id);

        $response->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));
    }
    /**
     * DELETE ..api/users/:user_id
     * USER CAN'T RESTORE A DELETED USER
     */
    public function testUserCantRestoreUser()
    {
        //DELETE FIRST, THEN RESTORE
        $deleteResponse  = $this->actingAs($this->user, 'api')->deleteJson($this->apiVersion . 'users/' . $this->admin->id);
        $restoreResponse = $this->actingAs($this->user, 'api')->putJson($this->apiVersion . 'users/' . $this->admin->id);
        $deleteResponse->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));

        $restoreResponse->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));
    }
    /**
     * PUT ..api/users/:user_id
     * USER CAN'T UPDATE SOMEONE ELSE'S PROFILE
     */
    public function testUserCantUpdateOtherProfile()
    {
        $oldEmail = $this->admin->email;
        $newEmail = $this->faker->email();
        $response = $this->actingAs($this->user, 'api')->putJson($this->apiVersion . 'users/' . $this->admin->id, [
            'email' => $newEmail,
        ]);

        //return failure - user can't change super-admin profile
        $response->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));
    }
    /**
     * PUT ..api/users/:user_id
     * USER CAN UPDATE OWN PROFILE
     */
    public function testUserCanUpdateOwnProfile()
    {
        $response = $this->actingAs($this->user, 'api')->putJson($this->apiVersion . 'users/' . $this->user->id, [
            'email' => $this->faker->email()
        ]);

        //return success - user may update their own profile
        $response->assertStatus(200);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.update.success', ['id' => $this->user->id])
        ));
    }
    /**
     * PUT ..api/users/:user_id
     * USER CAN UPDATE ADDITIONAL USER DATA
     */
    public function testUserCanUpdateAdditionalData()
    {
        $email = $this->faker->email();
        $user = $this->createUserWithAdditionalData($email);

        $response = $this->actingAs($this->admin, 'api')->putJson($this->apiVersion . 'users/' . $user['data'][0]['id'], [
            'data' => [
                'first_name' => $this->faker->firstName()
            ]
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.update.success', ['id' => $user['data'][0]['id']])
        ));
    }
    /**
     * PUT ..api/users/:user_id
     * USER ATTEMPTS TO EDIT NON-EXISTENT ADDITIONAL USER DATA
     */
    public function testUserCantUpdateUserWithRandomData()
    {
        $email = $this->faker->email();
        $user = $this->createUserWithAdditionalData($email);
        $response = $this->actingAs($this->admin, 'api')->putJson($this->apiVersion . 'users/' . $user['data'][0]['id'], [
            'email' => $this->faker->email(),
            'data' => [
                'random_input' => $this->faker->firstName()
            ]
        ]);

        $response->assertJson($this->apiResponse(
            false,
            500,
            __('users.update.failed', ['id' => $user['data'][0]['id']])
        ));
    }

    /**
     * POST ..api/users/:user_id/roles
     * ASSIGN MULTIPLE ROLES - USERS WILL HAVE THESE ROLES IN ADDITION TO CURRENTLY ASSIGNED ROLES
     */
    public function testUserCanBeAssignedRoles()
    {
        $role1 = Role::create(['name' => 'test_role_1']);
        $role2 = Role::create(['name' => 'test_role_2']);
        $roleRequestArray = [$role1->id,$role2->id];
        $response = $this->actingAs($this->admin, 'api')->postJson($this->apiVersion . 'users/' . $this->user->id . '/roles', [
            'email' => $this->faker->email(),
            'roles' => [$role1->id,$role2->id]
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.roles.create.success', ['user_id' => $this->user->id, 'role_id' => implode(',', $roleRequestArray)])
        ));
    }

    /**
     * DELETE ..api/users/:user_id/roles/:role_id
     * REMOVE ROLE FROM USER - USE WILL RETAIN ALL ROLES, MINUS THE SPECIFIED ROLE
     */
    public function testUserCanBeRemovedFromRole()
    {
        $role = $this->user->roles()->first();
        $response = $this->actingAs($this->admin, 'api')->deleteJson($this->apiVersion . 'users/' . $this->user->id . '/roles/' . $role->id);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.roles.remove.success', ['user_id' => $this->user->id, 'role_id' => $role->id])
        ));
    }

    /**
     * POST ..api/users/:user_id/roles
     * SYNC USER WITH ROLES - USER WILL ONLY BE ASSIGNED TO THE FOLLOWING ROLES AFTER SYNC
     */
    public function testUserCanBeSyncedWithRoles()
    {
        $role1 = Role::create(['name' => 'test_role_1']);
        $role2 = Role::create(['name' => 'test_role_2']);
        $roleRequestArray = [$role1->id,$role2->id];
        $response = $this->actingAs($this->admin, 'api')->postJson($this->apiVersion . 'users/' . $this->user->id . '/roles/sync', [
            'email' => $this->faker->email(),
            'roles' => [$role1->id,$role2->id]
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.roles.sync.success', ['user_id' => $this->user->id, 'role_id' => implode(',', $roleRequestArray)])
        ));
    }
}
