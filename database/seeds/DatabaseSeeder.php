<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		// Delete old seeds
		DB::table('property_files')->delete();
		DB::table('document_downloads')->delete();
		DB::table('properties')->delete();
		DB::table('property_types')->delete();
		DB::table('purchase_types')->delete();
		DB::table('pricing_types')->delete();
		DB::table('media')->delete();
		DB::table('pages')->delete();

		$this->call(UsersSeeder::class);
		$this->call(MediaSeeder::class);
		$this->call(PropertiesSeeder::class);
		$this->call(SettingsSeeder::class);
		$this->call(DocumentDownloadSeeder::class);
		$this->call(NewsletterSignupsSeeder::class);
	}
}
