<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectFinanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_finance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email');
            $table->bigInteger('phoneNumber');
            $table->string('companyAddress')->nullable();
            $table->string('companyAddress2')->nullable();
            $table->string('companyName')->nullable();
            $table->string('companyWebsite')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postalCode')->nullable();
            $table->string('businessType')->nullable();
            $table->string('projectCountry');
            $table->string('projectDescription');
            $table->integer('projectedCost');
            $table->integer('totalAmountSpent');
            $table->integer('totalAmountRequested');
            $table->string('sourceOfRequiredEntity');
            $table->string('isLandOwner');
            $table->string('isNeedDevelopmentPartner');
            $table->boolean('isApprovalsComplete');
            $table->boolean('isHaveDevelopmentPartner');
            $table->boolean('isEngineeringComplete');
            $table->boolean('isConstructionBegun');
            $table->string('signature');

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
        Schema::dropIfExists('project_finance_requests');
    }
}
