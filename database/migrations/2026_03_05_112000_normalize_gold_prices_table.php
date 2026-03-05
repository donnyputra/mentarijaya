<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizeGoldPricesTable extends Migration
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
            if (!Schema::hasColumn('gold_prices', 'price_date')) {
                $table->date('price_date')->nullable()->after('id');
            }

            if (!Schema::hasColumn('gold_prices', 'base_price')) {
                $table->decimal('base_price', 12, 2)->nullable()->after('price_date');
            }

            if (!Schema::hasColumn('gold_prices', 'notes')) {
                $table->text('notes')->nullable()->after('base_price');
            }

            if (!Schema::hasColumn('gold_prices', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')->nullable()->after('created_by');
            }
        });

        if (Schema::hasColumn('gold_prices', 'base_price') && Schema::hasColumn('gold_prices', 'max_price')) {
            DB::statement("UPDATE gold_prices SET base_price = COALESCE(base_price, max_price, min_price)");
        } elseif (Schema::hasColumn('gold_prices', 'base_price') && Schema::hasColumn('gold_prices', 'min_price')) {
            DB::statement("UPDATE gold_prices SET base_price = COALESCE(base_price, min_price)");
        }

        if (Schema::hasColumn('gold_prices', 'price_date')) {
            DB::statement("UPDATE gold_prices SET price_date = COALESCE(price_date, DATE(created_at), CURDATE())");
        }
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
            $dropColumns = [];

            foreach (['price_date', 'base_price', 'notes', 'created_by_user_id'] as $column) {
                if (Schema::hasColumn('gold_prices', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (count($dropColumns) > 0) {
                $table->dropColumn($dropColumns);
            }
        });
    }
}
