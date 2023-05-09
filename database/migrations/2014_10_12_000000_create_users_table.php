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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',50)->nullable(false);
            $table->string('apellidoP',50)->nullable(true);
            $table->string('apellidoM',50)->nullable(true);
            $table->string('username',15)->nullable(false);
            $table->string('password',200)->nullable(false);
            $table->string('email',100)->unique()->nullable(false);
            $table->string('telefono',10)->nullable(true);
            $table->rememberToken()->nullable(true);
            $table->string('usuario_creacion',20)->nullable(false);
            $table->string('usuario_modificacion',20)->nullable(true);
            $table->unsignedBigInteger('organismo_id')->nullable(true);
            $table->integer('perfil_id')->nullable(false);
            $table->boolean('estatus')->nullable(false)->default('1');
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
        Schema::dropIfExists('users');
    }
};
