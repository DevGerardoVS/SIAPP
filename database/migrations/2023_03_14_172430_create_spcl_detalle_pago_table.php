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
        Schema::create('spcl_detalle_pago', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_concesion',20)->nullable(false);
            $table->text('detalle_conceptos')->nullable(false);
            $table->text('convenio_bancos')->nullable(false);
            $table->double('importe_total')->nullable(false);
            $table->string('linea_captura',50)->nullable(false);
            $table->string('orden_pago',50)->nullable(false);
            $table->string('moneda',4)->nullable(false);
            $table->date('fecha_vencimiento',3)->nullable(false);
            $table->double('importe_concesion',3)->nullable(false);
            $table->double('importe_refrendo',3)->nullable(false);
            $table->string('estatus_pago',3)->nullable(false);
            $table->string('ejercicio',5)->nullable(false);
            
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->nullable(true)->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('no_concesion')->references('no_concesion')->on('spcl_detalle_concesion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spcl_detalle_pago');
    }
};

