<?php

namespace database\seeders;

use Illuminate\Database\Seeder;

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
             'id' => 'test',
             'created_at' => date("Y-m-d H:i:s"),
             'updated_at' => date("Y-m-d H:i:s"),
             'data' => ["tenancy_db_name" => "eventogy_test_tenant"],
         ],
        ];
        foreach ($tenants as $tenant) {
            \App\Models\Tenant::create($tenant);
        }
    }
}
