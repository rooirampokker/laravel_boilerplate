<?php
namespace Database\Seeders;

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
     $password = bcrypt('1234');
    \DB::table('users')->insert(array (
      0 =>
        array (
          'id' => 1,
          'email' => 'user_1@gmail.com',
          'password' => $password,
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s"),
          'deleted_at' => NULL,
        ),
      1 =>
        array (
          'id' => 2,
          'email' => 'user_2@gmail.com',
          'password' => $password,
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s"),
          'deleted_at' => NULL,
        ),
    ));


  }
}
