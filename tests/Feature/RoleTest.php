<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
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
    /**
     * ADMIN CAN DELETE NEW ROLE
     */
//    public function testAdminCanCreateRole()
//    {
//        $response = $this->actingAs($this->admin, 'api')->deleteJson('api/users/' . $this->user->id);
//        $deletedUser = $this->actingAs($this->admin, 'api')->getJson('api/users/' . $this->user->id);
//
//        $response->assertStatus(200);
//        $deletedUser->assertStatus(500);
//    }
//    /**
//     * DELETED RECORDS ARE NOT INCLUDED IN INDEX
//     */
//    public function testIndexDoesNotReturnDeletedUsers()
//    {
//        $this->actingAs($this->admin, 'api')->deleteJson('api/users/' . $this->user->id);
//        $response    = $this->actingAs($this->admin, 'api')->GETJson('api/users');
//        $userIdArray = array_column($response['data'], 'id');
//
//        $response->assertStatus(200);
//        $this->assertNotContains($this->user->id, $userIdArray);
//    }
//    /**
//     * ALL RECORDS, INCLUDING DELETED ARE NOT RETURNED WITH INDEXALL
//     */
//    public function testIndexAllReturnsDeletedUsers()
//    {
//        $this->actingAs($this->admin, 'api')->deleteJson('api/users/' . $this->user->id);
//
//        $response    = $this->actingAs($this->admin, 'api')->GETJson('api/users/all');
//        $userIdArray = array_column($response['data'], 'id');
//
//        $response->assertStatus(200);
//        $this->assertContains($this->user->id, $userIdArray);
//    }
//    /**
//     * ONLY DELETED RECORDS ARE INCLUDED IN INDEXTRASHED
//     */
//    public function testIndexTrashedDoesReturnDeletedUsers()
//    {
//        $this->actingAs($this->admin, 'api')->deleteJson('api/users/' . $this->user->id);
//        $response = $this->actingAs($this->admin, 'api')->GETJson('api/users/trashed');
//        $userIdArray = array_column($response['data'], 'id');
//
//        $response->assertStatus(200);
//        $this->assertContains($this->user->id, $userIdArray);
//        $this->assertCount(1, $response['data']);
//    }
//    /**
//     * DELETE/PATCH ..api/users/:user_id
//     * SUPER ADMIN CAN RESTORE A DELETED USER
//     */
//    public function testSuperAdminCanRestoreUser()
//    {
//        //DELETE FIRST, THEN RESTORE
//        $this->actingAs($this->admin, 'api')->deleteJson('api/users/' . $this->user->id);
//        $restoreResponse = $this->actingAs($this->admin, 'api')->patchJson('api/users/' . $this->user->id);
//        $restoreResponse->assertStatus(200);
//    }
//
//    /**
//     * POST ..api/users
//     * USER CAN'T CREATE NEW USER
//     */
//    public function testUserCantCreateUser()
//    {
//        $response = $this->actingAs($this->user, 'api')->postJson('api/users', [
//            'email' => $this->faker->email(),
//            'password' => $this->password,
//            'c_password' => $this->password
//        ]);
//
//        $response->assertStatus(401);
//    }
//    /**
//     * DELETE ..api/users/:user_id
//     * USER CAN'T DELETE ANOTHER USER
//     */
//    public function testUserCantDeleteUser()
//    {
//        $response = $this->actingAs($this->user, 'api')->deleteJson('api/users/' . $this->admin->id);
//
//        $response->assertStatus(401);
//    }
//    /**
//     * DELETE ..api/users/:user_id
//     * USER CAN'T RESTORE A DELETED USER
//     */
//    public function testUserCantRestoreUser()
//    {
//        //DELETE FIRST, THEN RESTORE
//        $deleteResponse  = $this->actingAs($this->user, 'api')->deleteJson('api/users/' . $this->admin->id);
//        $restoreResponse = $this->actingAs($this->user, 'api')->putJson('api/users/' . $this->admin->id);
//
//        $deleteResponse->assertStatus(401);
//        $restoreResponse->assertStatus(401);
//    }
//    /**
//     * PUT ..api/users/:user_id
//     * USER CAN'T UPDATE SOMEONE ELSE'S PROFILE
//     */
//    public function testUserCantUpdateOtherProfile()
//    {
//        $oldEmail = $this->admin->email;
//        $newEmail = $this->faker->email();
//        $response = $this->actingAs($this->user, 'api')->putJson('api/users/' . $this->admin->id, [
//            'email' => $newEmail,
//        ]);
//
//        //return failure - user can't change super-admin profile
//        $response->assertStatus(401);
//    }
//    /**
//     * PUT ..api/users/:user_id
//     * USER CAN UPDATE OWN PROFILE
//     */
//    public function testUserCanUpdateOwnProfile()
//    {
//        $oldEmail = $this->user->email;
//        $newEmail = $this->faker->email();
//        $response = $this->actingAs($this->user, 'api')->putJson('api/users/' . $this->user->id, [
//            'email' => $newEmail
//        ]);
//
//        //return success - user may update their own profile
//        $response->assertStatus(200);
//
//        $response->assertJson([
//            'success' => true,
//            'code' => 200,
//            'message' =>  __('users.update.success', ['id' => $this->user->id])
//        ]);
//    }
//    /**
//     * PUT ..api/users/:user_id
//     * USER CAN UPDATE ADDITIONAL USER DATA
//     */
//    public function testUserCanUpdateAdditionalData()
//    {
//        $email = $this->faker->email();
//        $user = $this->createUserWithAdditionalData($email);
//
//        $response = $this->actingAs($this->admin, 'api')->putJson('api/users/' . $user['data'][0]['id'], [
//            'data' => [
//                'first_name' => $this->faker->firstName()
//            ]
//        ]);
//
//        $response->assertJson([
//            'success' => true,
//            'code' => 200,
//            'message' =>  __('users.update.success', ['id' => $user['data'][0]['id']])
//        ]);
//    }
//    /**
//     * PUT ..api/users/:user_id
//     * USER ATTEMPTS TO EDIT NON-EXISTENT ADDITIONAL USER DATA
//     */
//    public function testUserCantUpdateUserWithRandomData()
//    {
//        $email = $this->faker->email();
//        $user = $this->createUserWithAdditionalData($email);
//        $response = $this->actingAs($this->admin, 'api')->putJson('api/users/' . $user['data'][0]['id'], [
//            'email' => $this->faker->email(),
//            'data' => [
//                'random_input' => $this->faker->firstName()
//            ]
//        ]);
//
//        $response->assertJson([
//            'success' => false,
//            'code' => 500,
//            'message' =>  __('users.update.failed', ['id' => $user['data'][0]['id']])
//        ]);
//    }
//
//    /**
//     * POST ..api/users/:user_id/roles
//     * ASSIGN MULTIPLE ROLES - USERS WILL HAVE THESE ROLES IN ADDITION TO CURRENTLY ASSIGNED ROLES
//     */
//    public function testUserCanBeAssignedRoles()
//    {
//        $role1 = Role::create(['name' => 'test_role_1']);
//        $role2 = Role::create(['name' => 'test_role_2']);
//        $roleRequestArray = [$role1->id,$role2->id];
//        $response = $this->actingAs($this->admin, 'api')->postJson('api/users/' . $this->user->id . '/roles', [
//            'email' => $this->faker->email(),
//            'roles' => [$role1->id,$role2->id]
//        ]);
//
//        $response->assertJson([
//            'success' => true,
//            'code' => 200,
//            'message' =>  __('users.roles.create.success', ['user_id' => $this->user->id, 'role_id' => implode(',', $roleRequestArray)])
//        ]);
//    }
//
//    /**
//     * DELETE ..api/users/:user_id/roles/:role_id
//     * REMOVE ROLE FROM USER - USE WILL RETAIN ALL ROLES, MINUS THE SPECIFIED ROLE
//     */
//    public function testUserCanBeRemovedFromRole()
//    {
//        $role = $this->user->roles()->first();
//        $response = $this->actingAs($this->admin, 'api')->deleteJson('api/users/' . $this->user->id . '/roles/'.$role->id);
//
//        $response->assertJson([
//            'success' => true,
//            'code' => 200,
//            'message' =>  __('users.roles.remove.success', ['user_id' => $this->user->id, 'role_id' => $role->id])
//        ]);
//    }
//
//    /**
//     * POST ..api/users/:user_id/roles
//     * SYNC USER WITH ROLES - USER WILL ONLY BE ASSIGNED TO THE FOLLOWING ROLES AFTER SYNC
//     */
//    public function testUserCanBeSyncedWithRoles()
//    {
//        $role1 = Role::create(['name' => 'test_role_1']);
//        $role2 = Role::create(['name' => 'test_role_2']);
//        $roleRequestArray = [$role1->id,$role2->id];
//        $response = $this->actingAs($this->admin, 'api')->postJson('api/users/' . $this->user->id . '/roles/sync', [
//            'email' => $this->faker->email(),
//            'roles' => [$role1->id,$role2->id]
//        ]);
//
//        $response->assertJson([
//            'success' => true,
//            'code' => 200,
//            'message' =>  __('users.roles.sync.success', ['user_id' => $this->user->id, 'role_id' => implode(',', $roleRequestArray)])
//        ]);
//    }
}
