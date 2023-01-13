<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UserTableSeeder extends Seeder
{
  /**
   * Auto generated seed file
   *
   * @return void
   */
    public function run()
    {
        $password = bcrypt('1234');
        $users = [
         [
             'id' => Uuid::uuid4()->toString(),
             'email' => 'user_1@gmail.com',
             'password' => $password,
             'created_at' => date("Y-m-d H:i:s"),
             'updated_at' => date("Y-m-d H:i:s"),
             'deleted_at' => null,
         ],
         [
             'id' => Uuid::uuid4()->toString(),
             'email' => 'user_2@gmail.com',
             'password' => $password,
             'created_at' => date("Y-m-d H:i:s"),
             'updated_at' => date("Y-m-d H:i:s"),
             'deleted_at' => null,
         ],
        ];
        foreach ($users as $user) {
            \App\Models\User::create($user);
        }
    }
}
