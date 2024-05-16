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
        if (!Schema::hasColumn('adm_users', 'clv_ur')) {
            Schema::table('adm_users', function (Blueprint $table) {
                $table->string('clv_ur', 20)->after(('clv_upp'));
            });
        } 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adm_users', function (Blueprint $table) {
            $table->dropColumn('clv_ur');

        });
    }
};
