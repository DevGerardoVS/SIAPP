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
        Schema::table('catalogo_hist', function (Blueprint $table) {
            if (!Schema::hasColumn('catalogo_hist', 'descripcion_larga')) {
                $table->string('descripcion_larga', 43);
            }
            if (!Schema::hasColumn('catalogo_hist', 'descripcion_corta')) {
                $table->string('descripcion_corta', 43);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogo_hist', function (Blueprint $table) {
            $table->dropColumn('descripcion_larga');
            $table->dropColumn('descripcion_corta');
        });
    }
};
