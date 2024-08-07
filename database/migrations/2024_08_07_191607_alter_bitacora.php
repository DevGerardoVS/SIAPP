<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('adm_bitacora', 'id_sistema')) {
            Schema::table('adm_bitacora', function (Blueprint $table) {
                $table->tinyInteger('id_sistema')->after('id')->nullable(true);
            });
        }
        if (!Schema::hasColumn('adm_bitacora', 'data')) {
            Schema::table('adm_bitacora', function (Blueprint $table) {
                $table->json('data')->after('accion')->nullable(true);
            });
        }
        DB::unprepared("UPDATE epp SET presupuestable = 1 WHERE id NOT IN (
                SELECT id FROM v_epp
                WHERE clv_programa IN ('5H','RM')
                );
                UPDATE epp SET presupuestable = 0 WHERE id IN (
                	SELECT id FROM v_epp
                	WHERE clv_programa IN ('5H','RM')
                );
                UPDATE v_epp SET presupuestable = 1 WHERE clv_programa NOT IN ('5H','RM');
                UPDATE v_epp SET presupuestable = 0 WHERE clv_programa IN ('5H','RM');
                #CON MIR
                UPDATE epp SET con_mir = 1 WHERE id NOT IN (
                	SELECT id FROM v_epp
                	WHERE clv_programa IN ('5H','RM')
                	UNION ALL 
                	SELECT id FROM v_epp
                	WHERE clv_subprograma IN ('UUU','21B')
                );
                UPDATE epp SET con_mir = 0 WHERE id IN (
                	SELECT id FROM v_epp
                	WHERE clv_programa IN ('5H','RM')
                	UNION ALL 
                	SELECT id FROM v_epp
                	WHERE clv_subprograma IN ('UUU','21B')
                );
                UPDATE v_epp ve
                JOIN epp e ON ve.id = e.id
                SET ve.con_mir = e.con_mir;
            ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
