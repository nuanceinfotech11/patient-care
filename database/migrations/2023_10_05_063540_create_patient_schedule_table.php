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
        Schema::create('patient_schedule', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->nullable();
            $table->date('date')->nullable();
            $table->string('time')->nullable();
            $table->integer('carer_code')->nullable();
            $table->integer('carer_assigned_by')->nullable();
            $table->integer('alternate_carer_code')->nullable();
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
        Schema::dropIfExists('patient_schedule');
    }
};
