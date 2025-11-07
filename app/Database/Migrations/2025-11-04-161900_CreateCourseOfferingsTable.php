<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseOfferingsTable extends Migration
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
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'batch_semester_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 30,
            ],
            'enrolled_count' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'full', 'completed'],
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
        $this->forge->addKey('course_id');
        $this->forge->addKey('batch_semester_id');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('batch_semester_id', 'batch_semesters', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('course_offerings');
    }

    public function down()
    {
        $this->forge->dropTable('course_offerings');
    }
}

