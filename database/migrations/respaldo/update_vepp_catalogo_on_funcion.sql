DELIMITER //

CREATE TRIGGER update_vepp_catalogo_on_funcion
AFTER UPDATE ON catalogo
FOR EACH ROW
BEGIN
    DECLARE funcion_id INT;
    DECLARE epp_id INT;
    DECLARE done INT DEFAULT 0;

    -- Declarar el cursor para obtener los ids de clasficacion_funcional y epp
    DECLARE funcion_epp_cursor CURSOR FOR 
    SELECT cf.id, e.id 
    FROM clasificacion_funcional as cf
    JOIN epp as e ON e.clasificacion_funcional_id = cf.id
    WHERE cf.funcion_id = OLD.id
    and  cf.ejercicio=old.ejercicio
    and  cf.deleted_at IS NULL;

    -- Declarar el handler para el final del cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    IF OLD.grupo_id = 'FUNCIÓN' THEN
        -- Abrir el cursor
        OPEN funcion_epp_cursor;

        -- Iterar sobre los resultados del cursor
        read_loop: LOOP
            FETCH funcion_epp_cursor INTO funcion_id, epp_id;
            IF done THEN
                LEAVE read_loop;
            END IF;

            -- Actualizar la tabla v_epp usando el valor obtenido de epp_id
            UPDATE v_epp
            SET funcion = NEW.descripcion
            WHERE ejercicio = OLD.ejercicio
            AND clv_funcion = OLD.clave
            AND id = epp_id;
        END LOOP;

        -- Cerrar el cursor
        CLOSE funcion_epp_cursor;
    END IF;
END//

DELIMITER ;