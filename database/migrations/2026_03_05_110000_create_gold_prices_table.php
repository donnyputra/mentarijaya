<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoldPricesTable extends Migration
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
            $table->decimal('base_price', 12, 2);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('price_date');
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
