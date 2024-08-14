DELIMITER //

CREATE TRIGGER insert_vepp_update_mml_avance_etapas_pp
AFTER
INSERT
	ON
	v_epp
FOR EACH ROW
BEGIN
-- Nuestra variable local para almacenar el resultado de la consulta
DECLARE record_exist INT;
-- Query para ver si ya existe un registro
SELECT
	count(*)
INTO
	record_exist
FROM
	mml_avance_etapas_pp
WHERE
	clv_upp = NEW.clv_upp
	AND clv_pp = NEW.clv_programa
	AND ejercicio = NEW.ejercicio
	AND deleted_at IS NULL;
-- Verificacion e inserción
   IF record_exist = 0 THEN
    INSERT
	INTO
	mml_avance_etapas_pp (ejercicio,
	clv_upp,
	clv_pp,
	etapa_0,
	etapa_1,
	etapa_2,
	etapa_3,
	etapa_4,
	etapa_5,
	estatus,
	ramo33,
	created_At,
	created_user)
VALUES (NEW.ejercicio,
NEW.clv_upp,
NEW.clv_programa,
0,
0,
0,
0,
0,
0,
0,
0,
NOW(),
'SYSTEM');
END IF;

END//

DELIMITER ;