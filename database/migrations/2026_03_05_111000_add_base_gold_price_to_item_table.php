<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseGoldPriceToItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('item', 'base_gold_price')) {
            return;
        }

        Schema::table('item', function (Blueprint $table) {
            $table->decimal('base_gold_price', 12, 2)->nullable()->after('sales_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('item', 'base_gold_price')) {
            return;
        }

        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn('base_gold_price');
        });
    }
}
