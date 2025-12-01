<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('function_type')->nullable(); // Dropdown options
            $table->string('contact_number')->nullable(); // Contact number
            $table->text('room_numbers')->nullable(); // Multiple room selection
            $table->string('guest_count')->nullable(); // Adults and Kids count
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['function_type', 'contact_number', 'room_numbers', 'guest_count']);
        });
    }
};

