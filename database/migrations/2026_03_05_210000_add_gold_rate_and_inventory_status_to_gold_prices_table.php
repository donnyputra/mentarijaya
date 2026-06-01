<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoldRateAndInventoryStatusToGoldPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('gold_prices')) {
            return;
        }

        Schema::table('gold_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('gold_prices', 'gold_rate')) {
                $table->decimal('gold_rate', 8, 2)->nullable()->after('price_date');
                $table->index('gold_rate');
            }

            if (!Schema::hasColumn('gold_prices', 'inventory_status_id')) {
                $table->unsignedBigInteger('inventory_status_id')->nullable()->after('gold_rate');
                $table->index('inventory_status_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('gold_prices')) {
            return;
        }

        Schema::table('gold_prices', function (Blueprint $table) {
            if (Schema::hasColumn('gold_prices', 'inventory_status_id')) {
                $table->dropIndex(['inventory_status_id']);
                $table->dropColumn('inventory_status_id');
            }

            if (Schema::hasColumn('gold_prices', 'gold_rate')) {
                $table->dropIndex(['gold_rate']);
                $table->dropColumn('gold_rate');
            }
        });
    }
}
