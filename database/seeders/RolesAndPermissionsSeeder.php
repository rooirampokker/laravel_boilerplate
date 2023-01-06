<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['guard_name' => 'api', 'name' => 'user-store']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-index']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-indexAll']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-indexTrashed']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-update']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-delete']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-restore']);
        Permission::create(['guard_name' => 'api', 'name' => 'user-show']);


        // create roles and assign existing permissions
        $role1 = Role::create(['guard_name' => 'api', 'name' => 'super-admin']);
        $role1->givePermissionTo(Permission::all());

        // create roles and assign existing permissions - employees are currently locked down with no permissions
        $role2 = Role::create(['guard_name' => 'api', 'name' => 'user']);

        // assign basic permissions
        //SUPER ADMIN - all permissions are implicitly granted in App/Providers/AuthServiceProvider.php, so this is kinda redundant
        $user = \App\Models\User::first();
        $user->assignRole($role1);

        $user = \App\Models\User::latest('id')->first();
        $user->assignRole($role2);
    }
}
