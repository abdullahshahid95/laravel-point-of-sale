<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropItemIdColumnInRequiredTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });
        Schema::table('inventory_deductions', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('required_tables', function (Blueprint $table) {
            //
        });
    }
}
