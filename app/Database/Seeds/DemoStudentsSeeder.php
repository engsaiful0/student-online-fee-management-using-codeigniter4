<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoStudentsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('students');

        // Get IDs from related tables
        $batchModel = new \App\Models\BatchModel();
        $departmentModel = new \App\Models\DepartmentModel();
        $programModel = new \App\Models\ProgramModel();

        // Get first batch
        $batch = $batchModel->where('status', 'active')->first();
        $batchId = $batch ? $batch['id'] : null;

        // Get first department
        $department = $departmentModel->where('status', 'active')->first();
        $departmentId = $department ? $department['id'] : null;

        // Get first program
        $program = $programModel->where('status', 'active')->first();
        $programId = $program ? $program['id'] : null;

        // Sample student data
        $students = [
            [
                'student_id'    => 'STU001',
                'name'          => 'John Doe',
                'email'         => 'john.doe@student.example.com',
                'password'      => password_hash('student123', PASSWORD_DEFAULT),
                'phone'         => '+1234567890',
                'address'       => '123 Main Street, City, State 12345',
                'batch_id'      => $batchId,
                'session'       => '2024-2025',
                'department_id' => $departmentId,
                'program_id'    => $programId,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'student_id'    => 'STU002',
                'name'          => 'Jane Smith',
                'email'         => 'jane.smith@student.example.com',
                'password'      => password_hash('student123', PASSWORD_DEFAULT),
                'phone'         => '+1234567891',
                'address'       => '456 Oak Avenue, City, State 12345',
                'batch_id'      => $batchId,
                'session'       => '2024-2025',
                'department_id' => $departmentId,
                'program_id'    => $programId,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'student_id'    => 'STU003',
                'name'          => 'Bob Johnson',
                'email'         => 'bob.johnson@student.example.com',
                'password'      => password_hash('student123', PASSWORD_DEFAULT),
                'phone'         => '+1234567892',
                'address'       => '789 Pine Road, City, State 12345',
                'batch_id'      => $batchId,
                'session'       => '2024-2025',
                'department_id' => $departmentId,
                'program_id'    => $programId,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'student_id'    => 'STU004',
                'name'          => 'Alice Williams',
                'email'         => 'alice.williams@student.example.com',
                'password'      => password_hash('student123', PASSWORD_DEFAULT),
                'phone'         => '+1234567893',
                'address'       => '321 Elm Street, City, State 12345',
                'batch_id'      => $batchId,
                'session'       => '2024-2025',
                'department_id' => $departmentId,
                'program_id'    => $programId,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'student_id'    => 'STU005',
                'name'          => 'Charlie Brown',
                'email'         => 'charlie.brown@student.example.com',
                'password'      => password_hash('student123', PASSWORD_DEFAULT),
                'phone'         => '+1234567894',
                'address'       => '654 Maple Drive, City, State 12345',
                'batch_id'      => $batchId,
                'session'       => '2024-2025',
                'department_id' => $departmentId,
                'program_id'    => $programId,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $inserted = 0;
        foreach ($students as $student) {
            // Check if student already exists by email or student_id
            $exists = $builder->where('email', $student['email'])
                             ->orWhere('student_id', $student['student_id'])
                             ->countAllResults(false);
            
            if ($exists == 0) {
                $builder->insert($student);
                $inserted++;
            }
        }
        
        echo "DemoStudentsSeeder: Inserted $inserted new student record(s) out of " . count($students) . " total.\n";
    }
}

