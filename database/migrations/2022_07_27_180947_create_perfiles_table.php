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
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',200)->nullable(false);
            $table->boolean('estatus')->nullable(false);
            $table->text('permisos')->nullable(true);
            $table->text('menu')->nullable(false);
            $table->char('tipo_perfil',1)->nullable(true);
            $table->string('usuario_creacion',20)->nullable(false);
            $table->string('usuario_modificacion',20)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfiles');
    }
};
