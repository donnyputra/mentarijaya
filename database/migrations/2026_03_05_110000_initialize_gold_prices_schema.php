<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitializeGoldPricesSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('gold_prices')) {
            return;
        }

        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->date('price_date');
            $table->decimal('gold_rate', 8, 2)->nullable();
            $table->unsignedBigInteger('inventory_status_id')->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->decimal('service_fee', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index('price_date');
            $table->index('gold_rate');
            $table->index('inventory_status_id');
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

        Schema::dropIfExists('gold_prices');
    }
}
