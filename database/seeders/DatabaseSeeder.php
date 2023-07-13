<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\catalogos\CatPermisos;
use App\Models\administracion\Menu;
use App\Models\administracion\Sistema;
use App\Models\administracion\Funciones;
use App\Models\administracion\Grupo;
use App\Models\administracion\UsuarioGrupo;
use App\Models\administracion\SistemaGrupo;
use App\Models\administracion\MenuGrupo;
use App\Models\administracion\Permisos;
use App\Models\administracion\PermisosUpp;

use DB;

class DatabaseSeeder extends Seeder
{
        protected $cat_permisos = array(
           ['id'=>1,'nombre'=>'Carga masiva'],
           ['id'=>2,'nombre'=>'Consultar obra'],
           ['id'=>3,'nombre'=>'Registrar obra'],
           ['id'=>4,'nombre'=>'Descargar oficio']
        );
        protected $grupos = array(
            ['id' => 1, 'nombre_grupo' => 'Administrador', 'estatus' => 0],
            ['id' => 2, 'nombre_grupo' => 'Gobdigital', 'estatus' => 0],
            ['id' => 3, 'nombre_grupo' => 'Auditor', 'estatus' => 0],
            ['id' => 4, 'nombre_grupo' => 'Upp', 'estatus' => 0],
            ['id' => 5, 'nombre_grupo' => 'Delegacion', 'estatus' => 0]
        );

    protected $cat_users = array(
        ['id' => 1, 'id_grupo' => 2, 'nombre' => 'Gob', 'p_apellido' => 'Digital', 's_apellido' => 'siapp', 'celular' => '00-00-00-00-00', 'email' => 'control@gmail.com', 'username' => 'control', 'password' => 'CasaMata', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 2, 'id_grupo' => 1, 'nombre' => 'sudo', 'p_apellido' => 'admin', 's_apellido' => 'sedj', 'celular' => '00-00-00-00-00', 'email' => 'prueba1@gmail.com', 'username' => 'administrador', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 3, 'id_grupo' => 1, 'nombre' => 'Francisco', 'p_apellido' => 'Méndez', 's_apellido' => 'Chávez', 'celular' => '44-32-21-90-95', 'email' => 'pacomendez2308@gmail.com', 'username' => 'depExpedientes', 'password' => 'depExpedientes.22', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 4, 'id_grupo' => 3, 'nombre' => 'auditor', 'p_apellido' => 'Prota', 's_apellido' => 'Ponce', 'celular' => '44-32-21-90-95', 'email' => 'pruebas@gmail.com', 'username' => 'Auditor', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '007'],
        ['id' => 5, 'id_grupo' => 1, 'nombre' => 'UnidadR', 'p_apellido' => 'u', 's_apellido' => 'pp', 'celular' => '44-32-21-90-95', 'email' => 'upp_user@gmail.com', 'username' => 'upp', 'password' => 'valida23', 'sudo' => 0, 'clv_upp' => '002'],
        ['id' => 6, 'id_grupo' => 1, 'nombre' => 'Luis', 'p_apellido' => 'Pardo', 's_apellido' => 'Escutia', 'celular' => '4400000081', 'email' => 'rosario.escutia+81@correo.michoacan.gob.mx', 'username' => 'LPARDO', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 7, 'id_grupo' => 2, 'nombre' => 'Mario', 'p_apellido' => 'Delgado', 's_apellido' => 'Murillo', 'celular' => '4400000082', 'email' => 'rosario.escutia+82@correo.michoacan.gob.mx', 'username' => 'DELEGACION', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 8, 'id_grupo' => 1, 'nombre' => 'Hugo', 'p_apellido' => 'Arechiga', 's_apellido' => 'Dos Santos', 'celular' => '4400000083', 'email' => 'rosario.escutia+83@correo.michoacan.gob.mx', 'username' => 'HUGOA', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 9, 'id_grupo' => 1, 'nombre' => 'Luis', 'p_apellido' => 'Leon', 's_apellido' => 'Tres', 'celular' => '4400000084', 'email' => 'rosario.escutia+84@correo.michoacan.gob.mx', 'username' => 'LALEON', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => NULL],
        ['id' => 10, 'id_grupo' => 4, 'nombre' => 'JORGE', 'p_apellido' => 'RESÉNDIZ', 's_apellido' => 'GARCÍA', 'email' => 'rosario.escutia+2@correo.michoacan.gob.mx', 'celular' => '4400000002', 'username' => 'UPP002', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '002'],
        ['id' => 11, 'id_grupo' => 4, 'nombre' => 'JULIETA', 'p_apellido' => 'GARCÍA', 's_apellido' => 'ZEPEDA', 'email' => 'rosario.escutia+1@correo.michoacan.gob.mx', 'celular' => '4400000001', 'username' => 'UPP001', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '001'],
        ['id' => 12, 'id_grupo' => 4, 'nombre' => 'RAÚL', 'p_apellido' => 'ZEPEDA', 's_apellido' => 'VILLASEÑOR', 'email' => 'rosario.escutia+53@correo.michoacan.gob.mx', 'celular' => '4400000053', 'username' => 'UPP003', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '003'],
        ['id' => 13, 'id_grupo' => 4, 'nombre' => 'CARLOS', 'p_apellido' => 'TORRES', 's_apellido' => 'PIÑA', 'email' => 'rosario.escutia+54@correo.michoacan.gob.mx', 'celular' => '4400000054', 'username' => 'UPP006', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '006'],
        ['id' => 14, 'id_grupo' => 4, 'nombre' => 'LUIS', 'p_apellido' => 'NAVARRO', 's_apellido' => 'GARCÍA', 'email' => 'rosario.escutia+72@correo.michoacan.gob.mx', 'celular' => '4400000072', 'username' => 'UPP007', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '007'],
        ['id' => 15, 'id_grupo' => 4, 'nombre' => 'ROGELIO', 'p_apellido' => 'ZARAZÚA', 's_apellido' => 'SÁNCHEZ', 'email' => 'rosario.escutia+55@correo.michoacan.gob.mx', 'celular' => '4400000055', 'username' => 'UPP008', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '008'],
        ['id' => 16, 'id_grupo' => 4, 'nombre' => 'CUAUHTÉMOC', 'p_apellido' => 'RAMÍREZ', 's_apellido' => 'ROMERO', 'email' => 'rosario.escutia+56@correo.michoacan.gob.mx', 'celular' => '4400000056', 'username' => 'UPP009', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '009'],
        ['id' => 17, 'id_grupo' => 4, 'nombre' => 'CLAUDIO', 'p_apellido' => 'MÉNDEZ', 's_apellido' => 'FERNÁNDEZ', 'email' => 'rosario.escutia+42@correo.michoacan.gob.mx', 'celular' => '4400000042', 'username' => 'UPP010', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '010'],
        ['id' => 18, 'id_grupo' => 4, 'nombre' => 'ROBERTO E.', 'p_apellido' => 'MONROY', 's_apellido' => 'GARCÍA', 'email' => 'rosario.escutia+44@correo.michoacan.gob.mx', 'celular' => '4400000044', 'username' => 'UPP011', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '011'],
        ['id' => 19, 'id_grupo' => 4, 'nombre' => 'GABRIELA DESIREÉ', 'p_apellido' => 'MOLINA', 's_apellido' => 'AGUILAR', 'email' => 'rosario.escutia+38@correo.michoacan.gob.mx', 'celular' => '4400000038', 'username' => 'UPP012', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '012'],
        ['id' => 20, 'id_grupo' => 4, 'nombre' => 'MARÍA TERESA', 'p_apellido' => 'MORA', 's_apellido' => 'COVARRUBIAS', 'email' => 'rosario.escutia+41@correo.michoacan.gob.mx', 'celular' => '4400000041', 'username' => 'UPP014', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '014'],
        ['id' => 21, 'id_grupo' => 4, 'nombre' => 'JOSE ALFREDO', 'p_apellido' => 'ORTEGA', 's_apellido' => 'REYES', 'email' => 'rosario.escutia+57@correo.michoacan.gob.mx', 'celular' => '4400000057', 'username' => 'UPP016', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '016'],
        ['id' => 22, 'id_grupo' => 4, 'nombre' => 'ELÍAS', 'p_apellido' => 'IBARRA', 's_apellido' => 'TORRES', 'email' => 'rosario.escutia+58@correo.michoacan.gob.mx', 'celular' => '4400000058', 'username' => 'UPP017', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '017'],
        ['id' => 23, 'id_grupo' => 4, 'nombre' => 'AZUCENA', 'p_apellido' => 'MARIN', 's_apellido' => 'CORREA', 'email' => 'rosario.escutia+40@correo.michoacan.gob.mx', 'celular' => '4400000040', 'username' => 'UPP019', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '019'],
        ['id' => 24, 'id_grupo' => 4, 'nombre' => 'GIULIANNA', 'p_apellido' => 'BUGARINI', 's_apellido' => 'TORRES', 'email' => 'rosario.escutia+3@correo.michoacan.gob.mx', 'celular' => '4400000003', 'username' => 'UPP020', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '020'],
        ['id' => 25, 'id_grupo' => 4, 'nombre' => 'TAMARA', 'p_apellido' => 'SOSA', 's_apellido' => 'ALANÍS', 'email' => 'rosario.escutia+45@correo.michoacan.gob.mx', 'celular' => '4400000045', 'username' => 'UPP021', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '021'],
        ['id' => 26, 'id_grupo' => 4, 'nombre' => 'ALAN MARTIN', 'p_apellido' => 'MARTÍNEZ', 's_apellido' => 'MARROQUIN', 'email' => 'rosario.escutia+4@correo.michoacan.gob.mx', 'celular' => '4400000004', 'username' => 'UPP022', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '022'],
        ['id' => 27, 'id_grupo' => 4, 'nombre' => 'JOSUÉ ADRIÁN', 'p_apellido' => 'ORTIZ', 's_apellido' => 'CALDERÓN', 'email' => 'rosario.escutia+5@correo.michoacan.gob.mx', 'celular' => '4400000005', 'username' => 'UPP023', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '023'],
        ['id' => 28, 'id_grupo' => 4, 'nombre' => 'MARCO ANTONIO', 'p_apellido' => 'FLORES', 's_apellido' => 'MEJÍA', 'email' => 'rosario.escutia+79@correo.michoacan.gob.mx', 'celular' => '4400000079', 'username' => 'UPP024', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '024'],
        ['id' => 29, 'id_grupo' => 4, 'nombre' => 'JOSUÉ ADRIÁN', 'p_apellido' => 'ORTIZ', 's_apellido' => 'CALDERÓN', 'email' => 'rosario.escutia+46@correo.michoacan.gob.mx', 'celular' => '4400000046', 'username' => 'UPP025', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '025'],
        ['id' => 30, 'id_grupo' => 4, 'nombre' => 'CASTOR', 'p_apellido' => 'ESTRADA', 's_apellido' => 'ROBLES', 'email' => 'rosario.escutia+6@correo.michoacan.gob.mx', 'celular' => '4400000006', 'username' => 'UPP031', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '031'],
        ['id' => 31, 'id_grupo' => 4, 'nombre' => 'CESAR ERWIN', 'p_apellido' => 'SÁNCHEZ', 's_apellido' => 'CORIA', 'email' => 'rosario.escutia+7@correo.michoacan.gob.mx', 'celular' => '4400000007', 'username' => 'UPP032', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '032'],
        ['id' => 32, 'id_grupo' => 4, 'nombre' => 'RAÚL', 'p_apellido' => 'MORÓN', 's_apellido' => 'VIDAL', 'email' => 'rosario.escutia+59@correo.michoacan.gob.mx', 'celular' => '4400000059', 'username' => 'UPP033', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '033'],
        ['id' => 33, 'id_grupo' => 4, 'nombre' => 'SERGIO', 'p_apellido' => 'PIMENTEL', 's_apellido' => 'MENDOZA', 'email' => 'rosario.escutia+8@correo.michoacan.gob.mx', 'celular' => '4400000008', 'username' => 'UPP035', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '035'],
        ['id' => 34, 'id_grupo' => 4, 'nombre' => 'OSCAR', 'p_apellido' => 'CELIS', 's_apellido' => 'SILVA', 'email' => 'rosario.escutia+9@correo.michoacan.gob.mx', 'celular' => '4400000009', 'username' => 'UPP036', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '036'],
        ['id' => 35, 'id_grupo' => 4, 'nombre' => 'JULIO CESAR', 'p_apellido' => 'MEDINA', 's_apellido' => 'ÁVILA', 'email' => 'rosario.escutia+10@correo.michoacan.gob.mx', 'celular' => '4400000010', 'username' => 'UPP037', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '037'],
        ['id' => 36, 'id_grupo' => 4, 'nombre' => 'YARABÍ', 'p_apellido' => 'ÁVILA', 's_apellido' => 'GONZÁLEZ', 'email' => 'rosario.escutia+77@correo.michoacan.gob.mx', 'celular' => '4400000077', 'username' => 'UPP038', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '038'],
        ['id' => 37, 'id_grupo' => 4, 'nombre' => 'ÓSCAR', 'p_apellido' => 'CELIS', 's_apellido' => 'SILVA', 'email' => 'rosario.escutia+60@correo.michoacan.gob.mx', 'celular' => '4400000060', 'username' => 'UPP040', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '040'],
        ['id' => 38, 'id_grupo' => 4, 'nombre' => 'IGNACIO', 'p_apellido' => 'HURTADO', 's_apellido' => 'GOMEZ', 'email' => 'rosario.escutia+11@correo.michoacan.gob.mx', 'celular' => '4400000011', 'username' => 'UPP041', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '041'],
        ['id' => 39, 'id_grupo' => 4, 'nombre' => 'ALMA ROSA', 'p_apellido' => 'BAHENA', 's_apellido' => 'VILLALOBOS', 'email' => 'rosario.escutia+12@correo.michoacan.gob.mx', 'celular' => '4400000012', 'username' => 'UPP042', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '042'],
        ['id' => 40, 'id_grupo' => 4, 'nombre' => 'HUGO ALBERTO', 'p_apellido' => 'GAMA', 's_apellido' => 'CORIA', 'email' => 'rosario.escutia+13@correo.michoacan.gob.mx', 'celular' => '4400000013', 'username' => 'UPP044', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '044'],
        ['id' => 41, 'id_grupo' => 4, 'nombre' => 'DAVID', 'p_apellido' => 'MENDOZA', 's_apellido' => 'ARMAS', 'email' => 'rosario.escutia+14@correo.michoacan.gob.mx', 'celular' => '4400000014', 'username' => 'UPP045', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '045'],
        ['id' => 42, 'id_grupo' => 4, 'nombre' => 'MANUEL ARTURO', 'p_apellido' => 'CHÁVEZ', 's_apellido' => 'CARMONA', 'email' => 'rosario.escutia+47@correo.michoacan.gob.mx', 'celular' => '4400000047', 'username' => 'UPP046', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '046'],
        ['id' => 43, 'id_grupo' => 4, 'nombre' => 'CRISTINA', 'p_apellido' => 'PORTILLO', 's_apellido' => 'AYALA', 'email' => 'rosario.escutia+15@correo.michoacan.gob.mx', 'celular' => '4400000015', 'username' => 'UPP047', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '047'],
        ['id' => 44, 'id_grupo' => 4, 'nombre' => 'ROBERTO', 'p_apellido' => 'ANDRADE', 's_apellido' => 'FERNÁNDEZ', 'email' => 'rosario.escutia+61@correo.michoacan.gob.mx', 'celular' => '4400000061', 'username' => 'UPP048', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '048'],
        ['id' => 45, 'id_grupo' => 4, 'nombre' => 'ROSENDO ANTONIO', 'p_apellido' => 'CARO', 's_apellido' => 'GÓMEZ', 'email' => 'rosario.escutia+16@correo.michoacan.gob.mx', 'celular' => '4400000016', 'username' => 'UPP049', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '049'],
        ['id' => 46, 'id_grupo' => 4, 'nombre' => 'ALFREDO', 'p_apellido' => 'RAMÍREZ', 's_apellido' => 'BEDOLLA', 'email' => 'rosario.escutia+17@correo.michoacan.gob.mx', 'celular' => '4400000017', 'username' => 'UPP050', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '050'],
        ['id' => 47, 'id_grupo' => 4, 'nombre' => 'DAVID', 'p_apellido' => 'ALFARO', 's_apellido' => 'GARCÉS', 'email' => 'rosario.escutia+18@correo.michoacan.gob.mx', 'celular' => '4400000018', 'username' => 'UPP051', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '051'],
        ['id' => 48, 'id_grupo' => 4, 'nombre' => 'OSVALDO', 'p_apellido' => 'RUIZ', 's_apellido' => 'RAMÍREZ', 'email' => 'rosario.escutia+19@correo.michoacan.gob.mx', 'celular' => '4400000019', 'username' => 'UPP052', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '052'],
        ['id' => 49, 'id_grupo' => 4, 'nombre' => 'GRACIELA CARMINA', 'p_apellido' => 'ANDRADE GARCÍA', 's_apellido' => 'PELÁEZ', 'email' => 'rosario.escutia+62@correo.michoacan.gob.mx', 'celular' => '4400000062', 'username' => 'UPP053', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '053'],
        ['id' => 50, 'id_grupo' => 4, 'nombre' => 'VÍCTOR MANUEL', 'p_apellido' => 'BÁEZ', 's_apellido' => 'CEJA', 'email' => 'rosario.escutia+48@correo.michoacan.gob.mx', 'celular' => '4400000048', 'username' => 'UPP054', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '054'],
        ['id' => 51, 'id_grupo' => 4, 'nombre' => 'PAULA EDITH', 'p_apellido' => 'ESPINOSA', 's_apellido' => 'BARRIENTOS', 'email' => 'rosario.escutia+49@correo.michoacan.gob.mx', 'celular' => '4400000049', 'username' => 'UPP055', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '055'],
        ['id' => 52, 'id_grupo' => 4, 'nombre' => 'SERGIO MIGUEL', 'p_apellido' => 'CEDILLO', 's_apellido' => 'FERNÁNDEZ', 'email' => 'rosario.escutia+76@correo.michoacan.gob.mx', 'celular' => '4400000076', 'username' => 'UPP060', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '060'],
        ['id' => 53, 'id_grupo' => 4, 'nombre' => 'LUIS EDGARDO', 'p_apellido' => 'AMEZCUA', 's_apellido' => 'ALCALÁ', 'email' => 'rosario.escutia+20@correo.michoacan.gob.mx', 'celular' => '4400000020', 'username' => 'UPP063', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '063'],
        ['id' => 54, 'id_grupo' => 4, 'nombre' => 'FRANCISCO', 'p_apellido' => 'MÁRQUEZ', 's_apellido' => 'TINOCO', 'email' => 'rosario.escutia+50@correo.michoacan.gob.mx', 'celular' => '4400000050', 'username' => 'UPP068', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '068'],
        ['id' => 55, 'id_grupo' => 4, 'nombre' => 'GRACIELA', 'p_apellido' => 'VILLASEÑOR', 's_apellido' => 'FERREYRA', 'email' => 'rosario.escutia+21@correo.michoacan.gob.mx', 'celular' => '4400000021', 'username' => 'UPP069', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '069'],
        ['id' => 56, 'id_grupo' => 4, 'nombre' => 'CAMERINO', 'p_apellido' => 'MORENO', 's_apellido' => 'SALINAS', 'email' => 'rosario.escutia+22@correo.michoacan.gob.mx', 'celular' => '4400000022', 'username' => 'UPP070', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '070'],
        ['id' => 57, 'id_grupo' => 4, 'nombre' => 'GABRIELA', 'p_apellido' => 'MANZO', 's_apellido' => 'ORTIZ', 'email' => 'rosario.escutia+23@correo.michoacan.gob.mx', 'celular' => '4400000023', 'username' => 'UPP071', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '071'],
        ['id' => 58, 'id_grupo' => 4, 'nombre' => 'ANTONIO GODOY', 'p_apellido' => 'GONZÁÑEZ', 's_apellido' => 'VÉLEZ', 'email' => 'rosario.escutia+63@correo.michoacan.gob.mx', 'celular' => '4400000063', 'username' => 'UPP073', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '073'],
        ['id' => 59, 'id_grupo' => 4, 'nombre' => 'LIVIER JULIETA', 'p_apellido' => 'SOTO', 's_apellido' => 'GONZÁLEZ', 'email' => 'rosario.escutia+24@correo.michoacan.gob.mx', 'celular' => '4400000024', 'username' => 'UPP074', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '074'],
        ['id' => 60, 'id_grupo' => 4, 'nombre' => 'MARCO ANTONIO', 'p_apellido' => 'TINOCO', 's_apellido' => 'ÁLVAREZ', 'email' => 'rosario.escutia+78@correo.michoacan.gob.mx', 'celular' => '4400000078', 'username' => 'UPP075', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '075'],
        ['id' => 61, 'id_grupo' => 4, 'nombre' => 'MIGUEL ÁNGEL', 'p_apellido' => 'CUIN', 's_apellido' => 'SIMÓN', 'email' => 'rosario.escutia+39@correo.michoacan.gob.mx', 'celular' => '4400000039', 'username' => 'UPP078', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '078'],
        ['id' => 62, 'id_grupo' => 4, 'nombre' => 'ABRAHAM', 'p_apellido' => 'MONTES', 's_apellido' => 'MAGAÑA', 'email' => 'rosario.escutia+25@correo.michoacan.gob.mx', 'celular' => '4400000025', 'username' => 'UPP079', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '079'],
        ['id' => 63, 'id_grupo' => 4, 'nombre' => 'MAURICIO', 'p_apellido' => 'VARGAS', 's_apellido' => 'ANDALUZ', 'email' => 'rosario.escutia+64@correo.michoacan.gob.mx', 'celular' => '4400000064', 'username' => 'UPP080', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '080'],
        ['id' => 64, 'id_grupo' => 4, 'nombre' => 'LUIS ROBERTO', 'p_apellido' => 'ARIAS', 's_apellido' => 'REYES', 'email' => 'rosario.escutia+26@correo.michoacan.gob.mx', 'celular' => '4400000026', 'username' => 'UPP081', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '081'],
        ['id' => 65, 'id_grupo' => 4, 'nombre' => 'ALEJANDRO', 'p_apellido' => 'ESTRADA', 's_apellido' => 'SALINAS', 'email' => 'rosario.escutia+27@correo.michoacan.gob.mx', 'celular' => '4400000027', 'username' => 'UPP082', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '082'],
        ['id' => 66, 'id_grupo' => 4, 'nombre' => 'BLANCA ISALIA', 'p_apellido' => 'LARA', 's_apellido' => 'LEYVA', 'email' => 'rosario.escutia+65@correo.michoacan.gob.mx', 'celular' => '4400000065', 'username' => 'UPP083', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '083'],
        ['id' => 67, 'id_grupo' => 4, 'nombre' => 'TERESA', 'p_apellido' => 'LÓPEZ', 's_apellido' => 'HERNÁNDEZ', 'email' => 'rosario.escutia+66@correo.michoacan.gob.mx', 'celular' => '4400000066', 'username' => 'UPP084', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '084'],
        ['id' => 68, 'id_grupo' => 4, 'nombre' => 'FELIPE', 'p_apellido' => 'MORALES', 's_apellido' => 'CORREA', 'email' => 'rosario.escutia+28@correo.michoacan.gob.mx', 'celular' => '4400000028', 'username' => 'UPP085', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '085'],
        ['id' => 69, 'id_grupo' => 4, 'nombre' => 'MARIBEL JULISA', 'p_apellido' => 'SUAREZ', 's_apellido' => 'BUCIO', 'email' => 'rosario.escutia+29@correo.michoacan.gob.mx', 'celular' => '4400000029', 'username' => 'UPP087', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '087'],
        ['id' => 70, 'id_grupo' => 4, 'nombre' => 'JOSUÉ ALFONSO', 'p_apellido' => 'MEJÍA', 's_apellido' => 'PINEDA', 'email' => 'rosario.escutia+67@correo.michoacan.gob.mx', 'celular' => '4400000067', 'username' => 'UPP088', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '088'],
        ['id' => 71, 'id_grupo' => 4, 'nombre' => 'ALDO', 'p_apellido' => 'GUTIÉRREZ', 's_apellido' => 'AGUIRRE', 'email' => 'rosario.escutia+30@correo.michoacan.gob.mx', 'celular' => '4400000030', 'username' => 'UPP089', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '089'],
        ['id' => 72, 'id_grupo' => 4, 'nombre' => 'ARELI', 'p_apellido' => 'GALLEGOS', 's_apellido' => 'IBARRA', 'email' => 'rosario.escutia+31@correo.michoacan.gob.mx', 'celular' => '4400000031', 'username' => 'UPP093', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '093'],
        ['id' => 73, 'id_grupo' => 4, 'nombre' => 'PEDRO ALEXIS', 'p_apellido' => 'VELÁZQUEZ', 's_apellido' => 'GUZMÁN', 'email' => 'rosario.escutia+32@correo.michoacan.gob.mx', 'celular' => '4400000032', 'username' => 'UPP094', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '094'],
        ['id' => 74, 'id_grupo' => 4, 'nombre' => 'CAROLINA', 'p_apellido' => 'RANGEL', 's_apellido' => 'GRACIDA', 'email' => 'rosario.escutia+68@correo.michoacan.gob.mx', 'celular' => '4400000068', 'username' => 'UPP095', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '095'],
        ['id' => 75, 'id_grupo' => 4, 'nombre' => 'ALEJANDRA', 'p_apellido' => 'OCHOA', 's_apellido' => 'ZARZOSA', 'email' => 'rosario.escutia+73@correo.michoacan.gob.mx', 'celular' => '4400000073', 'username' => 'UPP096', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '096'],
        ['id' => 76, 'id_grupo' => 4, 'nombre' => 'BLANCA GABRIELA', 'p_apellido' => 'PÉREZ', 's_apellido' => 'SANTAMARÍA', 'email' => 'rosario.escutia+43@correo.michoacan.gob.mx', 'celular' => '4400000043', 'username' => 'UPP098', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '098'],
        ['id' => 77, 'id_grupo' => 4, 'nombre' => 'GERARDO ANDRÉS', 'p_apellido' => 'HERRERA', 's_apellido' => 'PÉREZ', 'email' => 'rosario.escutia+33@correo.michoacan.gob.mx', 'celular' => '4400000033', 'username' => 'UPP099', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '099'],
        ['id' => 78, 'id_grupo' => 4, 'nombre' => 'IGNACIO', 'p_apellido' => 'MENDOZA', 's_apellido' => 'JIMÉNEZ', 'email' => 'rosario.escutia+34@correo.michoacan.gob.mx', 'celular' => '4400000034', 'username' => 'UPP100', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '100'],
        ['id' => 79, 'id_grupo' => 4, 'nombre' => 'ZENAIDA', 'p_apellido' => 'SALVADOR', 's_apellido' => 'BRÍGIDO', 'email' => 'rosario.escutia+35@correo.michoacan.gob.mx', 'celular' => '4400000035', 'username' => 'UPP101', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '101'],
        ['id' => 80, 'id_grupo' => 4, 'nombre' => 'MIRYAM GEORGINA', 'p_apellido' => 'ALCALÁ', 's_apellido' => 'CASILLAS', 'email' => 'rosario.escutia+69@correo.michoacan.gob.mx', 'celular' => '4400000069', 'username' => 'UPP102', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '102'],
        ['id' => 81, 'id_grupo' => 4, 'nombre' => 'JOSE DE JESUS', 'p_apellido' => 'MEDINA', 's_apellido' => 'BASURTO', 'email' => 'rosario.escutia+36@correo.michoacan.gob.mx', 'celular' => '4400000036', 'username' => 'UPP103', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '103'],
        ['id' => 82, 'id_grupo' => 4, 'nombre' => 'JOSÉ ALFREDO', 'p_apellido' => 'FLORES', 's_apellido' => 'VARGAS', 'email' => 'rosario.escutia+74@correo.michoacan.gob.mx', 'celular' => '4400000074', 'username' => 'UPP104', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '104'],
        ['id' => 83, 'id_grupo' => 4, 'nombre' => 'GLADYS', 'p_apellido' => 'BUTANDA', 's_apellido' => 'MACIAS', 'email' => 'rosario.escutia+75@correo.michoacan.gob.mx', 'celular' => '4400000075', 'username' => 'UPP105', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '105'],
        ['id' => 84, 'id_grupo' => 4, 'nombre' => 'ALEJANDRO', 'p_apellido' => 'MÉNDEZ', 's_apellido' => 'LÓPEZ', 'email' => 'rosario.escutia+51@correo.michoacan.gob.mx', 'celular' => '4400000051', 'username' => 'UPP106', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '106'],
        ['id' => 85, 'id_grupo' => 4, 'nombre' => 'JESÚS ANTONIO', 'p_apellido' => 'MORA', 's_apellido' => 'GONZÁLEZ', 'email' => 'rosario.escutia+37@correo.michoacan.gob.mx', 'celular' => '4400000037', 'username' => 'UPP107', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '107'],
        ['id' => 86, 'id_grupo' => 4, 'nombre' => 'MARIANA', 'p_apellido' => 'SOSA', 's_apellido' => 'OLMEDA', 'email' => 'rosario.escutia+70@correo.michoacan.gob.mx', 'celular' => '4400000070', 'username' => 'UPP108', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '108'],
        ['id' => 87, 'id_grupo' => 4, 'nombre' => 'ANDRÉS', 'p_apellido' => 'MEDINA', 's_apellido' => 'GUZMÁN', 'email' => 'rosario.escutia+71@correo.michoacan.gob.mx', 'celular' => '4400000071', 'username' => 'UPP109', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '109'],
        ['id' => 88, 'id_grupo' => 4, 'nombre' => 'MOISÉS', 'p_apellido' => 'PARDO', 's_apellido' => 'RODRÍGUEZ', 'email' => 'rosario.escutia+80@correo.michoacan.gob.mx', 'celular' => '4400000080', 'username' => 'UPP110', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => '110'],
        ['id' => 89, 'id_grupo' => 4, 'nombre' => 'ADRIAN', 'p_apellido' => 'LÓPEZ', 's_apellido' => 'SOLIS', 'email' => 'rosario.escutia+52@correo.michoacan.gob.mx', 'celular' => '4400000052', 'username' => 'UPPA13', 'password' => 'valida2022', 'sudo' => 0, 'clv_upp' => 'A13']
    );

        protected $sistemas = array(
            ['id' => 1, 'nombre_sistema' => 'Sistema de Calendarizacion','ruta' => 'sistemas', 'logo' => 'logo_expedientes.png', 'logo_min' => 'logo_expedientes_min.png', 'descripcion' => 'Sistema para la adminsitración de expedientes Jurídicos', 'estatus' => 1],
        );


        protected $menus = array(
            ['id' => 2,  'id_sistema' => 1,'padre' => 4, 'nombre_menu' => 'Logs', 'ruta' => '/logs', 'icono' => 'fa-calendar', 'nivel' => 0, 'posicion' => 3, 'descripcion' => 'Módulo de calendario'],
            ['id' => 3,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Usuarios', 'ruta' => '/adm-usuarios', 'icono' => 'fa-user', 'nivel' => 0, 'posicion' => 3, 'descripcion' => 'Módulo para administrar los usuarios del sistema'],
            ['id' => 4,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Administración', 'ruta' => '#', 'icono' => 'fa-gears', 'nivel' => 0, 'posicion' => 7, 'descripcion' => 'Conjunto de módulos de adminsitración del sistema'],
            ['id' => 5,  'id_sistema' => 1,'padre' => 4, 'nombre_menu' => 'Grupos', 'ruta' => '/adm-grupos', 'icono' => 'fa-users', 'nivel' => 1, 'posicion' => 1, 'descripcion' => 'Módulo para administrar los grupos del sistema'],
            ['id' => 6,  'id_sistema' => 1,'padre' => 4, 'nombre_menu' => 'Bitácora', 'ruta' => '/adm-bitacora', 'icono' => 'fa-bookmark', 'nivel' => 1, 'posicion' => 2, 'descripcion' => 'Bitácora de movimientos del sistema'],
            ['id' => 7,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Calendarizacion', 'ruta' => '/calendarizacion', 'icono' => ' fa-calendar', 'nivel' => 0, 'posicion' => 4, 'descripcion' => 'Calendarizacion de presupuestos'],
            ['id' => 8,  'id_sistema' => 1,'padre' => 7, 'nombre_menu' => 'Claves presupuestarias', 'ruta' => '/calendarizacion/claves', 'icono' => ' fa-calendar', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Registro de claves presupuestaria'],
            ['id' => 9,  'id_sistema' => 1,'padre' => 7, 'nombre_menu' => 'Metas', 'ruta' => '/calendarizacion/metas', 'icono' => 'fa-flag-checkered', 'nivel' => 1, 'posicion' => 1, 'descripcion' => 'Techos financieros'],
            ['id' => 10,  'id_sistema' => 1,'padre' => 7, 'nombre_menu' => 'Techos financieros', 'ruta' => '/calendarizacion/techos', 'icono' => 'fa-flag-checkered', 'nivel' => 2, 'posicion' => 2, 'descripcion' => 'Techos financieros'],
            ['id' => 11,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Reportes', 'ruta' => '/Reportes', 'icono' => 'fa-flag-checkered', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Reportes'],
            ['id' => 12,  'id_sistema' => 1,'padre' => 11, 'nombre_menu' => 'ley Planeacion Hacienda', 'ruta' => '/Reportes/ley-planeacion', 'icono' => 'fa-flag-checkered', 'nivel' => 1, 'posicion' => 1, 'descripcion' => 'Reportes'],
            ['id' => 13,  'id_sistema' => 1,'padre' => 11, 'nombre_menu' => 'Administrativos', 'ruta' => '/Reportes/administrativos', 'icono' => 'fa-flag-checkered', 'nivel' => 2, 'posicion' => 2, 'descripcion' => 'Reportes'],
            ['id' => 14,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Administracion de captura', 'ruta' => '/admon-capturas', 'icono' => ' fa-crosshairs', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Administracion de capturas'],
            ['id' => 15,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Configuraciones', 'ruta' => '/adm-configuracion', 'icono' => ' fa-crosshairs', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Administracion de configuraciones'],
        );
    protected $menuUpp = array(
        ['id_menu' => 7 ], 
        ['id_menu' => 8 ], 
        ['id_menu' => 9 ], 
        ['id_menu' => 11],
        ['id_menu' => 13]
    );
    protected $menugob = array(
        ['id_menu' => 2 ], 
        ['id_menu' => 4 ], 
        ['id_menu' => 5 ], 
        ['id_menu' => 6]
    );
    protected $menuAdmin = array(
        ['id_menu' => 3 ], 
        ['id_menu' => 7 ], 
        ['id_menu' => 8 ], 
        ['id_menu' => 9 ], 
        ['id_menu' => 10],
        ['id_menu' => 11],
        ['id_menu' => 12],
        ['id_menu' => 13],
        ['id_menu' => 14],
        ['id_menu' => 15]
    );
    protected $menuAuditor = array(
        ['id_menu' => 3 ], 
        ['id_menu' => 7 ], 
        ['id_menu' => 8 ], 
        ['id_menu' => 9 ], 
        ['id_menu' => 10],
        ['id_menu' => 11],
        ['id_menu' => 12],
        ['id_menu' => 13]
    );
    protected $menuDel =array(
        ['id_menu' => 7 ], 
        ['id_menu' => 8 ], 
        ['id_menu' => 10 ]
    );

        protected $funciones = array(
            ['id' => 1,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'getUsuarios', 'tipo' => 'Consulta', 'descripcion' => 'Obtener todos los usuarios de la BD'],
            ['id' => 2,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'postUsuarios', 'tipo' => 'Insercion', 'descripcion' => 'Insertar un usuario a la BD'],
            ['id' => 3,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'putUsuarios', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizar un usuario a la BD'],
            ['id' => 4,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'deleteUsuarios', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar un usuario a la BD'],
            ['id' => 5,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'viewPostUsuarios', 'tipo' => 'Vista', 'descripcion' => 'Vista create usuario'],  
            
            ['id' => 6,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Grupos', 'funcion' => 'getGrupos', 'tipo' => 'Consulta', 'descripcion' => 'Insertar un grupo a la BD'],
            ['id' => 7,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Grupos', 'funcion' => 'postGrupos', 'tipo' => 'Insercion', 'descripcion' => 'Insertar un grupo a la BD'],
            ['id' => 8,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Grupos', 'funcion' => 'putGrupos', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizar un grupo a la BD'],
            ['id' => 9,  'id_sistema' => 1,'id_menu' =>5, 'modulo' => 'Grupos', 'funcion' => 'deleteGrupos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar un grupo a la BD'],
            ['id' => 10,  'id_sistema' => 1,'id_menu' =>5, 'modulo' => 'Grupos', 'funcion' => 'viewPostGrupos', 'tipo' => 'Vista', 'descripcion' => 'Vista create grupo'],
            
            ['id' => 11,  'id_sistema' => 1,'id_menu' => 6, 'modulo' => 'Bitacora', 'funcion' => 'getBitacora', 'tipo' => 'Consulta', 'descripcion' => 'Consulta de registros de la bitácora'],
            ['id' => 12,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'getPermisos', 'tipo' => 'Consulta', 'descripcion' => 'Consulta de permisos'],
            ['id' => 13,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'postPermisos', 'tipo' => 'Insercion', 'descripcion' => 'Crear registro de permisos'],
            ['id' => 14,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'deletePermisos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar registro de permisos'],
            ['id' => 15,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'deletePermisos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar registro de permisos'],
            
            ['id' => 16,  'id_sistema' => 1,'id_menu' => 8, 'modulo' => 'Claves presupuestarias', 'funcion' => 'getClaves', 'tipo' => 'consulta', 'descripcion' => 'consulta claves presupuestarias'],
            ['id' => 17,  'id_sistema' => 1,'id_menu' => 8, 'modulo' => 'Claves presupuestarias', 'funcion' => 'postClaves', 'tipo' => 'Insercion', 'descripcion' => 'Insercion claves presupuestarias'],
            ['id' => 18,  'id_sistema' => 1,'id_menu' => 8, 'modulo' => 'Claves presupuestarias', 'funcion' => 'putClaves', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizacion claves presupuestarias'],
            ['id' => 19,  'id_sistema' => 1,'id_menu' => 8, 'modulo' => 'Claves presupuestarias', 'funcion' => 'deleteClaves', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminacion claves presupuestarias'],
           
            ['id' => 20,  'id_sistema' => 1,'id_menu' => 9, 'modulo' => 'Metas', 'funcion' => 'getMetas', 'tipo' => 'consulta', 'descripcion' => 'consulta Metas '],
            ['id' => 21,  'id_sistema' => 1,'id_menu' => 9, 'modulo' => 'Metas', 'funcion' => 'postMetas', 'tipo' => 'Insercion', 'descripcion' => 'Insercion Metas '],
            ['id' => 22,  'id_sistema' => 1,'id_menu' => 9, 'modulo' => 'Metas', 'funcion' => 'putMetas', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizacion Metas '],
            ['id' => 23,  'id_sistema' => 1,'id_menu' => 9, 'modulo' => 'Metas', 'funcion' => 'deleteMetas', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminacion Metas '],
            
            ['id' => 24,  'id_sistema' => 1,'id_menu' => 10, 'modulo' => 'Techos financieros', 'funcion' => 'getTechos', 'tipo' => 'consulta', 'descripcion' => 'consulta Techos Financieros'],
            ['id' => 25,  'id_sistema' => 1,'id_menu' => 10, 'modulo' => 'Techos financieros', 'funcion' => 'postTechos', 'tipo' => 'Insercion', 'descripcion' => 'Insercion Techos Financieros'],
            ['id' => 26,  'id_sistema' => 1,'id_menu' => 10, 'modulo' => 'Techos financieros', 'funcion' => 'putTechos', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizacion Techos Financieros'],
            ['id' => 27,  'id_sistema' => 1,'id_menu' => 10, 'modulo' => 'Techos financieros', 'funcion' => 'deleteTechos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminacion Techos Financieros'],
            
            ['id' => 28,  'id_sistema' => 1,'id_menu' => 12, 'modulo' => 'ley Planeacion Hacienda', 'funcion' => 'getPlaneacion', 'tipo' => 'consulta', 'descripcion' => 'consulta planeacion'],
            ['id' => 29,  'id_sistema' => 1,'id_menu' => 12, 'modulo' => 'ley Planeacion Hacienda', 'funcion' => 'postPlaneacion', 'tipo' => 'Insercion', 'descripcion' => 'insertar planeacion'],
            ['id' => 30,  'id_sistema' => 1,'id_menu' => 12, 'modulo' => 'ley Planeacion Hacienda', 'funcion' => 'putPlaneacion', 'tipo' => 'Actualizacion', 'descripcion' => 'actualizar planeacion'],
            ['id' => 31,  'id_sistema' => 1,'id_menu' => 12, 'modulo' => 'ley Planeacion Hacienda', 'funcion' => 'deletePlaneacion', 'tipo' => 'Eliminacion', 'descripcion' => 'eliminar planeacion'],
            
            ['id' => 32,  'id_sistema' => 1,'id_menu' => 13, 'modulo' => 'Administrativos', 'funcion' => 'getAdmon', 'tipo' => 'consulta', 'descripcion' => 'consulta Administrativos'],
            ['id' => 33,  'id_sistema' => 1,'id_menu' => 13, 'modulo' => 'Administrativos', 'funcion' => 'postAdmon', 'tipo' => 'Insercion', 'descripcion' => 'insertar Administrativos'],
            ['id' => 34,  'id_sistema' => 1,'id_menu' => 13, 'modulo' => 'Administrativos', 'funcion' => 'putAdmon', 'tipo' => 'Actualizacion', 'descripcion' => 'actualizar Administrativos'],
            ['id' => 35,  'id_sistema' => 1,'id_menu' => 13, 'modulo' => 'Administrativos', 'funcion' => 'deleteAdmon', 'tipo' => 'Eliminacion', 'descripcion' => 'eliminar Administrativos'],
            
            ['id' => 36,  'id_sistema' => 1,'id_menu' => 14, 'modulo' => 'Administracion de captura', 'funcion' => 'getCaptura', 'tipo' => 'consulta', 'descripcion' => 'consulta Admon Captura'],
            ['id' => 37,  'id_sistema' => 1,'id_menu' => 14, 'modulo' => 'Administracion de captura', 'funcion' => 'postCaptura', 'tipo' => 'Insercion', 'descripcion' => 'insertar Admon Captura'],
            ['id' => 38,  'id_sistema' => 1,'id_menu' => 14, 'modulo' => 'Administracion de captura', 'funcion' => 'putCaptura', 'tipo' => 'Actualizacion', 'descripcion' => 'actualizar Admon Captura'],
            ['id' => 39,  'id_sistema' => 1,'id_menu' => 14, 'modulo' => 'Administracion de captura', 'funcion' => 'deleteCaptura', 'tipo' => 'Eliminacion', 'descripcion' => 'eliminar Admon Captura'],
            
            ['id' => 40,  'id_sistema' => 1,'id_menu' => 2, 'modulo' => 'Logs', 'funcion' => 'getLogs', 'tipo' => 'consulta', 'descripcion' => 'Vista de Logs'],
            ['id' => 41,  'id_sistema' => 1,'id_menu' => 15, 'modulo' => 'Configuraciones', 'funcion' => 'viewPostUpps', 'tipo' => 'Consulta', 'descripcion' => 'Obtener todos los tipos de actividad por upp a la BD'],
            ['id' => 42,  'id_sistema' => 1,'id_menu' => 15, 'modulo' => 'Configuraciones', 'funcion' => 'updateUpps', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizar un tipo de actividad por upp a la BD'],
        );

        protected $relFunGroup=array(
            /* admin */
            /* usuarios */
            ['id'=>1,'id_grupo'=>1,'id_funcion'=>1],
            ['id'=>2,'id_grupo'=>1,'id_funcion'=>2],
            ['id'=>3,'id_grupo'=>1,'id_funcion'=>3],
            ['id'=>4,'id_grupo'=>1,'id_funcion'=>4],
            ['id'=>5,'id_grupo'=>1,'id_funcion'=>5],
            /* claves */
            ['id'=>6,'id_grupo'=>1,'id_funcion'=>16],
            ['id'=>7,'id_grupo'=>1,'id_funcion'=>17],
            ['id'=>8,'id_grupo'=>1,'id_funcion'=>18],
            ['id'=>9,'id_grupo'=>1,'id_funcion'=>19],
            /* metas */
            ['id'=>10,'id_grupo'=>1,'id_funcion'=>20],
            ['id'=>11,'id_grupo'=>1,'id_funcion'=>21],
            ['id'=>12,'id_grupo'=>1,'id_funcion'=>22],
            ['id'=>13,'id_grupo'=>1,'id_funcion'=>23],
            /* techos */
            ['id'=>14,'id_grupo'=>1,'id_funcion'=>24],
            ['id'=>15,'id_grupo'=>1,'id_funcion'=>25],
            ['id'=>16,'id_grupo'=>1,'id_funcion'=>26],
            ['id'=>17,'id_grupo'=>1,'id_funcion'=>27],
            /* planeacion */
            ['id'=>18,'id_grupo'=>1,'id_funcion'=>28],
            ['id'=>19,'id_grupo'=>1,'id_funcion'=>29],
            ['id'=>20,'id_grupo'=>1,'id_funcion'=>30],
            ['id'=>21,'id_grupo'=>1,'id_funcion'=>31],
            /* administrativos */
            ['id'=>22,'id_grupo'=>1,'id_funcion'=>32],
            ['id'=>23,'id_grupo'=>1,'id_funcion'=>33],
            ['id'=>24,'id_grupo'=>1,'id_funcion'=>34],
            ['id'=>25,'id_grupo'=>1,'id_funcion'=>35],
            /* admin captura */
            ['id'=>26,'id_grupo'=>1,'id_funcion'=>36],
            ['id'=>27,'id_grupo'=>1,'id_funcion'=>37],
            ['id'=>28,'id_grupo'=>1,'id_funcion'=>38],
            ['id'=>29,'id_grupo'=>1,'id_funcion'=>39],
            /* upp */
            /* planeacion */
            ['id'=>30,'id_grupo'=>4,'id_funcion'=>28],
            ['id'=>31,'id_grupo'=>4,'id_funcion'=>29],
            ['id'=>32,'id_grupo'=>4,'id_funcion'=>30],
            ['id'=>33,'id_grupo'=>4,'id_funcion'=>31],
            /* administrativos */
            ['id'=>34,'id_grupo'=>4,'id_funcion'=>32],
            ['id'=>35,'id_grupo'=>4,'id_funcion'=>33],
            ['id'=>36,'id_grupo'=>4,'id_funcion'=>34],
            ['id'=>37,'id_grupo'=>4,'id_funcion'=>35],
            /* claves */
            ['id'=>38,'id_grupo'=>4,'id_funcion'=>16],
            ['id'=>39,'id_grupo'=>4,'id_funcion'=>17],
            ['id'=>40,'id_grupo'=>4,'id_funcion'=>18],
            ['id'=>41,'id_grupo'=>4,'id_funcion'=>19],
            /* metas */
            ['id'=>42,'id_grupo'=>4,'id_funcion'=>20],
            ['id'=>43,'id_grupo'=>4,'id_funcion'=>21],
            ['id'=>44,'id_grupo'=>4,'id_funcion'=>22],
            ['id'=>45,'id_grupo'=>4,'id_funcion'=>23],
            /* claves delegacion */
            ['id'=>46,'id_grupo'=>5,'id_funcion'=>16],
            ['id'=>47,'id_grupo'=>5,'id_funcion'=>17],
            ['id'=>48,'id_grupo'=>5,'id_funcion'=>18],
            ['id'=>49,'id_grupo'=>5,'id_funcion'=>19],

            ['id'=>50,'id_grupo'=>2,'id_funcion'=>40],
            ['id'=>51,'id_grupo'=>2,'id_funcion'=>11],
            ['id'=>52,'id_grupo'=>2,'id_funcion'=>12],
            ['id'=>53,'id_grupo'=>2,'id_funcion'=>13],
            ['id'=>54,'id_grupo'=>2,'id_funcion'=>14],
            ['id'=>55,'id_grupo'=>2,'id_funcion'=>15],
            /* auditor */
            ['id'=>56,'id_grupo'=>3,'id_funcion'=>1],
            ['id'=>57,'id_grupo'=>3,'id_funcion'=>16],
            ['id'=>58,'id_grupo'=>3,'id_funcion'=>20],
            ['id'=>59,'id_grupo'=>3,'id_funcion'=>24],
            ['id'=>60,'id_grupo'=>3,'id_funcion'=>28],
            ['id'=>61,'id_grupo'=>3,'id_funcion'=>32],
            ['id'=>62,'id_grupo'=>3,'id_funcion'=>36]
        );
    public function run()
    {
        $this->call([
            fondosSeeder::class,
            pp_identificadoresSeeder::class,
            ProgramacionPresupuestoSeeder::class,
            TipoActividadUppSeeder::class,
            prueba2024::class
        ]);
        
        echo "\nInicializacion de Catalogos del Sistema";

        echo "\n    -Limpieza Anterior";
        DB::statement("SET foreign_key_checks=0");
        User::truncate();
        Sistema::truncate();

        Menu::truncate();
        Funciones::truncate();
        CatPermisos::truncate();
        DB::statement("SET foreign_key_checks=1");

        DB::beginTransaction();
        try {
            echo "\n    -Carga Catálogo Entes";
            foreach ($this->cat_permisos as $ente) {
                $ente_bd = CatPermisos::find($ente['id']);
                if (!$ente_bd) {
                    CatPermisos::create($ente);
                } else {
                    $ente_bd->update($ente);
                    $ente_bd->save();
                }
            }
            echo "\n    -Carga Catálogo Grupos";
            foreach ($this->grupos as $grupo) {
                $grupo_bd = Grupo::find($grupo['id']);
                if (!$grupo_bd) {
                    Grupo::create($grupo);
                } else {
                    $grupo_bd->update($grupo);
                    $grupo_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Usuarios";
            foreach ($this->cat_users as $user) {
                $user_bd = User::find($user['id']);
                if (!$user_bd) {
                   $Nuser= User::create($user);
                    UsuarioGrupo::create([
                        'id_grupo' => $Nuser->id_grupo,
                        'id_usuario' => $Nuser->id
                    ]);
                } else {
                    $user_bd->update($user);
                    $user_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Sistemas";
            foreach ($this->sistemas as $sistema) {
                $sistema_bd = Sistema::find($sistema['id']);
                if (!$sistema_bd) {
                    Sistema::create($sistema);
                } else {
                    $sistema_bd->update($sistema);
                    $sistema_bd->save();
                }
            }
            echo "\n    -Carga rel Sistema Grupo";
            foreach ($this->grupos as $grupo) {
                SistemaGrupo::create([
                    'id_grupo' => $grupo['id'],
                    'id_sistema' => 1
                ]);

            }
           

            echo "\n    -Carga Catálogo Menus";
            foreach ($this->menus as $menu) {
                $menu_bd = Menu::find($menu['id']);
                if (!$menu_bd) {
                    Menu::create($menu);
                } else {
                    $menu_bd->update($menu);
                    $menu_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Funciones";
            foreach ($this->funciones as $funcion) {
                $funcion_bd = Funciones::find($funcion['id']);
                if (!$funcion_bd) {
                    Funciones::create($funcion);
                } else {
                    $funcion_bd->update($funcion);
                    $funcion_bd->save();
                }
            }
            try {
                echo "\n    -Carga rel Menu grupo UPP";
            foreach ($this->menuUpp as $m) {
                MenuGrupo::create([
                    'id_grupo' => 4,
                    'id_menu' => $m['id_menu']
                ]);

            }
            } catch (\Throwable $th) {
                throw $th;
            }

            try {
                echo "\n    -Carga rel Menu grupo Admin";
            foreach ($this->menuAdmin as $m) {
                MenuGrupo::create([
                    'id_grupo' => 1,
                    'id_menu' => $m['id_menu']
                ]);

            }
            } catch (\Throwable $th) {
                throw $th;
            }
            try {
                echo "\n    -Carga rel Menu grupo gobdg";
            foreach ($this->menuAdmin as $m) {
                MenuGrupo::create([
                    'id_grupo' => 2,
                    'id_menu' => $m['id_menu']
                ]);

            }
            } catch (\Throwable $th) {
                throw $th;
            }
            try {
                echo "\n    -Carga rel Menu grupo Auditor";
            foreach ($this->menuAuditor as $m) {
                MenuGrupo::create([
                    'id_grupo' => 3,
                    'id_menu' => $m['id_menu']
                ]);

            }
            } catch (\Throwable $th) {
                throw $th;
            }

            try {
                echo "\n    -Carga rel Menu grupo Delegacion";
            foreach ($this->menuDel as $m) {
                MenuGrupo::create([
                    'id_grupo' => 5,
                    'id_menu' => $m['id_menu']
                ]);

            }
            } catch (\Throwable $th) {
                throw $th;
            }

            try {
                echo "\n    -Carga rel grupo funcion";
            foreach ($this->relFunGroup as $m) {
                Permisos::create($m);
            }
            } catch (\Throwable $th) {
                throw $th;
            }

            try {
                echo "\n    -Carga permisos adicionales";
                $user = User::where('deleted_at', null)->where('id_grupo', 1)->get();
            foreach ($user as $m) {
                PermisosUpp::create([
                    'id_user'=>$m->id,
                    'id_permiso'=>1
                ]);
            }
            } catch (\Throwable $th) {
                throw $th;
            }

            DB::commit();
            echo "\n    - Se aplico con exito el Seeder - Base:\n";
        } catch (\Exception $e) {
            DB::rollback();
            echo "\n    - Ocurrio un error al ejecutar la operacion:",$e;
        }

    }
}
