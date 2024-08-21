DELIMITER //

CREATE TRIGGER update_vepp_catologo_on_upp
AFTER UPDATE ON catalogo
FOR EACH ROW
BEGIN
IF OLD.grupo_id = 'UNIDAD PROGRAMÁTICA PRESUPUESTAL' then
   UPDATE v_epp
   SET upp = NEW.descripcion
   WHERE ejercicio = OLD.ejercicio
   AND clv_upp = OLD.clave
   AND deleted_at IS NULL; 
END IF;
END//

DELIMITER ;