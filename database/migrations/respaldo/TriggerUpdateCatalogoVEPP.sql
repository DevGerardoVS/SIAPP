DELIMITER //

CREATE TRIGGER update_vepp_catalogo
AFTER UPDATE ON catalogo
FOR EACH ROW
BEGIN
                IF OLD.grupo_id = 'SUBFUNCIÓN' THEN
                    UPDATE v_epp
                    SET subfuncion = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_subfuncion = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'EJE' THEN
                    UPDATE v_epp
                    SET eje = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_eje = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'LÍNEA DE ACCIÓN' THEN
                    UPDATE v_epp
                    SET linea_accion = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_linea_accion = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'PROGRAMA SECTORIAL' THEN
                    UPDATE v_epp
                    SET programa_sectorial = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_programa_sectorial = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'TIPOLOGÍA CONAC' THEN
                    UPDATE v_epp
                    SET tipologia_conac = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_tipologia_conac = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'PROGRAMA PRESUPUESTARIO' THEN
                    UPDATE v_epp
                    SET programa = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_programa = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'SUBPROGRAMA PRESUPUESTARIO' THEN
                    UPDATE v_epp
                    SET subprograma = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_subprograma = OLD.clave;
                END IF;
                IF OLD.grupo_id = 'PROYECTO PRESUPUESTARIO' THEN
                    UPDATE v_epp
                    SET proyecto = NEW.descripcion
                    WHERE ejercicio = OLD.ejercicio
                    AND clv_proyecto = OLD.clave;
                END IF;
END//

DELIMITER ;