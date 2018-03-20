<?php

use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Create faker
    	$data_faker = Faker\Factory::create();

    	// Media files to insert
    	$media_entries = [];

    	// Loop and add into media files
    	for($i = 1; $i < 10; $i++)
    		$media_entries[] = [
    			'title' => 'Test ' . ( ( $i%2 == 0 )? "image " : "document " ) . $i,
    			'path' => '/seed-media/' . ( ( $i%2 == 0 )? "image-{$i}.jpg" : "document-{$i}.pdf" ),
    			'natural_width' => ($i%2 == 0)? rand(300, 1000) : null,
    			'natural_height' => ($i%2 == 0)? rand(100, 500) : null,
    			'caption' => $data_faker->sentence(),
    			'alt' => $data_faker->sentence(),
    			'mime_type' => ($i%2 == 0)? "image/jpeg" : "application/pdf",
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		];

        // Seed files into upload folder and then records into Database
        for($i = 0; $i < count($media_entries); $i++ )
        	DB::table('media')->insert($media_entries[$i]);
    }
}
