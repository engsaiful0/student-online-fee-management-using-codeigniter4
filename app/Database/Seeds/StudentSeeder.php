<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('students');

        // Clear existing data (optional - uncomment if you want to reset)
        // $builder->truncate();

        // Sample student data
        $students = [
            [
                'name'       => 'Alice Johnson',
                'email'      => 'alice.student@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'student_id' => 'STU001',
                'phone'      => '+1987654321',
                'address'    => '123 Main Street, City, State 12345',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Bob Smith',
                'email'      => 'bob.smith@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'student_id' => 'STU002',
                'phone'      => '+1987654322',
                'address'    => '456 Oak Avenue, City, State 12345',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Charlie Brown',
                'email'      => 'charlie.brown@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'student_id' => 'STU003',
                'phone'      => '+1987654323',
                'address'    => '789 Pine Road, City, State 12345',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Diana Prince',
                'email'      => 'diana.prince@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'student_id' => 'STU004',
                'phone'      => '+1987654324',
                'address'    => '321 Elm Street, City, State 12345',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Edward Wilson',
                'email'      => 'edward.wilson@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'student_id' => 'STU005',
                'phone'      => '+1987654325',
                'address'    => '654 Maple Drive, City, State 12345',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($students as $student) {
            // Check if student already exists
            $exists = $builder->where('email', $student['email'])->countAllResults(false);
            
            if ($exists == 0) {
                $builder->insert($student);
            }
        }

        echo "StudentSeeder: Seeded " . count($students) . " student records.\n";
    }
}

