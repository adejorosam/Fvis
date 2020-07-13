<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('legalGivenName');
            $table->string('legalFamilyName');
            $table->boolean('isHavePreferredName');
            $table->string('preferredGivenName');
            $table->string('preferredFamilyName');
            $table->string('address');
            $table->string('email');
            $table->bigInteger('phoneNumber');
            $table->string('sourceOfInfo');
            $table->boolean('isWorkedBefore');
            $table->string('relevantWebsites');
            $table->string('linkedinProfileUrl');
            $table->string('resume');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('jobs');




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
        Schema::dropIfExists('job_applications');
    }
}
