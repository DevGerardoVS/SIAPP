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
        if (!Schema::hasColumn('mml_cremaa', 'valor_minimo')) {
            Schema::table('mml_cremaa', function (Blueprint $table) {
                $table->mediumInteger('valor_minimo')->after('responsable')->nullable(false)->default(0);
            });
        }
        if (!Schema::hasColumn('mml_cremaa', 'valor_maximo')) {
            Schema::table('mml_cremaa', function (Blueprint $table) {
                $table->mediumInteger('valor_maximo')->after('valor_minimo')->nullable(false)->default(0);
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
        //
    }
};
