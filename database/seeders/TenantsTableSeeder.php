<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class TenantsTableSeeder extends Seeder
{
    /**
     *
     */
    public function run()
    {
        $tenants = [
         [
             'name' => 'BNP',
             'domain' => 'bnp',
             'database' => 'eventogy_v2_bnp'
         ],
         [
            'name' => 'Clifford Chance',
            'domain' => 'cliffordchace',
            'database' => 'eventogy_v2_cliffordchance'
         ],
        ];
        foreach ($tenants as $tenant) {
            \App\Models\Tenant::create($tenant);
        }
    }
}
