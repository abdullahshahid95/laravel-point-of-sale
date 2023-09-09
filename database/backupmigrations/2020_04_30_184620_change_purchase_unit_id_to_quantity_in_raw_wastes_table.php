<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePurchaseUnitIdToQuantityInRawWastesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_wastes', function (Blueprint $table) {
            $table->dropColumn('purchase_unit_id');
        });
        Schema::table('raw_wastes', function (Blueprint $table) {
            $table->decimal('quantity', 8, 2)->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_wastes', function (Blueprint $table) {
            //
        });
    }
}
