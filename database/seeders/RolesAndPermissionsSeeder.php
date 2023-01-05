<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use \Ramsey\Uuid\Uuid;

class RolesAndPermissionsSeeder extends Seeder
{
    private $uuidArray;
  /**
   * Create the initial roles and permissions.
   *
   * @return void
   */
  public function __construct() {
      $this->uuidArray = [];
      for($x=0; $x<10; $x++) {
          $this->uuidArray[] = Uuid::uuid4()->toString();
      }
  }
  public function run()
  {
    // Reset cached roles and permissions
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // create permissions
    Permission::create([
        'id' => $this->uuidArray[0],
        'guard_name' => 'api',
        'name' => 'user-store']);
    Permission::create([
        'id' => $this->uuidArray[1],
        'guard_name' => 'api',
        'name' => 'user-index']);
    Permission::create([
        'id' => $this->uuidArray[2],
        'guard_name' => 'api',
        'name' => 'user-indexAll']);
    Permission::create([
        'id' => $this->uuidArray[3],
        'guard_name' => 'api',
        'name' => 'user-indexTrashed']);
    Permission::create([
        'id' => $this->uuidArray[4],
        'guard_name' => 'api',
        'name' => 'user-update']);
    Permission::create([
        'id' => $this->uuidArray[5],
        'guard_name' => 'api',
        'name' => 'user-delete']);
    Permission::create([
        'id' => $this->uuidArray[6],
        'guard_name' => 'api',
        'name' => 'user-restore']);
    Permission::create([
        'id' => $this->uuidArray[7],
        'guard_name' => 'api',
        'name' => 'user-show']);


    // create roles and assign existing permissions
    $role1 = Role::create([
        'id' => $this->uuidArray[8],
        'guard_name' => 'api',
        'name' => 'super-admin']);
    $role1->givePermissionTo(Permission::all());

    // create roles and assign existing permissions - employees are currently locked down with no permissions
    $role2 = Role::create([
        'id' => $this->uuidArray[9],
        'guard_name' => 'api',
        'name' => 'user']);

     // assign basic permissions
    //SUPER ADMIN - all permissions are implicitly granted in App/Providers/AuthServiceProvider.php, so this is kinda redundant
    $user = \App\Models\User::first();
    $user->assignRole($role1);

    $user = \App\Models\User::last();
    $user->assignRole($role2);
  }
}
