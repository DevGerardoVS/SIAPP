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
        if (!Schema::hasTable('mml_observaciones_pp_hist')) {
            Schema::create('mml_observaciones_pp_hist', function (Blueprint $table) {
                $table->integer('id_original');
                $table->integer('version');
                $table->string('clv_upp', 4)->nullable();
                $table->string('clv_pp', 5);
                $table->unsignedInteger('problema_id');
                $table->tinyInteger('etapa')->unsigned();
                $table->text('comentario')->nullable();
                $table->string('ruta', 200)->nullable();
                $table->string('nombre', 500)->nullable();
                $table->integer('ejercicio')->nullable();
                $table->string('created_user', 45)->nullable();
                $table->string('updated_user', 45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->tinyInteger('ramo33');
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }
        if (!Schema::hasTable('mml_cremaa')) {
            Schema::create('mml_cremaa', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_mml_mir');
                $table->enum('claridad', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
                $table->enum('relevancia', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
                $table->enum('economia', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
                $table->enum('monitoreable', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
                $table->enum('adecuado', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
                $table->enum('aportacion_marginal', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
                $table->string('justificacion', 255);
                $table->date('serie');
                $table->string('responsable', 150);
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
                $table->timestamp('created_at');
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
                $table->foreign('id_mml_mir')->references('id')->on('mml_mir');
            });
        }
        if (!Schema::hasTable('mml_variable')) {
            Schema::create('mml_variable', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_mml_mir');
                $table->string('nombre', 150);
                $table->string('descripcion', 255);
                $table->string('unidad_medida', 50);
                $table->string('medios_verificacion', 200);
                $table->string('frecuencia', 50);
                $table->string('desagregacion_geografica', 50);
                $table->string('metodo_recopilacion_datos', 50);
                $table->date('fecha_disponibilidad');
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
                $table->timestamp('created_at');
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
                $table->foreign('id_mml_mir')->references('id')->on('mml_mir');
            });
        }
        if (!Schema::hasTable('mml_doc_enlaces')) {
            Schema::create('mml_doc_enlaces', function (Blueprint $table) {
                $table->increments('id');
                $table->string('clv_upp', 4);
                $table->string('id_usuario', 200);
                $table->string('nombre', 30);
                $table->string('ruta', 200);
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->timestamp('deleted_at')->default(null)->nullable();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable()->default(null);
                $table->string('deleted_user', 45)->nullable()->default(null);
                $table->unique('clv_upp', 'id_usuario');
            });
        }

        if (!Schema::hasTable('sapp_cierre_ejercicio')) {
            Schema::create('sapp_cierre_ejercicio', function (Blueprint $table) {
                $table->increments('id');
                $table->string('clv_upp', 3);
                $table->tinyInteger('enero')->default(0);
                $table->tinyInteger('febrero')->default(0);
                $table->tinyInteger('marzo')->default(0);
                $table->tinyInteger('trimestre_uno')->default(0);
                $table->tinyInteger('abril')->default(0);
                $table->tinyInteger('mayo')->default(0);
                $table->tinyInteger('junio')->default(0);
                $table->tinyInteger('trimestre_dos')->default(0);
                $table->tinyInteger('julio')->default(0);
                $table->tinyInteger('agosto')->default(0);
                $table->tinyInteger('septiembre')->default(0);
                $table->tinyInteger('trimestre_tres')->default(0);
                $table->tinyInteger('octubre')->default(0);
                $table->tinyInteger('noviembre')->default(0);
                $table->tinyInteger('diciembre')->default(0);
                $table->tinyInteger('trimestre_cuatro')->default(0);
                $table->integer('ejercicio');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
            });
        }
        if (!Schema::hasTable('sapp_movimientos')) {
            Schema::create('sapp_movimientos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('programacion_id')->nullable();
                $table->integer('ejercicio');
                $table->integer('mes');
                $table->integer('dia');
                $table->string('clv_upp', 3);
                $table->string('clv_ur', 2);
                $table->string('clv_programa', 2);
                $table->string('clv_subprograma', 3);
                $table->string('clv_proyecto', 3);
                $table->string('fondo', 9);
                $table->string('partida', 6);
                $table->string('area_funcional', 16);
                $table->string('centro_gestor', 16);
                $table->string('clasificacion_administrativa', 5);
                $table->string('proyecto_obra', 6);
                $table->decimal('original_sapp', 22, 2);
                $table->decimal('ampliacion', 22, 2);
                $table->decimal('reduccion', 22, 2);
                $table->decimal('traspaso', 22, 2);
                $table->decimal('modificado', 22, 2);
                $table->decimal('apartado', 22, 2);
                $table->decimal('comprometido', 22, 2);
                $table->decimal('comprometido_cp', 22, 2);
                $table->decimal('devengado', 22, 2);
                $table->decimal('devengado_cp', 22, 2);
                $table->decimal('ejercido', 22, 2);
                $table->decimal('ejercido_cp', 22, 2);
                $table->decimal('pagado', 22, 2);
                $table->decimal('disponible', 22, 2);
                $table->tinyInteger('estatus')->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
            });
        }

        if (!Schema::hasTable('sapp_seguimiento')) {
            Schema::create('sapp_seguimiento', function (Blueprint $table) {
                $table->unique(['meta_id', 'clv_upp', 'clv_ur', 'clv_programa', 'clv_subprograma', 'clv_proyecto', 'ejercicio', 'mes'], 'clave_seguimiento');
                $table->increments('id');
                $table->integer('meta_id');
                $table->string('clv_upp', 3);
                $table->string('clv_ur', 2);
                $table->string('clv_programa', 2);
                $table->string('clv_subprograma', 3);
                $table->string('clv_proyecto', 3);
                $table->integer('realizado');
                $table->text('descripcion_act');
                $table->text('justificacion');
                $table->text('propuesta_mejora');
                $table->text('observaciones');
                $table->integer('ejercicio');
                $table->tinyInteger('mes');
                $table->tinyInteger('estatus')->default(0);
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
            });
        }
        if (!Schema::hasTable('sapp_acuse')) {
            Schema::create('sapp_acuse', function (Blueprint $table) {
                $table->unique(['clv_upp', 'ejercicio', 'mes']);
                $table->increments('id');
                $table->string('clv_upp', 3);
                $table->string('ruta', 200);
                $table->string('nombre', 500);
                $table->tinyInteger('firmado')->default(false);
                $table->integer('ejercicio');
                $table->tinyInteger('mes');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
            });
        }
        if (!Schema::hasTable('sapp_enlaces')) {
            Schema::create('sapp_enlaces', function (Blueprint $table) {
                $table->unique(['clv_upp', 'id_usuario']);
                $table->increments('id');
                $table->string('clv_upp', 3);
                $table->integer('id_usuario');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
            });
        }

        Schema::table('permisos_funciones', function (Blueprint $table) {
            $table->string('created_user', 45)->add();
            $table->string('updated_user', 45)->nullable()->add();
            $table->string('deleted_user', 45)->nullable()->add();
        });

        Schema::table('cat_permisos', function (Blueprint $table) {
            $table->unsignedInteger('id_sistema')->default(1)->add();
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas')->add();
        });

        Schema::rename('carga_masiva_estatus', 'notificaciones');

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->Integer('id_sistema')->nullable(true);
            $table->renameColumn('cargapayload','payload');
            $table->renameColumn('cargaMasClav','status');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mml_cremaa');
        Schema::dropIfExists('mml_variable');
        Schema::dropIfExists('mml_doc_enlaces');
        Schema::dropIfExists('sapp_cierre_ejercicio');
        Schema::dropIfExists('sapp_movimientos');
        Schema::dropIfExists('sapp_seguimiento');
        Schema::dropIfExists('sapp_acuse');
        Schema::dropIfExists('sapp_enlaces');
    }
};
