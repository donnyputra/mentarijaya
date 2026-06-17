<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToReceiptDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('receipt_details', function (Blueprint $table) {
            if (!Schema::hasColumn('receipt_details', 'notes')) {
                $table->text('notes')->nullable()->after('line_total');
            }
        });
    }

    public function down()
    {
        Schema::table('receipt_details', function (Blueprint $table) {
            if (Schema::hasColumn('receipt_details', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
}
