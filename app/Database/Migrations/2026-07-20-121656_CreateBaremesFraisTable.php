<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaremesFraisTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'type_operation' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, 
            ],
            'montant_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'montant_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('baremes_frais');
    }

    public function down()
    {
        $this->forge->dropTable('baremes_frais');
    }
}
