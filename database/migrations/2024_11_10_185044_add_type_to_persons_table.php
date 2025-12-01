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
        Schema::table('persons', function (Blueprint $table) {
            $table->string('type')->default('individual'); // Add the 'type' column with a default value
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('type'); // Remove the 'type' column if rolled back
        });
    }
};
