DELIMITER //

CREATE TRIGGER delete_catalogo_on_upp
AFTER DELETE ON catalogo
FOR EACH ROW
BEGIN
    IF OLD.grupo_id = 'UNIDAD PROGRAMÁTICA PRESUPUESTAL' THEN

        -- Acciones en caso de delete 

        -- borrar cierre ejercicio claves
        DELETE FROM cierre_ejercicio_claves
        WHERE
            ejercicio = OLD.ejercicio
            AND clv_upp = OLD.clave;

        -- borrar cierre ejercicio metas
        DELETE FROM cierre_ejercicio_metas
        WHERE
            ejercicio = OLD.ejercicio
            AND clv_upp = OLD.clave;

        -- borrar cierre ejercicio mml
        DELETE FROM mml_cierre_ejercicio
        WHERE
            ejercicio = OLD.ejercicio
            AND clv_upp = OLD.clave;

        -- borrar cierre ejercicio sapp
        DELETE FROM sapp_cierre_ejercicio
        WHERE
            ejercicio = OLD.ejercicio
            AND clv_upp = OLD.clave;

    END IF;
END//

DELIMITER ;