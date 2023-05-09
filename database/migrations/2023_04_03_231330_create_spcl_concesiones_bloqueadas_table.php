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
        Schema::create('spcl_concesiones_bloqueadas', function (Blueprint $table) {
            $table->id();
            $table->string('no_concesion',25)->unique()->nullable(false);
            $table->string('no_placas',10)->nullable(true);
            $table->string('no_serie_vehiculo',30)->nullable(true);
            $table->string('estatus',30)->nullable(false);
            $table->string('Obsevaciones',250)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->nullable(true)->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spcl_concesiones_bloqueadas');
    }
};
