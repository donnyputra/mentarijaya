<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryStatusInItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function($table) {
            $table->unsignedBigInteger('inventory_status_id');
            $table->foreign('inventory_status_id')->references('id')->on('inventory_status')->onDelete('cascade');
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
            $table->dropColumn('inventory_status_id');
        });
    }
}
