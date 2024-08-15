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
        if (Schema::hasTable('archivos_epp')) {
            Schema::table('archivos_epp', function (Blueprint $table) {
                $table->text('acciones')->nullable()->change();
            });

            if (!Schema::hasColumn('archivos_epp', 'descripcion')) {
                Schema::table('archivos_epp', function (Blueprint $table) {
                    $table->string('descripcion', 255)->nullable()->after('acciones');
                });
            }
        }

        if (Schema::hasTable('clasificacion_funcional')) {
            Schema::table('clasificacion_funcional', function (Blueprint $table) {
                $table->integer('subfuncion_id')->unsigned()->nullable()->change();
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
        //
    }
};
