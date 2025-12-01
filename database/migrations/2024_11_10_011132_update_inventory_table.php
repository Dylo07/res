<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInventoryTable extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Ensure unique entries for each item and date combination
            $table->unique(['item_id', 'stock_date'], 'item_stock_unique');

            // Add optional columns for future enhancements if needed
            // Example: if you want to log when a stock level was updated
            if (!Schema::hasColumn('inventory', 'last_updated_by')) {
                $table->unsignedBigInteger('last_updated_by')->nullable()->after('stock_level');
            }
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropUnique('item_stock_unique');

            if (Schema::hasColumn('inventory', 'last_updated_by')) {
                $table->dropColumn('last_updated_by');
            }
        });
    }
}
