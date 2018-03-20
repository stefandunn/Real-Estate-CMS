<?php

use Illuminate\Database\Seeder;

class DocumentDownloadSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		// Get media entries of PDF
		$documents = \App\MediaFile::where( [ 'mime_type' => 'application/pdf' ] )->get();

		// Loop through documents and create random download numbers
		foreach ($documents as $document)
		{
			DB::table('document_downloads')->insert([
				'document_id' => $document->id,
				'downloads' => rand(10, 100),
				'created_at' => \Carbon::now(),
				'updated_at' => \Carbon::now(),
			]);
		}
	}
}
