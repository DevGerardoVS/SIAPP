<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class UsersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    public function collection()
    {

        $users = DB::table('users')
            ->select('users.nombre','apellidoP','apellidoM', 'username', 'email','telefono','perfiles.nombre as perfil','users.delegacion',DB::raw('IF(users.estatus=1,"Vigente","No vigente") as estatus'))
            ->join('perfiles','users.perfil_id',"=","perfiles.id")
            ->orderBy('users.nombre', 'asc')
            ->get();

        return $users;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["Nombre", "Apellido Paterno", "Apellido Materno", "Usuario", "Correo", "Telefono", "Perfil", "Delegación", "Estatus"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 20,
            'D' => 15,
            'E' => 30,
            'F' => 15,
            'G' => 30,
            'H' => 15,
            'I' => 15,
            'J' => 15
        ];
    }
}
