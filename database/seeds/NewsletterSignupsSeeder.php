<?php

use Illuminate\Database\Seeder;

class NewsletterSignupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
	{
		// Delete exiting newsletter signups
		DB::table('newsletter_signups')->delete();

		// Faker to fake data
		$faker = Faker\Factory::create( 'en_GB' );

		// Loop through documents and create random download numbers
		for ($i = 0; $i < rand(20, 100); $i++ )
		{
			DB::table('newsletter_signups')->insert([
				'email' => $faker->email,
				'first_name' => $faker->firstName,
				'last_name' => $faker->lastName,
				'created_at' => \Carbon::now(),
				'updated_at' => \Carbon::now(),
			]);
		}
	}
}
