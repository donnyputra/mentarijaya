<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item', function (Blueprint $table) {
            $table->id();
            $table->string('item_no')->unique();
            $table->string('item_name');
            $table->decimal('item_weight', 8, 2);
            $table->decimal('item_gold_rate', 8, 2);
            $table->decimal('sales_price', 8, 2)->nullable();
            $table->timestamp('sales_at', 0)->nullable();

            $table->unsignedBigInteger('item_status_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('allocation_id');
            $table->unsignedBigInteger('sales_status_id')->nullable();
            $table->unsignedBigInteger('sales_by')->nullable();
            $table->unsignedBigInteger('store_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('item_status_id')->references('id')->on('item_status');
            $table->foreign('category_id')->references('id')->on('category');
            $table->foreign('allocation_id')->references('id')->on('allocation');
            $table->foreign('sales_status_id')->references('id')->on('sales_status');
            $table->foreign('sales_by')->references('id')->on('users');
            $table->foreign('store_id')->references('id')->on('store');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item');
    }
}
