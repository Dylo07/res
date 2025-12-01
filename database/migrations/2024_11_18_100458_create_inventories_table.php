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
        Schema::create('inv_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('inv_products')->onDelete('cascade');
            $table->date('stock_date');
            $table->float('stock_level');
            $table->timestamps();
            
            $table->unique(['product_id', 'stock_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_inventories');
    }
};
