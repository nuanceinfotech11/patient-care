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
        Schema::create('stock_out', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->nullable();
            $table->date('date')->nullable();
            $table->string('patient_code')->nullable();
            $table->string('carer_code')->nullable();
            $table->string('inventory_code')->nullable();
            $table->integer('quantity')->nullable();
            
            $table->string('stock_out_by')->nullable();
            
            
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
        Schema::dropIfExists('stock_out');
    }
};
