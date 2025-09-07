<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalOffice;

class MedicalOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicalOffices = [
            [
                'name' => 'Consultorio Médico Central',
                'address_line' => 'Av. 6 de Agosto 2170',
                'city' => 'La Paz',
                'province' => 'Pedro Domingo Murillo',
                'phone' => '2-2771234',
                'latitude' => -16.500000,
                'longitude' => -68.150000,
                'otros' => 'Planta baja, consultorio 101'
            ],
            [
                'name' => 'Centro Médico San Juan',
                'address_line' => 'Calle Sucre 425',
                'city' => 'Cochabamba',
                'province' => 'Cercado',
                'phone' => '4-4234567',
                'latitude' => -17.394000,
                'longitude' => -66.157000,
                'otros' => 'Segundo piso, ala norte'
            ],
            [
                'name' => 'Consultorio Dr. García',
                'address_line' => 'Av. Banzer 123',
                'city' => 'Santa Cruz',
                'province' => 'Andrés Ibáñez',
                'phone' => '3-3456789',
                'latitude' => -17.783000,
                'longitude' => -63.182000,
                'otros' => 'Torre médica, piso 3'
            ],
            [
                'name' => 'Clínica Familiar Norte',
                'address_line' => 'Calle Potosí 890',
                'city' => 'Potosí',
                'province' => 'Tomás Frías',
                'phone' => '2-6223344',
                'latitude' => -19.573000,
                'longitude' => -65.755000,
                'otros' => 'Edificio médico, consultorio A'
            ],
            [
                'name' => 'Centro de Salud Quillacollo',
                'address_line' => 'Plaza Principal s/n',
                'city' => 'Cochabamba',
                'province' => 'Quillacollo',
                'phone' => '4-4112233',
                'latitude' => -17.392000,
                'longitude' => -66.279000,
                'otros' => 'Frente a la iglesia principal'
            ],
            [
                'name' => 'Consultorio Médico El Alto',
                'address_line' => 'Av. Juan Pablo II 567',
                'city' => 'La Paz',
                'province' => 'Pedro Domingo Murillo',
                'phone' => '2-2834455',
                'latitude' => -16.505000,
                'longitude' => -68.151000,
                'otros' => 'Centro comercial, local 15'
            ],
            [
                'name' => 'Centro Médico Tarija',
                'address_line' => 'Calle Ingavi 234',
                'city' => 'Tarija',
                'province' => 'Cercado',
                'phone' => '4-6445566',
                'latitude' => -21.536000,
                'longitude' => -64.730000,
                'otros' => 'Cerca del mercado central'
            ],
            [
                'name' => 'Consultorio Sucre Centro',
                'address_line' => 'Plaza 25 de Mayo 78',
                'city' => 'Chuquisaca',
                'province' => 'Oropeza',
                'phone' => '4-6556677',
                'latitude' => -19.047000,
                'longitude' => -65.260000,
                'otros' => 'Edificio colonial, primer piso'
            ],
            [
                'name' => 'Centro de Salud Oruro',
                'address_line' => 'Calle Bolívar 345',
                'city' => 'Oruro',
                'province' => 'Cercado',
                'phone' => '2-5667788',
                'latitude' => -17.963000,
                'longitude' => -67.107000,
                'otros' => 'Zona centro, referencia: Banco Unión'
            ],
            [
                'name' => 'Consultorio Médico Trinidad',
                'address_line' => 'Av. 6 de Agosto 456',
                'city' => 'Beni',
                'province' => 'Cercado',
                'phone' => '3-4778899',
                'latitude' => -14.834000,
                'longitude' => -64.906000,
                'otros' => 'Barrio centro, cerca del aeropuerto'
            ]
        ];

        foreach ($medicalOffices as $office) {
            MedicalOffice::create($office);
        }
    }
}
