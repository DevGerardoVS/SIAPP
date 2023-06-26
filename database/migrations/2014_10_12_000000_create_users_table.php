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
        Schema::create('cat_entes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cve_upp', 5);
            $table->string('nombre_upp', 400);
            $table->string('cve_ur', 5);
            $table->string('nombre_ur', 400);
            $table->string('cve_uo', 5);
            $table->string('nombre_uo', 400);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        }); 

        Schema::create('adm_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_ente')->unsigned()->nullable();
            $table->string('nombre', 150);
            $table->string('p_apellido', 80);
            $table->string('s_apellido', 80);
            $table->string('email')->unique();
            $table->string('celular', 20);
            $table->string('username', 80)->unique();
            $table->string('password', 200);
            $table->rememberToken();
            $table->tinyInteger('sudo')->default(0);
            $table->tinyInteger('estatus')->default(1);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_ente' )->references('id')->on('cat_entes');
        });

        //Tablas para la administracion de roles en el proyecto
        Schema::create('adm_grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_grupo', 100);
            $table->tinyInteger('estatus')->default(0);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('adm_rel_user_grupo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_grupo')->unsigned();
            $table->integer('id_usuario')->unsigned();
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
            $table->integer('id_grupo')->unsigned();
            $table->integer('id_sistema')->unsigned();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo')->references('id')->on('adm_grupos');
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas');
        });

        Schema::create('adm_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sistema')->unsigned();
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
        Schema::create('upp_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sistema')->unsigned();
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
            $table->integer('id_grupo')->unsigned();
            $table->integer('id_menu')->unsigned();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_grupo')->references('id')->on('adm_grupos');
            $table->foreign('id_menu')->references('id')->on('adm_menus');
        });

        Schema::create('adm_funciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sistema')->unsigned();

            $table->integer('id_menu')->unsigned();
            $table->string('modulo', 50);
            $table->string('funcion', 70);
            $table->string('tipo', 50);
            $table->longText('descripcion');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas');
            $table->foreign('id_menu')->references('id')->on('adm_menus');
        });
        Schema::create('upp_funciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sistema')->unsigned();

            $table->integer('id_menu')->unsigned();
            $table->string('modulo', 50);
            $table->string('funcion', 70);
            $table->string('tipo', 50);
            $table->longText('descripcion');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_sistema')->references('id')->on('adm_sistemas');
            $table->foreign('id_menu')->references('id')->on('adm_menus');
        });

        Schema::create('adm_rel_funciones_grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_grupo')->unsigned();
            $table->integer('id_funcion')->unsigned();
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
            $table->date('fecha_movimiento');$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('adm_configuracion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entorno', 15);
            $table->string('logo', 100);
            $table->string('escudo', 100);
            $table->string('sello', 100);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_rel_user_grupo');
        Schema::dropIfExists('adm_rel_sistema_grupo');
        Schema::dropIfExists('adm_rel_menu_grupo');
        Schema::dropIfExists('adm_rel_funciones_grupos');
        Schema::dropIfExists('adm_bitacora');
        Schema::dropIfExists('adm_configuracion');
        Schema::dropIfExists('adm_funciones');
        Schema::dropIfExists('adm_menus');
        Schema::dropIfExists('adm_grupos');
        Schema::dropIfExists('adm_users');
        Schema::dropIfExists('cat_entes');
        Schema::dropIfExists('adm_sistemas');
    }
};
