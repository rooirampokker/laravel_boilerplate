<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessClient;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = Client::create([
            'name' => 'Personal Access Client',
            'secret' => config('passport.personal_access_client.secret'),
            'redirect => 'http://localhost',
            'personal_access_client' => '1',
            'password_client' => '0',
            'revoked' => '0']);

        PersonalAccessClient::create([
            'client_id' => $client->id,
        ]);
    }
}
