<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Delete previous settings
    	DB::table('settings')->delete();

        // Add default settings
        $settings = [
        	'title' => [
        		'value' => 'Estate Agents',
        		'description' => 'Title of the website',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	],
        	'logo_id' => [
        		'value' => \App\MediaFile::inRandomOrder()->where( [ 'mime_type' => 'image/jpeg' ] )->first()->id,
        		'description' => 'Logo for the theme',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	],
        	'facebook_link' => [
        		'value' => 'https://www.facebook.com/',
        		'description' => 'URL to facebook page',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	],
        	'twitter_link' => [
        		'value' => 'https://twitter.com/',
        		'description' => 'URL to twitter page',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	],
        	'main_contact_email' => [
        		'value' => 'info@example.com',
        		'description' => 'Primary email address that sits in footer of website',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	],
        	'main_contact_number' => [
        		'value' => '02088004321',
        		'description' => 'Primary phone number that sits in footer of website',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	],
        ];

        // Seed data
        foreach ($settings as $key => $setting_details)
        	DB::table('settings')->insert([
        		'key' => $key,
        		'value' => $setting_details['value'],
        		'description' => $setting_details['description'],
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	]);
    }
}
