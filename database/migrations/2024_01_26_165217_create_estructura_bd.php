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
       

        Schema::create('cat_permisos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 400);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        }); 

        Schema::create('adm_grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_grupo', 100);
            $table->tinyInteger('estatus')->default(0);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('adm_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_grupo');
            $table->string('nombre', 150);
            $table->string('p_apellido', 80);
            $table->string('s_apellido', 80);
            $table->string('email', 191)->unique();
            $table->string('celular', 20);
            $table->string('username', 80)->unique();
            $table->string('password', 200);
            $table->string('remember_token', 150);
            $table->string('clv_upp', 20)->nullable();
            $table->tinyInteger('estatus')->default(1);
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo' )->references('id')->on('adm_grupos');
        });

        Schema::create('adm_rel_user_grupo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_grupo');
            $table->unsignedInteger('id_usuario');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo')->references('id')->on('adm_grupos');
            $table->foreign('id_usuario')->references('id')->on('adm_users');
        });

        Schema::create('adm_sistemas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_sistema', 120);
            $table->string('ruta', 50);
            $table->string('logo', 100);
            $table->string('logo_min', 100);
            $table->longText('descripcion');
            $table->tinyInteger('estatus')->default(1);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('adm_rel_sistema_grupo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_grupo');
            $table->unsignedInteger('id_sistema');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo')->references('id')->on('adm_grupos');
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas');
        });

        Schema::create('adm_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_sistema');
            $table->tinyInteger('padre')->default(0);
            $table->string('nombre_menu', 100);
            $table->string('ruta', 400);
            $table->string('icono', 100);
            $table->tinyInteger('nivel')->default(0);
            $table->tinyInteger('posicion');
            $table->string('descripcion', 400);
            $table->tinyInteger('estatus')->default(1);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas');

        });

        Schema::create('adm_rel_menu_grupo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_grupo');
            $table->unsignedInteger('id_menu');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo')->references('id')->on('adm_grupos');
            $table->foreign('id_menu')->references('id')->on('adm_menus');
        });

        Schema::create('adm_funciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_sistema');
            $table->unsignedInteger('id_menu');
            $table->string('modulo', 50);
            $table->string('funcion', 70);
            $table->string('tipo', 50);
            $table->longText('descripcion');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas');
            $table->foreign('id_menu')->references('id')->on('adm_menus');
        });
        Schema::create('permisos_funciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_permiso');
            $table->longText('descripcion')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_user')->references('id')->on('adm_users');
            $table->foreign('id_permiso')->references('id')->on('cat_permisos');
        });

        Schema::create('adm_rel_funciones_grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_grupo');
            $table->unsignedInteger('id_funcion');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo')->references('id')->on('adm_grupos');
            $table->foreign('id_funcion')->references('id')->on('adm_funciones');
        });

        Schema::create('adm_bitacora', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 80);
            $table->string('accion', 200);
            $table->string('modulo', 80);
            $table->string('ip_origen', 50);
            $table->date('fecha_movimiento');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 191)->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tokenable_type', 191);
                $table->integer('tokenable_id');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::create('grupos', function (Blueprint $table){
            $table->increments('id');
            $table->string('grupo',100);
            $table->string('created_user',45);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
        });

        Schema::create('catalogo', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('grupo_id');
            $table->integer('ejercicio');
            $table->string('clave',6);
            $table->text('descripcion');
            $table->string('descripcion_larga',43);
            $table->string('descripcion_corta',22);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('grupo_id')->references('id')->on('grupos');
        });

        if (!Schema::hasTable('catalogo_hist')) {
            Schema::create('catalogo_hist', function (Blueprint $table){
                $table->increments('id');
                $table->integer('id_original');
                $table->unsignedInteger('grupo_id');
                $table->integer('ejercicio');
                $table->string('clave',6);
                $table->text('descripcion');
                $table->softDeletes();
                $table->string('created_user',45);
                $table->string('updated_user',45)->nullable();
                $table->string('deleted_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->foreign('grupo_id')->references('id')->on('grupos');
            });
        }

        Schema::create('entidad_ejecutora', function (Blueprint $table){
            $table->unique(['upp_id','subsecretaria_id','ur_id']);	
            $table->increments('id');
            $table->unsignedInteger('upp_id');
            $table->unsignedInteger('subsecretaria_id');
            $table->unsignedInteger('ur_id');
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('ejercicio');
        });

        Schema::create('clasificacion_geografica', function (Blueprint $table){
            $table->unique(['clv_entidad_federativa','clv_region','clv_municipio','clv_localidad'],'clave_cg');	
            $table->increments('id');
            $table->string('clv_entidad_federativa',2);
            $table->string('entidad_federativa',255);
            $table->string('clv_region',2);
            $table->string('region',255);
            $table->string('clv_municipio',3);
            $table->string('municipio',255);
            $table->string('clv_localidad',3);
            $table->string('localidad',255);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('fondo', function (Blueprint $table){
            $table->increments('id');
            $table->unique(['clv_etiquetado','clv_fuente_financiamiento','clv_ramo','clv_fondo_ramo','clv_capital'],'llave_fondo');
            $table->string('clv_etiquetado',1);
            $table->string('etiquetado', 255);
            $table->string('clv_fuente_financiamiento',1);
            $table->string('fuente_financiamiento', 255);
            $table->string('clv_ramo',2);
            $table->string('ramo', 255);
            $table->string('clv_fondo_ramo',2);
            $table->string('fondo_ramo', 255);
            $table->string('fondo_desc_corta', 22);
            $table->string('fondo_desc_larga', 43);
            $table->string('clv_capital',1);
            $table->string('capital', 255);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('posicion_presupuestaria', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_capitulo',1);
            $table->string('capitulo',255);
            $table->string('clv_concepto',1);
            $table->string('concepto',255);
            $table->string('clv_partida_generica',1);
            $table->string('partida_generica',255);
            $table->string('clv_partida_especifica',2);
            $table->string('partida_especifica',255);
            $table->string('partida_especifica_desc_corta',21);
            $table->string('clv_tipo_gasto',2)->nullable();
            $table->string('tipo_gasto',255)->nullable();
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('techos_financieros',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->string('clv_fondo',2);
            $table->bigInteger('presupuesto');
            $table->enum('tipo', ['Operativo', 'RH']);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('unidades_medida',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clave');
            $table->text('unidad_medida');
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('beneficiarios',function (Blueprint $table){
            $table->increments('id');
            $table->string('clave',2);
            $table->text('beneficiario');
            $table->integer('ejercicio')->default(null);
            $table->string('created_user',45);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));    
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
        });

        Schema::create('programacion_presupuesto',function (Blueprint $table){
            $table->increments('id');
            $table->string('clasificacion_administrativa',5);
            $table->string('entidad_federativa',2);
            $table->string('region',2);
            $table->string('municipio',3);
            $table->string('localidad',3);
            $table->string('upp',3);
            $table->string('subsecretaria',1);
            $table->string('ur',2);
            $table->string('finalidad',1);
            $table->string('funcion',1);
            $table->string('subfuncion',1);
            $table->string('eje',1);
            $table->string('linea_accion',2);
            $table->string('programa_sectorial',1);
            $table->string('tipologia_conac',1);
            $table->string('programa_presupuestario',2);
            $table->string('subprograma_presupuestario',3);
            $table->string('proyecto_presupuestario',3);
            $table->string('periodo_presupuestal',6);
            $table->string('posicion_presupuestaria',5);
            $table->string('tipo_gasto',1);
            $table->string('anio',2);
            $table->string('etiquetado',1);
            $table->string('fuente_financiamiento',1);
            $table->string('ramo',2);
            $table->string('fondo_ramo',2);
            $table->string('capital',1);
            $table->string('proyecto_obra',6);
            $table->integer('ejercicio')->nullable();
            $table->decimal('enero',22,2)->default(null);
            $table->decimal('febrero',22,2)->default(null);
            $table->decimal('marzo',22,2)->default(null);
            $table->decimal('abril',22,2)->default(null);
            $table->decimal('mayo',22,2)->default(null);
            $table->decimal('junio',22,2)->default(null);
            $table->decimal('julio',22,2)->default(null);
            $table->decimal('agosto',22,2)->default(null);
            $table->decimal('septiembre',22,2)->default(null);
            $table->decimal('octubre',22,2)->default(null);
            $table->decimal('noviembre',22,2)->default(null);
            $table->decimal('diciembre',22,2)->default(null);
            $table->decimal('total',22,2)->default(null);
            $table->integer('estado');
            $table->enum('tipo', ['Operativo', 'RH']);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('tipologia_conac',function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('tipo');
            $table->string('clave',1)->nullable();
            $table->string('descripcion',255);
            $table->string('clave_conac',1)->nullable();
            $table->string('descripcion_conac',255)->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('cierre_ejercicio_metas',function (Blueprint $table){
            $table->unique(['clv_upp','ejercicio']);	
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->enum('estatus',['Cerrado','Abierto'])->default(null);
            $table->unsignedInteger('ejercicio');
            $table->string('capturista',150)->nullable();
            $table->tinyInteger('activos')->default(1);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_user',45);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable();
        });

        Schema::create('uppautorizadascpnomina', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('cierre_ejercicio_claves',function (Blueprint $table){
            $table->unique(['clv_upp','ejercicio']);	
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->enum('estatus',['Cerrado','Abierto'])->default(null);
            $table->integer('ejercicio');
            $table->string('capturista',150)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_user',45);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable();
            $table->tinyInteger('activos')->default(1);
        });

        Schema::create('manuales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',70)->nullable(false);
            $table->string('ruta',255)->nullable(false);
            $table->json('usuarios')->nullable(false);
            $table->integer('estatus')->nullable(false);
            $table->string('usuario_creacion',20)->nullable(false);
            $table->string('usuario_modificacion',20)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->nullable(true)->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        //-------------------------- Tablas MML ---------------------------------

        Schema::create('mml_definicion_problema', function (Blueprint $table){
            $table->increments('id');
            //$table->unique(['clv_upp','clv_pp','ejercicio','ramo33']);
            $table->string('clv_upp')->nullable();
            $table->string('clv_pp',255);
            $table->string('poblacion_objetivo',255);
            $table->string('descripcion',255);
            $table->string('magnitud',255);
            $table->string('necesidad_atender',255);
            $table->integer('delimitacion_geografica');
            $table->string('region',3);
            $table->string('municipio',3);
            $table->string('localidad',3);
            $table->string('problema_central',255);
            $table->string('objetivo_central',255);
            $table->string('comentarios_upp',255);
            $table->integer('ejercicio');
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->tinyInteger('ramo33');
        });
        
        if (!Schema::hasTable('mml_definicion_problema_hist')) {
            Schema::create('mml_definicion_problema_hist', function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->string('clv_upp',4)->nullable();
                $table->string('clv_pp',255);
                $table->string('poblacion_objetivo',255);
                $table->string('descripcion',255);
                $table->string('magnitud',255);
                $table->string('necesidad_atender',255);
                $table->integer('delimitacion_geografica');
                $table->string('region',3);
                $table->string('municipio',3);
                $table->string('localidad',3);
                $table->string('problema_central',255);
                $table->string('objetivo_central',255);
                $table->string('comentarios_upp',255);
                $table->integer('ejercicio');
                $table->string('created_user',45)->nullable();
                $table->string('updated_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->tinyInteger('ramo33');
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('mml_actividades', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',4);
            $table->string('clv_ur',4);
            $table->string('clv_pp',5);
            $table->string('entidad_ejecutora',6);
            $table->string('area_funcional',16);
            $table->string('id_catalogo',255)->nullable();
            $table->string('nombre',255)->nullable();
            $table->integer('ejercicio');
            $table->string('created_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
        });

        if (!Schema::hasTable('mml_actividades_hist')) {
            Schema::create('mml_actividades_hist', function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->string('clv_upp',50);
                $table->string('entidad_ejecutora',6);
                $table->string('area_funcional',16);
                $table->string('id_catalogo',255)->nullable();
                $table->string('nombre',255)->nullable();
                $table->integer('ejercicio');
                $table->string('created_user',45)->nullable();
                $table->string('updated_user',45)->nullable();
                $table->string('deleted_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('mml_arbol_problema', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('problema_id');
            $table->string('clv_upp',4)->nullable();
            $table->string('clv_pp',5)->nullable();
            $table->enum('tipo',['Efecto','Causa'])->nullable();
            $table->integer('padre_id')->nullable();
            $table->string('indice',10)->nullable();
            $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable();
            $table->string('descripcion',255);
            $table->tinyInteger('ramo33');
            $table->integer('ejercicio');
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema')->onDelete('cascade');
        });

        if (!Schema::hasTable('mml_arbol_problema_hist')) {
            Schema::create('mml_arbol_problema_hist', function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->unsignedInteger('problema_id');
                $table->string('clv_upp',4)->nullable();
                $table->string('clv_pp',255)->nullable();
                $table->enum('tipo',['Efecto','Causa'])->nullable();
                $table->integer('padre_id')->nullable();
                $table->string('indice',10)->nullable();
                $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable();
                $table->string('descripcion',255);
                $table->integer('ejercicio');
                $table->string('created_user',45)->nullable();
                $table->string('updated_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->tinyInteger('ramo33');
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('mml_arbol_objetivos', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('problema_id');
            $table->string('clv_upp',4)->nullable();
            $table->string('clv_pp',5)->nullable();
            $table->enum('tipo',['Fin','Medio'])->nullable();
            $table->integer('padre_id')->nullable();
            $table->string('indice',10)->nullable();
            $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable();
            $table->string('descripcion',255);
            $table->integer('calificacion_id');
            $table->tinyInteger('seleccion_mir');
            $table->enum('tipo_indicador',['Componente','Actividad'])->nullable();
            $table->integer('ejercicio');
            $table->tinyInteger('ramo33');
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema')->onDelete('cascade');
        });

        if (!Schema::hasTable('mml_arbol_objetivos_hist')) {
            Schema::create('mml_arbol_objetivos_hist', function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->unsignedInteger('problema_id');
                $table->string('clv_upp',4)->nullable();
                $table->string('clv_pp',255)->nullable();
                $table->enum('tipo',['Fin','Medio'])->nullable();
                $table->integer('padre_id')->nullable();
                $table->string('indice',10)->nullable();
                $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable();
                $table->string('descripcion',255);
                $table->integer('calificacion_id');
                $table->tinyInteger('seleccion_mir');
                $table->enum('tipo_indicador',['Componente','Actividad'])->nullable();
                $table->integer('ejercicio');
                $table->string('created_user',45)->nullable();
                $table->string('updated_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->tinyInteger('ramo33');
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('mml_observaciones_pp', function (Blueprint $table){
            $table->unique(['clv_upp', 'clv_pp', 'ejercicio', 'problema_id', 'etapa'], 'llave_pp_observaciones');
            $table->increments('id');
            $table->string('clv_upp',4)->nullable();
            $table->string('clv_pp',5);
            $table->unsignedInteger('problema_id');
            $table->tinyInteger('etapa')->unsigned();
            $table->text('comentario')->nullable();
            $table->string('ruta',200)->nullable();
            $table->string('nombre',500)->nullable();
            $table->integer('ejercicio')->nullable();
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->tinyInteger('ramo33');
            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
        });

        if (!Schema::hasTable('mml_observaciones_pp_hist')) {
            Schema::create('mml_observaciones_pp_hist', function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->string('clv_upp',4)->nullable();
                $table->string('clv_pp',5);
                $table->unsignedInteger('problema_id');
                $table->tinyInteger('etapa')->unsigned();
                $table->text('comentario')->nullable();
                $table->string('ruta',200)->nullable();
                $table->string('nombre',500)->nullable();
                $table->integer('ejercicio')->nullable();
                $table->string('created_user',45)->nullable();
                $table->string('updated_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->tinyInteger('ramo33');
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('mml_objetivo_sectorial_estrategia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clv_objetivo_sectorial', 6);
            $table->text('objetivo_sectorial');
            $table->string('clv_estrategia', 9);
            $table->text('estrategia');
            $table->string('clv_cpladem_linea_accion', 12);
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        Schema::create('mml_mir',function (Blueprint $table){
            $table->increments('id');
            $table->string('entidad_ejecutora',6)->nullable();
            $table->string('area_funcional',16)->nullable();
            $table->string('clv_upp',4)->nullable();
            $table->string('clv_ur',4)->nullable();
            $table->string('clv_pp',5);
            $table->integer('nivel');
            $table->bigInteger('id_epp')->nullable();
            $table->integer('componente_padre')->nullable();
            $table->text('objetivo');
            $table->string('indicador',255);
            $table->string('definicion_indicador',255);
            $table->string('metodo_calculo',255);
            $table->text('descripcion_metodo');
            $table->integer('tipo_indicador');
            $table->integer('unidad_medida');
            $table->integer('dimension');
            $table->integer('comportamiento_indicador');
            $table->integer('frecuencia_medicion');
            $table->text('medios_verificacion');
            $table->string('lb_valor_absoluto',255);
            $table->string('lb_valor_relativo',255);
            $table->integer('lb_anio');
            $table->enum('lb_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
            $table->enum('lb_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
            $table->string('mp_valor_absoluto',255);
            $table->string('mp_valor_relativo',255);
            $table->integer('mp_anio');
            $table->integer('mp_anio_meta')->nullable();
            $table->enum('mp_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
            $table->enum('mp_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
            $table->text('supuestos');
            $table->text('estrategias');
            $table->integer('ejercicio');
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->tinyInteger('ramo33');
            $table->integer('desagregacion_geografica')->nullable();
        });

        if (!Schema::hasTable('mml_mir_hist')) {
            Schema::create('mml_mir_hist',function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->string('entidad_ejecutora',6)->nullable();
                $table->string('area_funcional',16)->nullable();
                $table->string('clv_upp',4)->nullable();
                $table->string('clv_ur',4)->nullable();
                $table->string('clv_pp',5);
                $table->integer('nivel');
                $table->bigInteger('id_epp')->nullable();
                $table->integer('componente_padre')->nullable();
                $table->text('objetivo');
                $table->string('indicador',255);
                $table->string('definicion_indicador',255);
                $table->string('metodo_calculo',255);
                $table->text('descripcion_metodo');
                $table->integer('tipo_indicador');
                $table->integer('unidad_medida');
                $table->integer('dimension');
                $table->integer('comportamiento_indicador');
                $table->integer('frecuencia_medicion');
                $table->text('medios_verificacion');
                $table->string('lb_valor_absoluto',255);
                $table->string('lb_valor_relativo',255);
                $table->integer('lb_anio');
                $table->enum('lb_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
                $table->enum('lb_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
                $table->string('mp_valor_absoluto',255);
                $table->string('mp_valor_relativo',255);
                $table->integer('mp_anio');
                $table->integer('mp_anio_meta')->nullable();
                $table->enum('mp_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
                $table->enum('mp_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'])->nullable();
                $table->text('supuestos');
                $table->text('estrategias');
                $table->integer('ejercicio');
                $table->string('created_user',45)->nullable();
                $table->string('updated_user',45)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->tinyInteger('ramo33');
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('mml_avance_etapas_pp', function (Blueprint $table){
            $table->increments('id');
            $table->unique(['clv_upp','clv_pp','ejercicio','ramo33']);
            $table->string('clv_upp',4)->nullable();
            $table->string('clv_pp',5);
            $table->tinyInteger('etapa_0');
            $table->tinyInteger('etapa_1');
            $table->tinyInteger('etapa_2');
            $table->tinyInteger('etapa_3');
            $table->tinyInteger('etapa_4');
            $table->tinyInteger('etapa_5');
            $table->unsignedInteger('estatus');
            $table->integer('ejercicio')->default(0);
            $table->string('nombre_minuta',100)->nullable();
            $table->string('ruta',100)->nullable();
            $table->string('extension',4)->nullable();
            $table->tinyInteger('ramo33');
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        Schema::create('mml_objetivos_desarrollo_sostenible', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_estrategia',9);
            $table->string('clv_plan_nacional',1);
            $table->string('plan_nacional',255);
            $table->string('clv_ods',2);
            $table->text('ods');
            $table->string('clv_objetivos_y_metas_ods',5);
            $table->text('objetivos_y_metas_ods');
            $table->string('created_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        Schema::create('mml_catalogos', function (Blueprint $table){
            $table->increments('id');
            $table->string('grupo',30);
            $table->string('valor',255);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
        });

        Schema::create('mml_cierre_ejercicio', function (Blueprint $table){
            $table->unique(['clv_upp','ejercicio']);	
            $table->increments('id');
            $table->string('clv_upp',4);
            $table->enum('estatus', ['Cerrado', 'Abierto']);
            $table->tinyInteger('statusm')->default(0);
            $table->integer('ejercicio');
            $table->string('capturista',150)->nullable();
            $table->string('created_user',45);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
        });

        Schema::create('mml_cremaa', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('id_mml_mir');
            $table->enum('claridad', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('relevancia', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('economia', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('monitoreable', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('adecuado', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('aportacion_marginal', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->string('justificacion',255);
            $table->date('serie');
            $table->string('responsable',150);
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->foreign('id_mml_mir')->references('id')->on('mml_mir');
        });

        Schema::create('mml_variable', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('id_mml_mir');
            $table->string('nombre',150);
            $table->string('descripcion',255);
            $table->string('unidad_medida',50);
            $table->string('medios_verificacion',200);
            $table->string('frecuencia',50);
            $table->string('desagregacion_geografica',50);
            $table->string('metodo_recopilacion_datos',50);
            $table->date('fecha_disponibilidad');
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->foreign('id_mml_mir')->references('id')->on('mml_mir');
        });

        Schema::create('mml_doc_enlaces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clv_upp',4);
            $table->string('id_usuario',200);
            $table->string('nombre',30);
            $table->string('ruta',200);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->default(NULL)->nullable();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable()->default(NULL);
            $table->string('deleted_user',45)->nullable()->default(NULL);
            $table->unique('clv_upp','id_usuario');
        });

        if (!Schema::hasTable('mml_minutas_historico')) {
            Schema::create('mml_minutas_historico', function (Blueprint $table) {
                $table->increments('id');
                $table->string('clv_upp',4);
                $table->string('clv_pp',4);
                $table->string('ejercicio',4);
                $table->tinyInteger('ramo33');
                $table->string('ruta_general',300);
                $table->string('nombre_minuta',200);
                $table->date('fecha_creacion');
                $table->tinyInteger('estatus');
                $table->string('username_create',45);
                $table->string('username_update',45)->nullable()->default(NULL);
                $table->string('username_delete',45)->nullable()->default(NULL);
                $table->timestamp('deleted_at')->default(NULL)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
        }

        Schema::create('metas',function (Blueprint $table){
            $table->increments('id');
           // $table->unique(['clv_actividad','clv_fondo','mir_id'],'clave_mir');	//??
           // $table->unique(['clv_actividad','clv_fondo','actividad_id'],'clave_actividad');	//??
            $table->string('clv_actividad',255)->unique()->nullable();
            $table->string('clv_fondo',2);
            $table->unsignedInteger('mir_id')->nullable();
            $table->unsignedInteger('actividad_id')->nullable();
            $table->enum('tipo_meta',['Operativo','RH'])->default('Operativo');
            $table->enum('tipo',['Acumulativa','Continua','Especial']);
            $table->unsignedInteger('beneficiario_id');
            $table->unsignedInteger('unidad_medida_id');
            $table->integer('cantidad_beneficiarios');
            $table->integer('enero')->default(null);
            $table->integer('febrero')->default(null);
            $table->integer('marzo')->default(null);
            $table->integer('abril')->default(null);
            $table->integer('mayo')->default(null);
            $table->integer('junio')->default(null);
            $table->integer('julio')->default(null);
            $table->integer('agosto')->default(null);
            $table->integer('septiembre')->default(null);
            $table->integer('octubre')->default(null);
            $table->integer('noviembre')->default(null);
            $table->integer('diciembre')->default(null);
            $table->integer('total')->default(null);
            $table->integer('estatus');
            $table->integer('ejercicio');
            $table->string('created_user',45);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios');
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida');
            $table->foreign('mir_id')->references('id')->on('mml_mir');
            $table->foreign('actividad_id')->references('id')->on('mml_actividades');
        });

        if (!Schema::hasTable('metas_hist')) {
            Schema::create('metas_hist',function (Blueprint $table){
                $table->integer('id_original');
                $table->integer('version');
                $table->string('clv_actividad',255)->unique()->nullable();
                $table->string('clv_fondo',2);
                $table->unsignedInteger('mir_id')->nullable();
                $table->integer('actividad_id')->nullable();
                $table->enum('tipo_meta',['Operativo','RH'])->default('Operativo');
                $table->enum('tipo',['Acumulativa','Continua','Especial']);
                $table->unsignedInteger('beneficiario_id');
                $table->unsignedInteger('unidad_medida_id');
                $table->integer('cantidad_beneficiarios');
                $table->integer('enero')->default(null);
                $table->integer('febrero')->default(null);
                $table->integer('marzo')->default(null);
                $table->integer('abril')->default(null);
                $table->integer('mayo')->default(null);
                $table->integer('junio')->default(null);
                $table->integer('julio')->default(null);
                $table->integer('agosto')->default(null);
                $table->integer('septiembre')->default(null);
                $table->integer('octubre')->default(null);
                $table->integer('noviembre')->default(null);
                $table->integer('diciembre')->default(null);
                $table->integer('total')->default(null);
                $table->integer('estatus');
                $table->integer('ejercicio');
                $table->string('created_user',45);
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->string('updated_user',45)->nullable();
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->string('deleted_user',45)->nullable();
                $table->softDeletes();
                $table->primary(['id_original', 'version', 'ejercicio']);
            });
        }

        Schema::create('sector_linea_accion',function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('linea_accion_id');
            $table->string('clv_sector',1);
            $table->string('sector',255);
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('linea_accion_num',8);
            $table->string('clv_linea_accion',2);
            $table->foreign('linea_accion_id')->references('id')->on('catalogo');
        });

        Schema::create('epp',function (Blueprint $table){
            $table->increments('id');
            $table->unique(['sector_publico_id','sector_publico_f_id','sector_economia_id','subsector_economia_id','ente_publico_id','upp_id','subsecretaria_id',
                            'ur_id','finalidad_id','funcion_id','subfuncion_id','eje_id','linea_accion_id','programa_sectorial_id','tipologia_conac_id','programa_id','subprograma_id',
                            'proyecto_id','ejercicio'],'llave_epp');	
            $table->unsignedInteger('sector_publico_id');
            $table->unsignedInteger('sector_publico_f_id');
            $table->unsignedInteger('sector_economia_id');
            $table->unsignedInteger('subsector_economia_id');
            $table->unsignedInteger('ente_publico_id');
            $table->unsignedInteger('upp_id');
            $table->unsignedInteger('subsecretaria_id');
            $table->unsignedInteger('ur_id');
            $table->unsignedInteger('finalidad_id');
            $table->unsignedInteger('funcion_id');
            $table->unsignedInteger('subfuncion_id');
            $table->unsignedInteger('eje_id');
            $table->unsignedInteger('linea_accion_id');
            $table->unsignedInteger('programa_sectorial_id');
            $table->unsignedInteger('tipologia_conac_id');
            $table->unsignedInteger('programa_id');
            $table->unsignedInteger('subprograma_id');
            $table->unsignedInteger('proyecto_id');
            $table->unsignedInteger('ejercicio');
            $table->tinyInteger('presupuestable')->default(false);
            $table->tinyInteger('con_mir');
            $table->tinyInteger('confirmado')->default(false);
            $table->tinyInteger('tipo_presupuesto')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->string('created_user',45);
        });

        Schema::create('proyectos_obra',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_proyecto_obra',6);
            $table->string('proyecto_obra',255);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable();
            $table->string('updated_user',45)->nullable();
            $table->string('created_user',45);
        });

        Schema::create('pp_identificadores',function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('epp_id');
            $table->unsignedInteger('clas_geo_id');
            $table->unsignedInteger('pos_pre_id');
            $table->unsignedInteger('fondo_id');
            $table->unsignedInteger('obra_id');
            $table->foreign('epp_id')->references('id')->on('epp');
            $table->foreign('clas_geo_id')->references('id')->on('clasificacion_geografica');
            $table->foreign('pos_pre_id')->references('id')->on('posicion_presupuestaria');
            $table->foreign('fondo_id')->references('id')->on('fondo');
            $table->foreign('obra_id')->references('id')->on('proyectos_obra');
        });

        Schema::create('tipo_actividad_upp', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->tinyInteger('Continua')->default(false);
            $table->tinyInteger('Acumulativa')->default(false);
            $table->tinyInteger('Especial')->default(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_user',45);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable();
        });

        Schema::create('rel_economica_administrativa', function (Blueprint $table){
            $table->increments('id');
            $table->string('clasificacion_administrativa',5);
            $table->string('clasificacion_economica',6);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        if (!Schema::hasTable('programacion_presupuesto_hist')) {
            Schema::create('programacion_presupuesto_hist', function(Blueprint $table){
                $table->increments('id');
                $table->integer('id_original');
                $table->integer('version');
                $table->string('clasificacion_administrativa',5);
                $table->string('entidad_federativa',2);
                $table->string('region',2);
                $table->string('municipio',3);
                $table->string('localidad',3);
                $table->string('upp',3);
                $table->string('subsecretaria',1);
                $table->string('ur',2);
                $table->string('finalidad',1);
                $table->string('funcion',1);
                $table->string('subfuncion',1);
                $table->string('eje',1);
                $table->string('linea_accion',2);
                $table->string('programa_sectorial',1);
                $table->string('tipologia_conac',1);
                $table->string('programa_presupuestario',2);
                $table->string('subprograma_presupuestario',3);
                $table->string('proyecto_presupuestario',3);
                $table->string('periodo_presupuestal',6);
                $table->string('posicion_presupuestaria',5);
                $table->string('tipo_gasto',1);
                $table->string('anio',2);
                $table->string('etiquetado',1);
                $table->string('fuente_financiamiento',1);
                $table->string('ramo',2);
                $table->string('fondo_ramo',2);
                $table->string('capital',1);
                $table->string('proyecto_obra',6);
                $table->integer('ejercicio')->nullable();
                $table->decimal('enero',22,2)->default(null);
                $table->decimal('febrero',22,2)->default(null);
                $table->decimal('marzo',22,2)->default(null);
                $table->decimal('abril',22,2)->default(null);
                $table->decimal('mayo',22,2)->default(null);
                $table->decimal('junio',22,2)->default(null);
                $table->decimal('julio',22,2)->default(null);
                $table->decimal('agosto',22,2)->default(null);
                $table->decimal('septiembre',22,2)->default(null);
                $table->decimal('octubre',22,2)->default(null);
                $table->decimal('noviembre',22,2)->default(null);
                $table->decimal('diciembre',22,2)->default(null);
                $table->decimal('total',22,2)->default(null);
                $table->integer('estado');
                $table->enum('tipo', ['Operativo', 'RH']);
                $table->string('created_user',45);
                $table->string('updated_user',45)->nullable();
                $table->string('deleted_user',45)->nullable();
                $table->timestamp('created_at');
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
            });
        }

        Schema::create('epp_aux', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_sector_publico')->nullable();
            $table->string('clv_sector_publico',6);
            $table->text('sector_publico');
            $table->integer('id_sector_publico_f')->nullable();
            $table->string('clv_sector_publico_f',6);
            $table->text('sector_publico_f');
            $table->integer('id_sector_economia')->nullable();
            $table->string('clv_sector_economia',6);
            $table->text('sector_economia');
            $table->integer('id_subsector_economia')->nullable();
            $table->string('clv_subsector_economia',6);
            $table->text('subsector_economia');
            $table->integer('id_ente_publico')->nullable();
            $table->string('clv_ente_publico',6);
            $table->text('ente_publico');
            $table->integer('id_upp')->nullable();
            $table->string('clv_upp',6);
            $table->text('upp');
            $table->integer('id_subsecretaria')->nullable();
            $table->string('clv_subsecretaria',6);
            $table->text('subsecretaria');
            $table->integer('id_ur')->nullable();
            $table->string('clv_ur',6);
            $table->text('ur');
            $table->integer('id_finalidad')->nullable();
            $table->string('clv_finalidad',6);
            $table->text('finalidad');
            $table->integer('id_funcion')->nullable();
            $table->string('clv_funcion',6);
            $table->text('funcion');
            $table->integer('id_subfuncion')->nullable();
            $table->string('clv_subfuncion',6);
            $table->text('subfuncion');
            $table->integer('id_eje')->nullable();
            $table->string('clv_eje',6);
            $table->text('eje');
            $table->integer('id_linea_accion')->nullable();
            $table->string('clv_linea_accion',6);
            $table->text('linea_accion');
            $table->integer('id_programa_sectorial')->nullable();
            $table->string('clv_programa_sectorial',6);
            $table->text('programa_sectorial');
            $table->integer('id_tipologia_conac')->nullable();
            $table->string('clv_tipologia_conac',6);
            $table->text('tipologia_conac');
            $table->integer('id_programa')->nullable();
            $table->string('clv_programa',6);
            $table->text('programa');
            $table->integer('id_subprograma')->nullable();
            $table->string('clv_subprograma',6);
            $table->text('subprograma');
            $table->integer('id_proyecto')->nullable();
            $table->string('clv_proyecto',6);
            $table->text('proyecto');
        });

        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',250)->nullable();
            $table->longText('valor')->nullable();
            $table->string('tipo',50)->nullable();
            $table->string('usuario_creacion',20)->nullable();
            $table->string('usuario_modificacion',20)->nullable();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue',191)->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('carga_masiva_estatus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_usuario')->nullable();
            $table->longText('cargapayload')->nullable();
            $table->tinyInteger('cargaMasClav')->default(0);
            $table->string('created_user',45)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('deleted_user',45)->nullable();
            $table->softDeletes();
        });

        Schema::create('ramo_33', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('id_ramo_fondo');
            $table->unsignedInteger('id_fondo_federal');
            $table->unsignedInteger('id_programa');
            $table->integer('ejercicio');
            $table->foreign('id_ramo_fondo')->references('id')->on('fondo');
            $table->foreign('id_fondo_federal')->references('id')->on('catalogo');
            $table->foreign('id_programa')->references('id')->on('catalogo');
        });

        //-----------------------------------Tablas SAPP----------------------------------

        Schema::create('sapp_cierre_ejercicio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clv_upp',3);
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
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
        });

        Schema::create('sapp_movimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('programacion_id')->nullable();
            $table->integer('ejercicio');
            $table->integer('mes');
            $table->integer('dia');
            $table->string('clv_upp',3);
            $table->string('clv_ur',2);
            $table->string('clv_programa',2);
            $table->string('clv_subprograma',3);
            $table->string('clv_proyecto',3);
            $table->string('fondo',9);
            $table->string('partida',6);
            $table->string('area_funcional',16);
            $table->string('centro_gestor',16);
            $table->string('clasificacion_administrativa',5);
            $table->string('proyecto_obra',6);
            $table->decimal('original_sapp',22,2);
            $table->decimal('ampliacion',22,2);
            $table->decimal('reduccion',22,2);
            $table->decimal('traspaso',22,2);
            $table->decimal('modificado',22,2);
            $table->decimal('apartado',22,2);
            $table->decimal('comprometido',22,2);
            $table->decimal('comprometido_cp',22,2);
            $table->decimal('devengado',22,2);
            $table->decimal('devengado_cp',22,2);
            $table->decimal('ejercido',22,2);
            $table->decimal('ejercido_cp',22,2);
            $table->decimal('pagado',22,2);
            $table->decimal('disponible',22,2);
            $table->tinyInteger('estatus')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
        });

        Schema::create('sapp_seguimiento', function (Blueprint $table) {
            $table->unique(['meta_id','clv_upp','clv_ur','clv_programa','clv_subprograma','clv_proyecto','ejercicio','mes'],'clave_seguimiento');	
            $table->increments('id');
            $table->integer('meta_id');
            $table->string('clv_upp',3);
            $table->string('clv_ur',2);
            $table->string('clv_programa',2);
            $table->string('clv_subprograma',3);
            $table->string('clv_proyecto',3);
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
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
        });

        Schema::create('sapp_acuse', function (Blueprint $table) {
            $table->unique(['clv_upp','ejercicio','mes']);
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->string('ruta',200);
            $table->string('nombre',500);
            $table->tinyInteger('firmado')->default(false);
            $table->integer('ejercicio');
            $table->tinyInteger('mes');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
        });

        Schema::create('sapp_enlaces', function (Blueprint $table) {
            $table->unique(['clv_upp','id_usuario']);
            $table->increments('id');
            $table->string('clv_upp',3);
            $table->integer('id_usuario');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
        });

        Schema::create('sapp_rel_metas_partidas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meta_id');
            $table->string('partida',6);
            $table->integer('ejercicio');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user',45);
            $table->string('updated_user',45)->nullable();
            $table->string('deleted_user',45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_permisos');
        Schema::dropIfExists('adm_users');
        Schema::dropIfExists('adm_grupos');
        Schema::dropIfExists('adm_rel_user_grupo');
        Schema::dropIfExists('adm_sistemas');
        Schema::dropIfExists('adm_rel_sistema_grupo');
        Schema::dropIfExists('adm_menus');
        Schema::dropIfExists('adm_rel_menu_grupo');
        Schema::dropIfExists('adm_funciones');
        Schema::dropIfExists('permisos_funciones');
        Schema::dropIfExists('adm_rel_funciones_grupos');
        Schema::dropIfExists('adm_bitacora');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('pp_identificadores');
        Schema::dropIfExists('ramo_33');
        Schema::dropIfExists('fondo');
        Schema::dropIfExists('techos_financieros');
        Schema::dropIfExists('programacion_presupuesto');
        Schema::dropIfExists('tipologia_conac');
        Schema::dropIfExists('metas');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('beneficiarios');
        Schema::dropIfExists('cierre_ejercicio_metas');
        Schema::dropIfExists('cierre_ejercicio_claves');
        Schema::dropIfExists('uppAutorizadasCPNomina');
        Schema::dropIfExists('clasificacion_administrativa');
        Schema::dropIfExists('clasificacion_geografica');
        Schema::dropIfExists('entidad_ejecutora');
        Schema::dropIfExists('posicion_presupuestaria');
        Schema::dropIfExists('epp');
        Schema::dropIfExists('sector_linea_accion');
        Schema::dropIfExists('catalogo');
        Schema::dropIfExists('subgrupos');
        Schema::dropIfExists('proyectos_obra');
        Schema::dropIfExists('tipo_actividad_upp');
        Schema::dropIfExists('rel_economica_administrativa');
        Schema::dropIfExists('programacion_presupuesto_hist');
        Schema::dropIfExists('mml_avance_etapas_pp');
        Schema::dropIfExists('mml_catalogos');
        Schema::dropIfExists('mml_objetivos_desarrollo_sostenible');
        Schema::dropIfExists('mml_arbol_problema');
        Schema::dropIfExists('mml_arbol_objetivos');
        Schema::dropIfExists('mml_observaciones_pp');
        Schema::dropIfExists('mml_definicion_problema');
        Schema::dropIfExists('mml_objetivo_sectorial_estrategia');
        Schema::dropIfExists('mml_mir');
        Schema::dropIfExists('epp_aux');
        Schema::dropIfExists('configuracion');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('carga_masiva_estatus');
        Schema::dropIfExists('mml_cremaa');
        Schema::dropIfExists('mml_variable');
        Schema::dropIfExists('mml_doc_enlaces');
        Schema::dropIfExists('sapp_cierre_ejercicio');
        Schema::dropIfExists('sapp_movimientos');
        Schema::dropIfExists('sapp_seguimiento');
        Schema::dropIfExists('sapp_acuse');
        Schema::dropIfExists('sapp_enlaces');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('manuales');
        Schema::dropIfExists('sapp_rel_metas_partidas');
    }
};
