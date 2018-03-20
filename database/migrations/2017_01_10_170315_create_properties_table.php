<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_type_id')->unsigned()->index();
            $table->integer('feature_image_id')->unsigned()->index();
            $table->integer('purchase_type_id')->unsigned()->index();
            $table->integer('pricing_type_id')->unsigned()->nullable();

            $table->string('name', 255);
            $table->integer('price')->nullable();
            $table->text('description')->nullable();
            $table->text('overview')->nullable();
            $table->text('snippet');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('town')->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('short_address')->nullable();
            $table->string('longitude', 20)->nullable();
            $table->string('latitude', 20)->nullable();
            $table->string('contact_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('tags')->nullable();
            $table->integer('square_footage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
