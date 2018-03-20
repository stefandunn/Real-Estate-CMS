<?php

use Illuminate\Database\Seeder;

class PropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This will insert property types, pricing types, purchase types and properties into database
     *
     * @return void
     */
    public function run()
    {

    	// Creater new faker for data entry
    	$faker = Faker\Factory::create( 'en_GB' );

    	// Add data into property types
    	$property_types = [
    		[
    			'name' => 'Commercial',
    			'description' => 'Commercial properties',
    		],
    		[
    			'name' => 'Upper Parts',
    			'description' => 'Commercial upper-parts',
    		],
    		[
    			'name' => 'Office',
    			'description' => 'Office spaces',
    		],
    		[
    			'name' => 'Retail',
    			'description' => 'Properties for retail shops',
    		],
    		[
    			'name' => 'Land',
    			'description' => 'Land for sale',
    		],
    		[
    			'name' => 'Public House',
    			'description' => 'Public houses for sale',
    		],
    	];

    	// Loop through
    	for( $i = 0; $i < count( $property_types); $i++ )
	    	DB::table('property_types')->insert($property_types[$i]);

    	// Add data into property pricing
    	$pricing_types = [
    		[
    			'name' => 'Per annum',
    			'description' => 'Price paid per year',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		],
    		[
    			'name' => 'Per calendar month',
    			'description' => 'price is paid on a calendar monthly basis',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		],
    		[
    			'name' => 'Freehold',
    			'description' => 'One-off freehold lease price for property',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		],
    		[
    			'name' => 'Sharehold',
    			'description' => 'One-off sharehold lease price for property',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		],
    	];

    	// Loop through
    	for( $i = 0; $i < count( $pricing_types); $i++ )
	    	DB::table('pricing_types')->insert($pricing_types[$i]);

    	// Add data into property pricing
    	$purchase_types = [
    		[
    			'name' => 'Rent',
    			'description' => 'Property is on a lease and rented out',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		],
    		[
    			'name' => 'Buy',
    			'description' => 'Property is to purchase as freehold or sharehold',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
    		],
    	];

    	// Loop through
    	for( $i = 0; $i < count( $purchase_types); $i++ )
	    	DB::table('purchase_types')->insert($purchase_types[$i]);

        // Seed away
        for( $i = 0; $i < 20; $i++)
        {
        	$building_no = $faker->buildingNumber;
        	$street = $faker->streetName;
        	$address_line_1 = $building_no . " " . $street;
        	$address_line_2 = (rand(0,1))? $faker->secondaryAddress : null;
        	$town = $faker->city;
        	$postcode = $faker->postcode;
        	$tags = [ 'commercial', 'office', 'rent', 'ground floor', 'retail', 'large'];
        	shuffle($tags);

        	DB::table('properties')->insert([
        		'property_type_id' => \App\PropertyType::inRandomOrder()->first()->id,
        		'pricing_type_id' => \App\PricingType::inRandomOrder()->first()->id,
        		'purchase_type_id' => \App\PurchaseType::inRandomOrder()->first()->id,
        		'feature_image_id' => \App\MediaFile::where([['mime_type', 'LIKE', 'image%']])->inRandomOrder()->first()->id,
                'reference_code' => substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ_2346789", 6)), 0, 6),
        		'name' => $address_line_1,
        		'price' => ( rand(0, count($purchase_types)) === 0 )? rand(500, 1000) : rand(200000, 100000),
        		'description' => $faker->paragraph(rand(2, 4)),
        		'overview' => $faker->paragraph(rand(1, 2)),
        		'snippet' => $faker->sentence(rand(6, 20)),
        		'address_line_1' => $address_line_1,
        		'address_line_2' => $address_line_2,
        		'town' => $town,
        		'postcode' => $postcode,
        		'short_address' => "{$address_line_1}, {$postcode}",
        		'longitude' => $faker->longitude(),
        		'latitude' => $faker->latitude(),
        		'contact_number' => (rand(1, 10)%2 == 0)? $faker->phoneNumber : null,
        		'contact_email' => (rand(1, 10)%2 == 0)? $faker->safeEmail : null,
        		'tags' => implode(",", array_slice( $tags, 0, rand(2, 5))),
        		'square_footage' => rand(100, 2400),
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
        	]);
        }

        // Seed property images (at most 3 for each property)
        foreach( \App\Property::all() as $property )
        {
            $media_used = [];
            for($i = 0; $i < rand(0, 3); $i++ )
            {
                $media_file = \App\MediaFile::where( [ [ 'mime_type', 'LIKE', 'image%' ] ] )->whereNotIn( 'id', $media_used )->inRandomOrder()->first()->id;
                $media_used[] = $media_file;
                DB::table('property_files')->insert([
                    'property_id' => $property->id,
                    'file_id' => $media_file,
                    'type' => 'image',
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now(),
                ]);
            }
        }

        // Seed property files/documents (at most 3 for each property)
        foreach( \App\Property::all() as $property )
        {
            $media_used = [];
            for($i = 0; $i < rand(0, 3); $i++ )
            {
                $media_file = \App\MediaFile::where( [ [ 'mime_type', 'NOT LIKE', 'image%' ] ] )->whereNotIn( 'id', $media_used )->inRandomOrder()->first()->id;
                $media_used[] = $media_file;
                DB::table('property_files')->insert([
                    'property_id' => $property->id,
                    'file_id' => $media_file,
                    'type' => 'document',
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now(),
                ]);
            }
        }
    }
}
