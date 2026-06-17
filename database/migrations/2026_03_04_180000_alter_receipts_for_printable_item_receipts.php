<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReceiptsForPrintableItemReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('receipts', 'store_id')) {
                $table->unsignedBigInteger('store_id')->nullable()->after('uuid');
            }

            if (!Schema::hasColumn('receipts', 'sales_by')) {
                $table->unsignedBigInteger('sales_by')->nullable()->after('store_id');
            }
        });

        Schema::table('receipt_details', function (Blueprint $table) {
            if (!Schema::hasColumn('receipt_details', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('receipt_id');
            }

            if (!Schema::hasColumn('receipt_details', 'item_no')) {
                $table->string('item_no')->nullable()->after('item_id');
            }

            if (!Schema::hasColumn('receipt_details', 'sales_price')) {
                $table->decimal('sales_price', 12, 2)->nullable()->after('item_weight');
            }

            if (!Schema::hasColumn('receipt_details', 'line_total')) {
                $table->decimal('line_total', 12, 2)->nullable()->after('service_fee');
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
        Schema::table('receipt_details', function (Blueprint $table) {
            $columns = [];

            foreach (['item_id', 'item_no', 'sales_price', 'line_total'] as $column) {
                if (Schema::hasColumn('receipt_details', $column)) {
                    $columns[] = $column;
                }
            }

            if (count($columns) > 0) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            $columns = [];

            foreach (['store_id', 'sales_by'] as $column) {
                if (Schema::hasColumn('receipts', $column)) {
                    $columns[] = $column;
                }
            }

            if (count($columns) > 0) {
                $table->dropColumn($columns);
            }
        });
    }
}
