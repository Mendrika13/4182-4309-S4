<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrefixesTable extends Migration
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
            'prefixe' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('prefixe');
        $this->forge->createTable('prefixes');
    }

    public function down()
    {
        $this->forge->dropTable('prefixes');
    }
}
