<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('admins');

        // Clear existing data (optional - uncomment if you want to reset)
        // $builder->truncate();

        // Sample admin data
        $admins = [
            [
                'name'       => 'Super Admin',
                'email'      => 'admin@example.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'phone'      => '+1234567890',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'John Admin',
                'email'      => 'john.admin@example.com',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'phone'      => '+1234567891',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Jane Manager',
                'email'      => 'jane.manager@example.com',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'phone'      => '+1234567892',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($admins as $admin) {
            // Check if admin already exists
            $exists = $builder->where('email', $admin['email'])->countAllResults(false);
            
            if ($exists == 0) {
                $builder->insert($admin);
            }
        }

        echo "AdminSeeder: Seeded " . count($admins) . " admin records.\n";
    }
}

