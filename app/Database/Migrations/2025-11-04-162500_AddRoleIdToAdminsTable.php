<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleIdToAdminsTable extends Migration
{
    public function up()
    {
        $fields = [
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'phone',
            ],
        ];
        
        $this->forge->addColumn('admins', $fields);
        
        // Add foreign key constraint
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('admins', 'admins_role_id_foreign');
        
        // Drop column
        $this->forge->dropColumn('admins', 'role_id');
    }
}

