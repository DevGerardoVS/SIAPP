<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //se crea un trigger de update para la tabla catologo que actualiza la vista v_epp
        //si se requieren los campos que son de otras tablas agregar al final los ifs correspondientes
        DB::unprepared('
            CREATE TRIGGER update_vepp_catalogo
            AFTER UPDATE ON catalogo
            FOR EACH ROW
            BEGIN
                IF OLD.grupo_id = \'SECTOR PÚBLICO\' THEN
                    UPDATE v_epp
                    SET sector_publico = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_sector_publico = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'SECTOR PÚBLICO FINANCIERO/NO FINANCIERO\' THEN
                    UPDATE v_epp
                    SET sector_publico_f = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_sector_publico_f = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'SECTOR DE ECONOMIA\' THEN
                    UPDATE v_epp
                    SET sector_economia = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_sector_economia = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'SUBSECTOR DE ECONOMÍA\' THEN
                    UPDATE v_epp
                    SET subsector_economia = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_subsector_economia = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'ENTE PÚBLICO\' THEN
                    UPDATE v_epp
                    SET ente_publico = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_ente_publico = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'UNIDAD PROGRAMÁTICA PRESUPUESTAL\' THEN
                    UPDATE v_epp
                    SET upp = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_upp = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'SUBSECRETARÍA\' THEN
                    UPDATE v_epp
                    SET subsecretaria = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_subsecretaria = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'UNIDAD RESPONSABLE\' THEN
                    UPDATE v_epp
                    SET ur = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_ur = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'FINALIDAD\' THEN
                    UPDATE v_epp
                    SET finalidad = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_finalidad = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'FUNCIÓN\' THEN
                    UPDATE v_epp
                    SET funcion = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_funcion = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'SUBFUNCIÓN\' THEN
                    UPDATE v_epp
                    SET subfuncion = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_subfuncion = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'EJE\' THEN
                    UPDATE v_epp
                    SET eje = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_eje = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'LÍNEA DE ACCIÓN\' THEN
                    UPDATE v_epp
                    SET linea_accion = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_linea_accion = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'PROGRAMA SECTORIAL\' THEN
                    UPDATE v_epp
                    SET programa_sectorial = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_programa_sectorial = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'TIPOLOGÍA CONAC\' THEN
                    UPDATE v_epp
                    SET tipologia_conac = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_tipologia_conac = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'PROGRAMA PRESUPUESTARIO\' THEN
                    UPDATE v_epp
                    SET programa = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_programa = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'SUBPROGRAMA PRESUPUESTARIO\' THEN
                    UPDATE v_epp
                    SET subprograma = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_subprograma = OLD.clave;
                END IF;
                IF OLD.grupo_id = \'PROYECTO PRESUPUESTARIO\' THEN
                    UPDATE v_epp
                    SET proyecto = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_proyecto = OLD.clave;
                END IF;
            END;
        ');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_vepp_sector_publico');

    }
};
