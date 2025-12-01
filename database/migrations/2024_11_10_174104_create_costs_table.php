<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('group_id'); // Foreign key for groups
            $table->string('person_or_shop'); // Name of the person or shop
            $table->decimal('amount', 10, 2); // Amount of the cost
            $table->date('cost_date'); // Date of the cost
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Foreign key constraint
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('costs');
    }
};
