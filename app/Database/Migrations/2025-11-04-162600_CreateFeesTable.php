<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeesTable extends Migration
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
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'fee_type' => [
                'type'       => 'ENUM',
                'constraint' => ['course_fee', 'tuition_fee', 'registration_fee', 'examination_fee', 'other'],
                'default'    => 'course_fee',
            ],
            'course_offering_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'fee_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'paid_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'payment_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'receipt_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'partial', 'overdue', 'cancelled'],
                'default'    => 'pending',
            ],
            'authorized_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'authorized_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
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
        $this->forge->addKey('student_id');
        $this->forge->addKey('course_offering_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_offering_id', 'course_offerings', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('authorized_by', 'admins', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('fees');
    }

    public function down()
    {
        $this->forge->dropTable('fees');
    }
}

