<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResetTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column for password reset token
        Schema::table('users', function (Blueprint $table){

            // Add column
            $table->string('reset_token', 255)->after('password');

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
        Schema::table('users', function (Blueprint $table){

            // Add column
            $table->dropColumn('reset_token');

        });
    }
}
