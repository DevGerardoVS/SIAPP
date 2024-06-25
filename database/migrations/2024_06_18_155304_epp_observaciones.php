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
        if (!Schema::hasTable('epp_observaciones')) {
            Schema::create('epp_observaciones', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_epp');
                $table->integer('etapa');
                $table->string('observacion',500);
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();

                $table->foreign('id_epp')->references('id')->on('epp');
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
        Schema::dropIfExists('epp_observaciones');
    }
};
