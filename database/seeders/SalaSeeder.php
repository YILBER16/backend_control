<?php

namespace Database\Seeders;

use App\Models\Sala;
use Illuminate\Database\Seeder;

class SalaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salas = [
            [
                'nombre' => 'Sala 1',
                'descripcion' => 'Aula 1 - Aula de informática',
                'capacidad' => 30,
                'ubicacion' => 'Edificio A, Piso 1',
                'estado' => 'activa',
            ],
            [
                'nombre' => 'Sala 2',
                'descripcion' => 'Aula 2 - Laboratorio de cómputo',
                'capacidad' => 25,
                'ubicacion' => 'Edificio A, Piso 2',
                'estado' => 'activa',
            ],
            [
                'nombre' => 'Sala 3',
                'descripcion' => 'Aula 3 - Sala de capacitación',
                'capacidad' => 20,
                'ubicacion' => 'Edificio B, Piso 1',
                'estado' => 'activa',
            ],
            [
                'nombre' => 'Sala 4',
                'descripcion' => 'Aula 4 - Laboratorio avanzado',
                'capacidad' => 15,
                'ubicacion' => 'Edificio B, Piso 2',
                'estado' => 'inactiva',
            ],
        ];

        foreach ($salas as $sala) {
            Sala::create($sala);
        }
    }
}
