<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseServiceFeeToItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('item')) {
            return;
        }

        if (Schema::hasColumn('item', 'base_service_fee')) {
            return;
        }

        Schema::table('item', function (Blueprint $table) {
            $table->decimal('base_service_fee', 12, 2)->nullable()->after('base_gold_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('item')) {
            return;
        }

        if (!Schema::hasColumn('item', 'base_service_fee')) {
            return;
        }

        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn('base_service_fee');
        });
    }
}
