<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Role;
use App\Models\User;
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
        $response = $this->postJson($this->apiVersion . 'users/login',
            [
                'email' => $email,
                'password' => $password
            ]);

        return $response;
    }

    /**
     * POST ..api/users/login
     *
     * LOGIN SUCCESS
     */
    public function testSuccessfulLogin()
    {
        $response = $this->login($this->admin->email, $this->password);

        $response->assertStatus(200);
    }

    /**
     * POST ..api/users/login
     *
     * LOGIN FAILURE
     */
    public function testFailedLogin()
    {
        $response = $this->login('user_does_not_exist@email.com', $this->password);

        $response->assertStatus(401);
    }

    /**
     * POST ..api/users
     *
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
     * POST ..api/users
     *
     * USER CAN'T CREATE NEW USER
     */
    public function testUserCantCreateUser()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson($this->apiVersion . 'users',
                [
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
     *
     * SUPER ADMIN CAN DELETE NEW USER
     */
    public function testSuperAdminCanDeleteUser()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->deleteJson($this->apiVersion . 'users/' . $this->user->id);
        $deletedUser = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users/' . $this->user->id);

        $response->assertStatus(200);
        $deletedUser->assertStatus(404);
    }

    /**
     * DELETE ..api/users/:user_id
     *
     * USER CAN'T DELETE ANOTHER USER
     */
    public function testUserCantDeleteUser()
    {
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($this->apiVersion . 'users/' . $this->admin->id);

        $response->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));
    }

    /**
     * GET ..api/users
     *
     * DELETED RECORDS ARE NOT INCLUDED IN INDEX
     */
    public function testIndexDoesNotReturnDeletedUsers()
    {
        $this->user->delete();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users');

        $userIdArray = array_column($response['data']['users'], 'id');

        $response->assertStatus(200);
        $this->assertNotContains($this->user->id, $userIdArray);
    }

    /**
     * GET ..api/users?includes=:relation_name
     *
     * USER CAN INCLUDE ADDITIONAL RELATIONS WITH AN INDEX
     */
    public function testIndexIncludesRelatedData()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users?includes=roles');

        $response->assertStatus(200);
        $this->assertArrayHasKey('roles', $response['data']['users'][0]);
    }

    /**
     * GET ..api/users?includes=:relation_name.:relation_relation_name
     *
     * USER CAN INCLUDE NESTED RELATIONS WITH AN INDEX
     */
    public function testIndexIncludesNestedRelatedData()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users?includes=roles.permissions');

        $response->assertStatus(200);
        $this->assertArrayHasKey('roles', $response['data']['users'][0]);
        $this->assertArrayHasKey('permissions', $response['data']['users'][0]['roles'][0]);
    }

    /**
     * GET ..api/users/all
     *
     * ALL RECORDS, INCLUDING DELETED ARE NOT RETURNED WITH INDEXALL
     */
    public function testIndexAllReturnsDeletedUsers()
    {
        $this->user->delete();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users/all');

        $userIdArray = array_column($response['data']['users'], 'id');

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.index.success')
        ));
        $this->assertContains($this->user->id, $userIdArray);
    }

    /**
     * GET ..api/users?trashed=1
     *
     * ONLY DELETED RECORDS ARE INCLUDED IN INDEXTRASHED
     */
    public function testIndexTrashedDoesReturnDeletedUsers()
    {
        $this->user->delete();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users?trashed=1');

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
     * PATCH ..api/users/:user_id
     *
     * SUPER ADMIN CAN RESTORE A DELETED USER
     */
    public function testSuperAdminCanRestoreUser()
    {
        //DELETE FIRST, THEN RESTORE
        $this->user->delete();

        $response = $this->actingAs($this->admin, 'api')
            ->patchJson($this->apiVersion . 'users/' . $this->user->id);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.restore.success', ['id' => $this->user->id])
        ));
    }

    /**
     * DELETE ..api/users/:user_id
     *
     * USER CAN'T RESTORE A DELETED USER
     */
    public function testUserCantRestoreUser()
    {
        //DELETE FIRST, THEN ATTEMPT RESTORE
        $this->admin->delete();

        $restoreResponse = $this->actingAs($this->user, 'api')
            ->putJson($this->apiVersion . 'users/' . $this->admin->id);

        $restoreResponse->assertJson($this->apiResponse(
            false,
            401,
            __('auth.unauthorized')
        ));
    }

    /**
     * PUT ..api/users/:user_id
     *
     * USER CAN'T UPDATE SOMEONE ELSE'S PROFILE
     */
    public function testUserCantUpdateOtherProfile()
    {
        $newEmail = $this->faker->email();

        $response = $this->actingAs($this->user, 'api')
            ->putJson($this->apiVersion . 'users/' . $this->admin->id,
                [
                    'email' => $newEmail
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
     *
     * USER CAN UPDATE OWN PROFILE
     */
    public function testUserCanUpdateOwnProfile()
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson($this->apiVersion . 'users/' . $this->user->id,
                [
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
     *
     * USER CAN UPDATE ADDITIONAL USER DATA
     */
    public function testUserCanUpdateAdditionalData()
    {
        $email = $this->faker->email();
        $user = $this->createUserWithAdditionalData($email);

        $response = $this->actingAs($this->admin, 'api')
            ->putJson($this->apiVersion . 'users/' . $user['data']['users'][0]['id'],
                [
                    'data' => [
                        'first_name' => $this->faker->firstName()
                    ]
                ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.update.success', ['id' => $user['data']['users'][0]['id']])
        ));
    }

    /**
     * PUT ..api/users/:user_id
     *
     * USER ATTEMPTS TO EDIT NON-EXISTENT ADDITIONAL USER DATA
     */
    public function testUserCantUpdateUserWithRandomData()
    {
        $email = $this->faker->email();
        $user = $this->createUserWithAdditionalData($email);

        $response = $this->actingAs($this->admin, 'api')
            ->putJson($this->apiVersion . 'users/' . $user['data']['users'][0]['id'],
                [
                    'email' => $this->faker->email(),
                    'data' => [
                        'random_input' => $this->faker->firstName()
                    ]
                ]);

        $response->assertJson($this->apiResponse(
            false,
            500,
            __('users.update.failed', ['id' => $user['data']['users'][0]['id']])
        ));
    }

    /**
     * POST ..api/users/:user_id/roles
     *
     * ASSIGN MULTIPLE ROLES - USERS WILL HAVE THESE ROLES IN ADDITION TO CURRENTLY ASSIGNED ROLES
     */
    public function testUserCanBeAssignedRoles()
    {
        $role1 = Role::create(['name' => 'test_role_1']);
        $role2 = Role::create(['name' => 'test_role_2']);
        $roleRequestArray = [$role1->id,$role2->id];

        $response = $this->actingAs($this->admin, 'api')
            ->postJson($this->apiVersion . 'users/' . $this->user->id . '/roles',
                [
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
        $response = $this->actingAs($this->admin, 'api')
            ->deleteJson($this->apiVersion . 'users/' . $this->user->id . '/roles/' . $role->id);

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

        $response = $this->actingAs($this->admin, 'api')
            ->postJson($this->apiVersion . 'users/' . $this->user->id . '/roles/sync',
                [
                    'email' => $this->faker->email(),
                    'roles' => [$role1->id,$role2->id]
                ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.roles.sync.success', ['user_id' => $this->user->id, 'role_id' => implode(',', $roleRequestArray)])
        ));
    }

    /**
     * GET ../api/user?search=:search_string
     *
     * @return void
     */
    public function testAdminUserCanSearchUserByName()
    {

        $searchTarget = "Unit Test SearchTarget";
        User::factory()->count(4)->create();
        User::factory()->create(['first_name' => $searchTarget]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users?search=' . $searchTarget);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.index.success'),
        ));
        $this->assertEquals($searchTarget, $response['data']['users'][0]['first_name'], $searchTarget);
        $this->assertCount(1, $response['data']['users']);
    }

    /**
     * GET ../api/users?search=:queryString&relation=:relation
     *
     * @return void
     */
    public function testAdminUserCanSearchUserRelation()
    {
        $role1 = Role::create(['name' => 'test_role_1']);
        $role2 = Role::create(['name' => 'test_role_2']);

        $users = User::factory()->count(2)->create();
        $findUser = $users->first();
        $findUser->assignRole($role1->name);
        $users->last()->assignRole($role2->name);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson($this->apiVersion . 'users?search=' . $role1->name . '&relation=roles');

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('users.index.success'),
        ));
        $this->assertEquals($findUser->email, $response['data']['users'][0]['email']);
        $this->assertCount(1, $response['data']['users']);
    }
}
