DELIMITER //
CREATE TRIGGER update_vepp_catalogo_on_sector_economia
AFTER UPDATE ON catalogo
FOR EACH ROW
begin
DECLARE sector_economia INT;
DECLARE epp_id INT;
declare done int DEFAULT 0;
-- Declarar el cursor
DECLARE epp_cursor CURSOR FOR 
SELECT id FROM epp WHERE clasificacion_administrativa_id  = sector_economia;
 -- Declarar el handler para el final del cursor
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
IF OLD.grupo_id = 'SECTOR DE ECONOMIA' THEN
-- Obtener el id de la entidad ejecutora
SELECT id INTO sector_economia 
FROM clasificacion_administrativa 
WHERE sector_economia_id = OLD.id
and ejercicio = OLD.ejercicio
and deleted_at is null;
-- Abrir el cursor
OPEN epp_cursor;     
-- Iterar sobre los resultados del cursor
read_loop: LOOP
FETCH epp_cursor INTO epp_id;
IF done THEN
LEAVE read_loop;
END IF;
 -- Actualizar la tabla v_epp usando el valor obtenido de epp_id
update
v_epp
set
sector_economia = NEW.descripcion
where
ejercicio = OLD.ejercicio
and clv_sector_economia  = OLD.clave
and id = epp_id;
END LOOP;
-- Cerrar el cursor
close epp_cursor;
END IF;
END//

DELIMITER ;