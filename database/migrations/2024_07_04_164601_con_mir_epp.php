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
        if (Schema::hasTable('v_epp')) {
            Schema::table('v_epp', function (Blueprint $table) {
                $table->tinyInteger('presupuestable')->nullable(false)->default(0)->after('estatus');
                $table->tinyInteger('con_mir')->nullable(false)->default(0)->after('presupuestable');
                $table->tinyInteger('confirmado')->nullable(false)->default(0)->after('con_mir');
                $table->tinyInteger('tipo_presupuesto')->nullable(true)->default(null)->after('confirmado');
            });
        }

        if (Schema::hasTable('epp')) {
            Schema::table('epp', function (Blueprint $table) {
                $table->integer('padre_id')->nullable(true)->default(null)->after('id');
            });
        }

        if (Schema::hasTable('upp_extras')) {
            Schema::table('upp_extras', function (Blueprint $table) {
                $table->smallInteger('estatus_epp')->nullable(true)->default(5)->change();
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
        
    }
};
