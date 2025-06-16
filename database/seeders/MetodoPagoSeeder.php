<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=array([
            'nombre' => 'Tajeta',
            'created_at' => Carbon::now()
        ],[
            'nombre' => 'Bitcoin',
            'created_at' => Carbon::now()
        ]);
    //insertar a la data de prueba
    DB::table('metodopago')->insert($data);
    }
}
