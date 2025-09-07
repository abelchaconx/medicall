<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Medicina General', 'description' => 'Atención primaria, diagnóstico y manejo inicial de patologías comunes.'],
            ['name' => 'Pediatría', 'description' => 'Atención médica de niños y adolescentes. Crecimiento, vacunas y enfermedades infantiles.'],
            ['name' => 'Cardiología', 'description' => 'Diagnóstico y tratamiento de enfermedades del corazón y sistema circulatorio.'],
            ['name' => 'Dermatología', 'description' => 'Diagnóstico y tratamiento de enfermedades de la piel, cabello y uñas.'],
            ['name' => 'Ginecología y Obstetricia', 'description' => 'Salud de la mujer, control prenatal y atención del parto.'],
            ['name' => 'Neurología', 'description' => 'Trastornos del sistema nervioso central y periférico.'],
            ['name' => 'Ortopedia', 'description' => 'Patologías del sistema musculoesquelético y tratamiento quirúrgico y conservador.'],
            ['name' => 'Oftalmología', 'description' => 'Enfermedades y cirugía de los ojos.'],
            ['name' => 'Endocrinología', 'description' => 'Trastornos hormonales y metabólicos (diabetes, tiroides, etc.).'],
            ['name' => 'Psiquiatría', 'description' => 'Diagnóstico y tratamiento de trastornos mentales y emocionales.'],
            ['name' => 'Otorrinolaringología', 'description' => 'Enfermedades de oído, nariz y garganta.'],
            ['name' => 'Oncología', 'description' => 'Diagnóstico y tratamiento del cáncer.'],
            ['name' => 'Urología', 'description' => 'Enfermedades del aparato urinario y sistema reproductor masculino.'],
            ['name' => 'Nefrología', 'description' => 'Enfermedades del riñón y manejo de la insuficiencia renal.'],
            ['name' => 'Reumatología', 'description' => 'Trastornos autoinmunes y enfermedades reumáticas.'],
        ];

        foreach ($specialties as $s) {
            Specialty::updateOrCreate(['name' => $s['name']], $s);
        }
    }
}
