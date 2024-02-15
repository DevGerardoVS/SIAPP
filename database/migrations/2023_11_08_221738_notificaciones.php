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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->Integer('id_usuario')->nullable(true);
            $table->Integer('id_sistema')->nullable(true);
            $table->json('payload')->nullable(true);
            $table->tinyInteger('status')->nullable(false)->default(0);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
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
        Schema::dropIfExists('notificaciones');

    }
};
