<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealEstateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_estate_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('phoneNumber');
            $table->string('email');
            $table->string('address');
            $table->string('typeOfBusiness');
            $table->string('noOfEmployee');
            $table->string('products');
            $table->string('areaOfInterest');
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
        Schema::dropIfExists('real_estate_contacts');
    }
}
