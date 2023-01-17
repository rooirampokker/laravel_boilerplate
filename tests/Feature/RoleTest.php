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
     * GET ../api/roles
     *
     * @return void
     */
    public function testUserCanGetRoleIndex() {
        $response = $this->actingAs($this->admin, 'api')->getJson('api/roles');

        $response->assertJson([
            'success' => true,
            'code' => 200,
            'message' =>  __('roles.index.success')
        ]);
    }

    /**
     * GET ../api/roles/{role_id}
     *
     * @return void
     */
    public function testUserCanGetRoleShow() {
        $response = $this->actingAs($this->admin, 'api')->getJson('api/roles/'.$this->adminRole->id);

        $response->assertJson([
            'success' => true,
            'code' => 200,
            'message' =>  __('roles.show.success'),
            'data' => []
        ]);
    }
    /**
     * POST ../api/roles
     *
     * @return void
     */
    public function testUserCanStoreRole() {
        $response = $this->actingAs($this->admin, 'api')->postJson('api/roles', [
            'name' => 'new_role'
        ]);

        $response->assertJson([
            'success' => true,
            'code' => 200,
            'message' =>  __('roles.store.success'),
            'data' => []
        ]);
    }
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
