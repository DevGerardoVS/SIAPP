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

        $users = DB::table('adm_users')
            ->select('id','clv_upp','nombre','p_apellido', 's_apellido', 'username','email','celular')
            ->orderBy('nombre', 'asc')
            ->get();
        return $users;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["ID","Clave upp","Nombre", "Apellido Paterno", "Apellido Materno", "Usuario", "Correo", "Telefono"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 20,
            'H' => 20,
        ];
    }
}
