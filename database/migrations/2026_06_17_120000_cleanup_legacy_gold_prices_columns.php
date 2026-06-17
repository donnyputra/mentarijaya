<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupLegacyGoldPricesColumns extends Migration
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

        if (Schema::hasColumn('gold_prices', 'base_price')) {
            if (Schema::hasColumn('gold_prices', 'max_price') && Schema::hasColumn('gold_prices', 'min_price')) {
                DB::statement('UPDATE gold_prices SET base_price = COALESCE(base_price, max_price, min_price)');
            } elseif (Schema::hasColumn('gold_prices', 'max_price')) {
                DB::statement('UPDATE gold_prices SET base_price = COALESCE(base_price, max_price)');
            } elseif (Schema::hasColumn('gold_prices', 'min_price')) {
                DB::statement('UPDATE gold_prices SET base_price = COALESCE(base_price, min_price)');
            }
        }

        Schema::table('gold_prices', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['min_price', 'max_price', 'deleted_at'] as $column) {
                if (Schema::hasColumn('gold_prices', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (count($dropColumns) > 0) {
                $table->dropColumn($dropColumns);
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
            if (!Schema::hasColumn('gold_prices', 'min_price')) {
                $table->decimal('min_price', 12, 2)->nullable()->after('id');
            }

            if (!Schema::hasColumn('gold_prices', 'max_price')) {
                $table->decimal('max_price', 12, 2)->nullable()->after('min_price');
            }

            if (!Schema::hasColumn('gold_prices', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        if (Schema::hasColumn('gold_prices', 'base_price')) {
            DB::statement('UPDATE gold_prices SET min_price = COALESCE(min_price, base_price), max_price = COALESCE(max_price, base_price)');
        }
    }
}
