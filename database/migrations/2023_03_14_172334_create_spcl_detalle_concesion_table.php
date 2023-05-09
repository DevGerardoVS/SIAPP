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
    public function up(): void
    {
        Schema::create('spcl_detalle_concesion', function (Blueprint $table) {
            // $table->increments('id');
            $table->string('no_concesion',25)->primary()->nullable(false);
            $table->string('email',120)->nullable(false);
            $table->string('telefono',15)->nullable(true);
            $table->string('objeto_contrato',30)->nullable(false);
            $table->string('cuenta_contrato',20)->nullable(false);
            $table->string('interlocutor',20)->nullable(false);
            $table->string('rfc',20)->nullable(false);
            $table->string('propietario',250)->nullable(false);
            $table->string('no_placas',10)->nullable(false);
            $table->string('no_serie_vehiculo',30)->nullable(false);
            $table->string('grupo',40)->nullable(false);
            $table->string('tipo_servicio',80)->nullable(false);
            $table->string('modalidad',80)->nullable(false);
            $table->string('estatus',30)->nullable(false);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(true);

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->nullable(true)->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spcl_detalle_concesion');
    }
};


