<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceFeeToGoldPricesTable extends Migration
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

        if (Schema::hasColumn('gold_prices', 'service_fee')) {
            return;
        }

        Schema::table('gold_prices', function (Blueprint $table) {
            $table->decimal('service_fee', 12, 2)->nullable()->after('base_price');
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

        if (!Schema::hasColumn('gold_prices', 'service_fee')) {
            return;
        }

        Schema::table('gold_prices', function (Blueprint $table) {
            $table->dropColumn('service_fee');
        });
    }
}
