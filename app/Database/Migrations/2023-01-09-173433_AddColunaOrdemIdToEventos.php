<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColunaOrdemIdToEventos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('eventos', [
            'ordem_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
                'after' => 'conta_id'
            ],
        ]);

        $sql = "ALTER TABLE eventos 
                ADD CONSTRAINT eventos_ordem_id_foreign
                FOREIGN KEY (ordem_id) REFERENCES ordens(id)
                ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->db->simpleQuery($sql);
    }

    public function down()
    {
        $this->forge->dropForeignKey('eventos', 'eventos_ordem_id_foreign');

        $this->forge->dropColumn('eventos', 'ordem_id');
    }
}
