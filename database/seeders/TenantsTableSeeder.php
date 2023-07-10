<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantsTableSeeder extends Seeder
{
  /**
   * Auto generated seed file
   *
   * @return void
   */
    public function run()
    {
        $tenants = [
         [
             'name' => 'test',
             'tenancy_db_name' => 'laravel_test_tenant',
             'description' => 'test tenant generated during initial seeding'
         ]
        ];
        foreach ($tenants as $tenant) {
            $thisTenant = Tenant::create($tenant);
            $thisTenant->domains()->create(['domain' => $tenant['name'] . "." . env('APP_DOMAIN')]);
        }
    }
}
