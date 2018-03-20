<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReferenceToProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column for thumbnail path
        Schema::table('properties', function (Blueprint $table){

            // Add column
            $table->string('reference_code', 6)->after('pricing_type_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove column for password reset token
        Schema::table('properties', function (Blueprint $table){

            // Add column
            $table->dropColumn('reference_code');

        });
    }
}
