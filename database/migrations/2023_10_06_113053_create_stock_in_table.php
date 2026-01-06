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
        Schema::create('stock_in', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->nullable();
            $table->date('date')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('inventory_code')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('rate')->nullable();
            $table->string('stock_in_by')->nullable();
            $table->string('supplier_doc_no')->nullable();
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
        Schema::dropIfExists('stock_in');
    }
};
