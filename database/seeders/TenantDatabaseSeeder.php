<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $client = new ClientRepository();

        $client->createPasswordGrantClient(null, 'Default password grant client', 'https://eventogy_v2');
        $client->createPersonalAccessClient(null, 'Default personal access client', 'https://eventogy_v2');

        $this->call(UserTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
