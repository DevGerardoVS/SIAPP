<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Epp;
use App\Models\v_epp;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $epp = Epp::where('deleted_at',null)->get();
        foreach ($epp as $key) {
            V_epp::where([
                'id'=>$key->id
                ])->update([
                    'confirmado'=> $key->confirmado,
                    'updated_user'=> $key->updated_user,
                ]);
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
