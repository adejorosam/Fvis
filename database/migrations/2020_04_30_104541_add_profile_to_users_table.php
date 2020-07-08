<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('relativename')->nullable();
            $table->string('relativenumber')->nullable();
            $table->string('employ_status')->nullable();
            $table->string('employ_company')->nullable();
            $table->string('employ_name')->nullable();
            $table->string('employ_number')->nullable();
            $table->string('salary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
