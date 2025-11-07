<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToStudentsTable extends Migration
{
    public function up()
    {
        $fields = [
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'student_id',
            ],
            'session' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'batch_id',
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'session',
            ],
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'department_id',
            ],
        ];

        $this->forge->addColumn('students', $fields);

        // Add foreign keys
        $this->forge->addForeignKey('batch_id', 'batches', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Drop foreign keys first
        $this->forge->dropForeignKey('students', 'students_batch_id_foreign');
        $this->forge->dropForeignKey('students', 'students_department_id_foreign');
        $this->forge->dropForeignKey('students', 'students_program_id_foreign');
        
        // Drop columns
        $this->forge->dropColumn('students', ['batch_id', 'session', 'department_id', 'program_id']);
    }
}

