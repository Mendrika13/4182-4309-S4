<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PrefixeSeeder extends Seeder
{
    public function run()
    {
        $prefixes = ['032', '033', '034', '037', '038'];

        foreach ($prefixes as $prefixe) {
            
            $existe = $this->db->table('prefixes')->where('prefixe', $prefixe)->get()->getRow();
            if (! $existe) {
                $this->db->table('prefixes')->insert(['prefixe' => $prefixe]);
            }
        }
    }
}
