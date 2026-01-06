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
        Schema::create('decease_inventory_mapping', function (Blueprint $table) {
            $table->id();
            $table->integer('decease_id')->unsigned();
            $table->integer('inventory_id')->unsigned();
    
         //FOREIGN KEY
           $table->foreign('decease_id')->references('id')->on('disease')->onDelete('cascade');
           $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('decease_inventory_mapping');
    }
};
