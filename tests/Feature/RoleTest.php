<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Permission;
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

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.index.success'),
        ));
    }

    /**
     * GET ../api/roles/{role_id}
     *
     * @return void
     */
    public function testUserCanGetRoleShow() {
        $response = $this->actingAs($this->admin, 'api')->getJson('api/roles/'.$this->adminRole->id);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.show.success'),
        ));
    }
    /**
     * POST ../api/roles
     *
     * @return void
     */
    public function testUserCanStoreRole() {
        $response = $this->actingAs($this->admin, 'api')->postJson('api/roles', [
            'name' => 'new_role',
            'guard_name' => 'api'
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.store.success'),
        ));
    }
    /**
     * UPDATE ../api/roles/{role_id}
     *
     * @return void
     */
    public function testUserCanUpdateRole() {
        $response = $this->actingAs($this->admin, 'api')->putJson('api/roles/'.$this->adminRole->id, [
            'name' => 'new_role'
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.update.success'),
        ));
    }
    /**
     * DELETE ../api/roles/{role_id}
     *
     * @return void
     */
    public function testUserCanDeleteRole() {
        $response = $this->actingAs($this->admin, 'api')->deleteJson('api/roles/'.$this->adminRole->id);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.delete.success', ['id' => $this->adminRole->id]),
        ));
    }

    /**
     * POST ../api/roles/{role_id}/permissions
     *
     * @return void
     */
    public function testPermissionsCanBeAddedToRoles() {
        $permission1 = Permission::create(['name' => 'model-index', 'guard_name' =>'api'])->fresh();
        $permission2 = Permission::create(['name' => 'model-show', 'guard_name' =>'api'])->fresh();

        $permissionIDArray = [$permission1->id, $permission2->id];

        $response = $this->actingAs($this->admin, 'api')->postJson('api/roles/'.$this->adminRole->id.'/permissions', [
            'permissions' => $permissionIDArray
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.permissions.create.success', ['role_id' => $this->adminRole->id, 'permission_id' => implode(',', $permissionIDArray)]),
        ));
    }

    /**
     * DELETE ../api/roles/{role_id}/permissions
     *
     * @return void
     */
    public function testPermissionsCanBeRemovedFromRoles() {
        $response = $this->actingAs($this->admin, 'api')->deleteJson('api/roles/'.$this->adminRole->id.'/permissions/'.$this->adminRole->permissions->first()->id);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.permissions.delete.success', ['role_id' => $this->adminRole->id, 'permission_id' => $this->adminRole->permissions->first()->id])
        ));
    }

    /**
     * POST ../api/roles/{role_id}/permissions/sync
     *
     * @return void
     */
    public function testPermissionsCanBeSyncedToRoles() {
        $permission1 = Permission::create(['name' => 'model-index', 'guard_name' =>'api'])->fresh();
        $permission2 = Permission::create(['name' => 'model-show', 'guard_name' =>'api'])->fresh();

        $permissionIDArray = [$permission1->id, $permission2->id];

        $response = $this->actingAs($this->admin, 'api')->postJson('api/roles/'.$this->adminRole->id.'/permissions/sync', [
            'permissions' => $permissionIDArray
        ]);

        $response->assertJson($this->apiResponse(
            true,
            200,
            __('roles.permissions.sync.success', ['role_id' => $this->adminRole->id, 'permission_id' => implode(',', $permissionIDArray)]),
        ));
    }
}
