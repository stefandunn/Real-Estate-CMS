<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysOnPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add foreign key to ther database table "properties"
        Schema::table('properties', function (Blueprint $table) {
            $table->foreign('property_type_id')->references('id')->on('property_types');

            $table->foreign('feature_image_id')->references('id')->on('images');
            $table->foreign('purchase_type_id')->references('id')->on('purchase_types');
            $table->foreign('pricing_type_id')->references('id')->on('pricing_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
