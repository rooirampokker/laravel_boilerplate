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
             'id' => 'test'
         ]
        ];
        foreach ($tenants as $tenant) {
            $thisTenant = Tenant::create($tenant);
            $thisTenant->domains()->create(['domain' => $tenant['id'] . "." . env('APP_DOMAIN')]);
        }
    }
}
