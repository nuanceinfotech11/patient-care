<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_carer_map', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('company')->unsigned();
            $table->foreign('company')->references('id')->on('companies')->onDelete('cascade');

            $table->bigInteger('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('carer_id')->unsigned();
            $table->foreign('carer_id')->references('id')->on('users')->onDelete('cascade');
            




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
        Schema::dropIfExists('patient_carer_map');
    }
};
