<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sapp_movimientos', function (Blueprint $table) {
            $table->renameColumn('original_sap', 'original_sapp');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sapp_movimientos', function (Blueprint $table) {
            $table->dropColumn('original_sapp');

        });
    }
};
