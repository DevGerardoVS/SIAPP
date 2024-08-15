DELIMITER //

CREATE TRIGGER insert_cierres_on_upp
AFTER INSERT ON catalogo
FOR EACH ROW
BEGIN
    IF NEW.grupo_id = 'UNIDAD PROGRAMÁTICA PRESUPUESTAL' then
        -- Insert en cierre_ejercicio_claves
        INSERT INTO cierre_ejercicio_claves (
            clv_upp,
            estatus,
            ejercicio,
            activos,
            created_at,
            created_user
        ) VALUES (
            NEW.clave,
            'Abierto',
            NEW.ejercicio,
            1,
            NOW(),
            'SYSTEM'
        );
       -- Insert en cierre_ejercicio_metas
               INSERT INTO cierre_ejercicio_metas (
            clv_upp,
            estatus,
            ejercicio,
            activos,
            created_at,
            created_user
        ) VALUES (
            NEW.clave,
            'Abierto',
            NEW.ejercicio,
            1,
            NOW(),
            'SYSTEM'
        );
        -- Insert en mml_cierre ejercicio
         insert into mml_cierre_ejercicio (
         clv_upp,
         estatus,
         statusm,
         ejercicio,
         created_at,
         created_user 
         ) values (
         NEW.clave,
         'Abierto',
         0,
         NEW.ejercicio,
         NOW(),
         'SYSTEM'
         );
         -- Insert en sapp_cierre_ejercicio
         insert into sapp_cierre_ejercicio (
         clv_upp,
         ejercicio,
         created_user,
         created_at 
         ) values (
          new.clave,
          new.ejercicio,
          'SYSTEM',
          now()
         ); 
    END IF;
END//

DELIMITER ;