<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Delete old seeds
    	DB::table('users')->delete();

      // Add in test username
      DB::table('users')->insert([
        'name' => 'Test account',
        'username' => 'test',
        'email' => 'test@pexample.co.uk',
        'password' => bcrypt( 'PSS123' ),
            'reset_token' => bin2hex(random_bytes(64)), // Random 64 bit string hash
            'created_at' => \Carbon::now(),
            'updated_at' => \Carbon::now(),
      ]);
      
    }
}
