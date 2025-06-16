<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            //EstadosSeeder::class,
            //RolSeeder::class
            MetodoPagoSeeder::class
        ]);
        //User::factory()->create([
        //    'email' => 'test@example.com',
        //]);
    }
}
