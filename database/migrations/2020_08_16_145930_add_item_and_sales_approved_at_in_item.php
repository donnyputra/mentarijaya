<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemAndSalesApprovedAtInItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function($table) {
            $table->timestamp('item_approved_at', 0)->nullable();
            $table->timestamp('sales_approved_at', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item', function($table) {
            $table->dropColumn('item_approved_at');
            $table->dropColumn('sales_approved_at');
        });
    }
}
