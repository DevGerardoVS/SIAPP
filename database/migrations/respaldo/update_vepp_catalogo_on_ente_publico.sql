DELIMITER //
CREATE TRIGGER update_vepp_catalogo_on_ente_publico
AFTER UPDATE ON catalogo
FOR EACH ROW
begin
DECLARE ente_publico INT;
DECLARE epp_id INT;
declare done int DEFAULT 0;
-- Declarar el cursor
DECLARE epp_cursor CURSOR FOR 
SELECT id FROM epp WHERE clasificacion_administrativa_id  = ente_publico;
 -- Declarar el handler para el final del cursor
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
IF OLD.grupo_id = 'ENTE PÚBLICO' THEN
-- Obtener el id de la entidad ejecutora
SELECT id INTO ente_publico 
FROM clasificacion_administrativa 
WHERE ente_publico_id = OLD.id
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
ente_publico = NEW.descripcion
where
ejercicio = OLD.ejercicio
and clv_ente_publico  = OLD.clave
and id = epp_id;
END LOOP;
-- Cerrar el cursor
close epp_cursor;
END IF;
END//

DELIMITER ;