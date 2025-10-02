<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
  /**
   * Auto generated seed file
   *
   * @return void
   */
    public function run()
    {
        User::factory()->create(['email' => 'user_1@gmail.com']);
        User::factory()->count(4)->create();
    }
}
