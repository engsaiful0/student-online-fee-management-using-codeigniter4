<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBatchSemestersTable extends Migration
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
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'semester_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'completed'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('batch_id');
        $this->forge->addKey('semester_id');
        $this->forge->addForeignKey('batch_id', 'batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('batch_semesters');
    }

    public function down()
    {
        $this->forge->dropTable('batch_semesters');
    }
}

