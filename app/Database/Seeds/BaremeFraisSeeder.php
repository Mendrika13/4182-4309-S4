<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaremeFraisSeeder extends Seeder
{
    public function run()
    {
        // Vide la table avant de la remplir pour éviter les doublons
        $this->db->table('baremes_frais')->truncate();

        $baremes = [
            // ---- RETRAITS ----
            ['type_operation' => 'retrait', 'montant_min' => 0,      'montant_max' => 5000,      'frais' => 100],
            ['type_operation' => 'retrait', 'montant_min' => 5001,   'montant_max' => 20000,     'frais' => 300],
            ['type_operation' => 'retrait', 'montant_min' => 20001,  'montant_max' => 50000,     'frais' => 600],
            ['type_operation' => 'retrait', 'montant_min' => 50001,  'montant_max' => 100000,    'frais' => 1000],
            ['type_operation' => 'retrait', 'montant_min' => 100001, 'montant_max' => 999999999, 'frais' => 1500],

            // ---- TRANSFERTS ----
            ['type_operation' => 'transfert', 'montant_min' => 0,      'montant_max' => 5000,      'frais' => 50],
            ['type_operation' => 'transfert', 'montant_min' => 5001,   'montant_max' => 20000,     'frais' => 150],
            ['type_operation' => 'transfert', 'montant_min' => 20001,  'montant_max' => 50000,     'frais' => 300],
            ['type_operation' => 'transfert', 'montant_min' => 50001,  'montant_max' => 100000,    'frais' => 500],
            ['type_operation' => 'transfert', 'montant_min' => 100001, 'montant_max' => 999999999, 'frais' => 800],
        ];

        $this->db->table('baremes_frais')->insertBatch($baremes);
    }
}
