<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('App\Database\Seeds\PrefixeSeeder');
        $this->call('App\Database\Seeds\BaremeFraisSeeder');
    }
}
