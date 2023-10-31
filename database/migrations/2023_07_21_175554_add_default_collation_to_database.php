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
        Schema::table('database', function (Blueprint $table) {
            Schema::connection(env('DB_CONNECTION'))->getConnection()->statement('ALTER DATABASE `'.env('DB_DATABASE').'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }); 
    }
};
