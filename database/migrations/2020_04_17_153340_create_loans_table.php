<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->string('interest');
            $table->string('final_amount');
            $table->integer('credit_score')->nullable();
            $table->string('ref')->nullable();
            $table->string('purpose')->nullable();
            $table->bigInteger('bank_id')->unsigned();
            $table->string('status')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->timestamp('repayment_date')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
        });
        
        Schema::table('loans', function($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
