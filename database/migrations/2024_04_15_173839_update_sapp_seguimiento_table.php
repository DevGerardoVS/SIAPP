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
        if (!Schema::hasColumn('sapp_seguimiento', 'clave_seguimiento')) {
            Schema::table('sapp_seguimiento', function (Blueprint $table) {
                $table->dropUnique('clave_seguimiento');
            });
        } 
        Schema::table('sapp_seguimiento', function (Blueprint $table) {
            $table->unique(['id','meta_id', 'clv_upp', 'clv_ur', 'clv_programa', 'clv_subprograma', 'clv_proyecto', 'ejercicio', 'mes','deleted_at'], 'clave_seguimiento');
        });
        
                    

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
