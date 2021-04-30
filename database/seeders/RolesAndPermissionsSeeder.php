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
    \DB::table('model_has_roles')->delete();
    \DB::table('model_has_permissions')->delete();
    \DB::table('role_has_permissions')->delete();
    \DB::table('roles')->delete();
    \DB::table('permissions')->delete();

    // Reset cached roles and permissions
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // create permissions
    Permission::create(['guard_name' => 'api', 'name' => 'create users']);
    Permission::create(['guard_name' => 'api', 'name' => 'list users']);
    Permission::create(['guard_name' => 'api', 'name' => 'update users']);
    Permission::create(['guard_name' => 'api', 'name' => 'delete users']);
    Permission::create(['guard_name' => 'api', 'name' => 'restore users']);


    // create roles and assign existing permissions
    $role1 = Role::create(['guard_name' => 'api', 'name' => 'super-admin']);
    $role1->givePermissionTo(Permission::all());

    // create roles and assign existing permissions - employees are currently locked down with no permissions
    $role2 = Role::create(['guard_name' => 'api', 'name' => 'user']);

     // assign basic permissions
    //SUPER ADMIN - all permissions are implicitly granted in App/Providers/AuthServiceProvider.php, so this is kinda redundant
    $user = \App\Models\User::find(1);
    $user->assignRole($role1);

    $user = \App\Models\User::find(2);
    $user->assignRole($role2);
  }
}
