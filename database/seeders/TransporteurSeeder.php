<?php

namespace Database\Seeders;

use App\Models\Transporteur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransporteurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            [
                'code' => 'bpost_home',
                'nom' => 'Bpost Domicile',
                'details' => '2-3 jours',
                'prix' => 3.99,
            ],
            [
                'code' => 'mondial_relay',
                'nom' => 'Point Relais',
                'details' => 'Mondial Relay',
                'prix' => 3.49,
            ],
            [
                'code' => 'express',
                'nom' => 'Express BE',
                'details' => 'Lendemain (si <15h)',
                'prix' => 6.99,
            ],
        ];

        foreach ($options as $option) {
            Transporteur::updateOrCreate(
                ['code' => $option['code']], // Check if exists by code
                $option // Update or create with these values
            );
        }
    }
}
