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
        Schema::dropIfExists('mml_doc_enlaces');
        Schema::create('mml_doc_enlaces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clv_upp',4)->nullable(false);
            $table->string('id_usuario',200)->nullable(false);
            $table->string('nombre',30)->nullable(false);
            $table->string('ruta',200)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamp('deleted_at')->default(NULL)->nullable(true);
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true)->default(NULL);
            $table->string('deleted_user',45)->nullable(true)->default(NULL);
            $table->unique('clv_upp','id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mml_doc_enlaces');
    }
};
