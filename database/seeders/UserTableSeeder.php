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
    \DB::table('user_data')->delete();
    \DB::table('users')->delete();

    \DB::table('users')->insert(array (
      0 =>
        array (
          'id' => 1,
          'email' => 'user_1@gmail.com',
          'password' => bcrypt('1234'),
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s"),
          'deleted_at' => NULL,
        ),
      1 =>
        array (
          'id' => 2,
          'email' => 'user_2@bravedigital.co.za',
          'password' => bcrypt('1234'),
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s"),
          'deleted_at' => NULL,
        ),
    ));


  }
}