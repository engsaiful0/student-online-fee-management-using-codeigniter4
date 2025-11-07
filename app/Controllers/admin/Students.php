<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\BatchModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;

class Students extends BaseController
{
    protected $studentModel;
    protected $batchModel;
    protected $departmentModel;
    protected $programModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->batchModel = new BatchModel();
        $this->departmentModel = new DepartmentModel();
        $this->programModel = new ProgramModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Students',
            'page' => 'admin'
        ];
        $content = view('admin/students/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $students = $this->studentModel->select('students.*, 
                                                batches.name as batch_name, 
                                                batches.code as batch_code,
                                                departments.name as department_name,
                                                departments.code as department_code,
                                                programs.name as program_name,
                                                programs.code as program_code')
                                        ->join('batches', 'batches.id = students.batch_id', 'left')
                                        ->join('departments', 'departments.id = students.department_id', 'left')
                                        ->join('programs', 'programs.id = students.program_id', 'left')
                                        ->orderBy('students.id', 'DESC')
                                        ->findAll();
        
        $data = [];
        foreach ($students as $student) {
            $data[] = [
                'id'               => $student['id'],
                'student_id'       => $student['student_id'] ?? '-',
                'name'             => $student['name'],
                'email'            => $student['email'],
                'phone'            => $student['phone'] ?? '-',
                'batch_name'       => $student['batch_name'] ?? '-',
                'batch_code'       => $student['batch_code'] ?? '-',
                'session'          => $student['session'] ?? '-',
                'department_name'  => $student['department_name'] ?? '-',
                'department_code'  => $student['department_code'] ?? '-',
                'program_name'     => $student['program_name'] ?? '-',
                'program_code'     => $student['program_code'] ?? '-',
                'status'           => $student['status'],
                'created_at'       => date('Y-m-d H:i:s', strtotime($student['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function getBatches()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $batches = $this->batchModel->where('status', 'active')->findAll();
        return $this->response->setJSON(['data' => $batches]);
    }

    public function getDepartments()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $departments = $this->departmentModel->where('status', 'active')->findAll();
        return $this->response->setJSON(['data' => $departments]);
    }

    public function getPrograms()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $programs = $this->programModel->where('status', 'active')->findAll();
        return $this->response->setJSON(['data' => $programs]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'student_id'    => $this->request->getPost('student_id'),
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'password'      => $this->request->getPost('password') ?: 'student123', // Default password
            'phone'         => $this->request->getPost('phone'),
            'address'       => $this->request->getPost('address'),
            'batch_id'      => $this->request->getPost('batch_id') ?: null,
            'session'       => $this->request->getPost('session'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'program_id'    => $this->request->getPost('program_id') ?: null,
            'status'        => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['name']) || empty($data['email'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Name and Email are required fields.',
            ]);
        }

        // Set validation rules for create
        $this->studentModel->setValidationRules();
        
        try {
            if (!$this->studentModel->insert($data)) {
                $errors = $this->studentModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->studentModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student created successfully',
                'id' => $insertId,
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadCsv()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $file = $this->request->getFile('csv_file');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please upload a valid CSV file.'
            ]);
        }

        if ($file->getExtension() !== 'csv') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only CSV files are allowed.'
            ]);
        }

        // Read CSV file
        $csvData = [];
        $filePath = $file->getTempName();
        
        // Read file content and remove BOM if present
        $fileContent = file_get_contents($filePath);
        // Remove UTF-8 BOM
        $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);
        // Remove UTF-16 BOM
        $fileContent = preg_replace('/^\xFE\xFF/', '', $fileContent);
        $fileContent = preg_replace('/^\xFF\xFE/', '', $fileContent);
        
        // Write cleaned content back to temp file
        file_put_contents($filePath, $fileContent);
        
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            // Read header row
            $headers = fgetcsv($handle);
            
            // Clean headers: remove quotes, trim, and handle BOM
            $normalizedHeaders = array_map(function($header) {
                // Remove quotes if present (both single and double quotes)
                $header = trim($header, '"\'');
                // Remove BOM characters (UTF-8 BOM)
                $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
                // Remove zero-width non-breaking space
                $header = preg_replace('/^\xE2\x80\x8B/', '', $header);
                // Remove any invisible unicode characters
                $header = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $header);
                // Trim whitespace
                $header = trim($header);
                // Convert to lowercase for comparison
                return strtolower($header);
            }, $headers);
            
            // Also store original headers for error messages
            $originalHeaders = $headers;
            
            $expectedHeaders = ['student id', 'student name', 'batch', 'session', 'department', 'program', 'status'];
            
            // Check if all expected headers are present
            $missingHeaders = array_diff($expectedHeaders, $normalizedHeaders);
            if (!empty($missingHeaders)) {
                $expectedDisplay = 'Student ID, Student Name, Batch, Session, Department, Program, Status';
                $foundDisplay = implode(', ', $originalHeaders);
                $missingDisplay = implode(', ', array_map(function($h) {
                    return ucwords(str_replace(' ', ' ', $h));
                }, $missingHeaders));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'CSV headers do not match. Expected: ' . $expectedDisplay . 
                                 '. Found: ' . $foundDisplay . 
                                 '. Missing: ' . $missingDisplay .
                                 '. Please download the template and use the exact header format.'
                ]);
            }
            
            // Check if there are extra headers (optional, just warn)
            $extraHeaders = array_diff($normalizedHeaders, $expectedHeaders);
            
            // Create a mapping array for easy access (using original header order)
            $headerMap = [];
            foreach ($normalizedHeaders as $index => $normalizedHeader) {
                $headerMap[$normalizedHeader] = $index;
            }

            $rowNum = 1;
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== FALSE) {
                $rowNum++;
                if (count($row) < count($normalizedHeaders)) continue; // Skip incomplete rows

                // Map row data using header map
                $rowData = [];
                foreach ($expectedHeaders as $expectedHeader) {
                    if (isset($headerMap[$expectedHeader]) && isset($row[$headerMap[$expectedHeader]])) {
                        $rowData[$expectedHeader] = trim($row[$headerMap[$expectedHeader]]);
                    } else {
                        $rowData[$expectedHeader] = '';
                    }
                }
                
                // Get IDs from names
                $batchId = null;
                $departmentId = null;
                $programId = null;

                if (!empty($rowData['batch'])) {
                    $batch = $this->batchModel->where('name', trim($rowData['batch']))
                                             ->orWhere('code', trim($rowData['batch']))
                                             ->first();
                    $batchId = $batch ? $batch['id'] : null;
                }

                if (!empty($rowData['department'])) {
                    $department = $this->departmentModel->where('name', trim($rowData['department']))
                                                       ->orWhere('code', trim($rowData['department']))
                                                       ->first();
                    $departmentId = $department ? $department['id'] : null;
                }

                if (!empty($rowData['program'])) {
                    $program = $this->programModel->where('name', trim($rowData['program']))
                                                  ->orWhere('code', trim($rowData['program']))
                                                  ->first();
                    $programId = $program ? $program['id'] : null;
                }

                // Generate email if not provided (use student_id or name)
                $email = '';
                if (!empty($rowData['student id'])) {
                    $email = strtolower(str_replace(' ', '', $rowData['student id'])) . '@student.example.com';
                } else {
                    $email = strtolower(str_replace(' ', '', $rowData['student name'])) . '@student.example.com';
                }

                // Check if email already exists
                $existingStudent = $this->studentModel->findByEmail($email);
                if ($existingStudent) {
                    $errors[] = "Row $rowNum: Email already exists for student ID: " . ($rowData['student id'] ?? 'N/A');
                    $errorCount++;
                    continue;
                }

                $studentData = [
                    'student_id'    => !empty($rowData['student id']) ? trim($rowData['student id']) : null,
                    'name'          => trim($rowData['student name']),
                    'email'         => $email,
                    'password'      => 'student123', // Default password
                    'batch_id'      => $batchId,
                    'session'       => !empty($rowData['session']) ? trim($rowData['session']) : null,
                    'department_id' => $departmentId,
                    'program_id'    => $programId,
                    'status'        => !empty($rowData['status']) ? strtolower(trim($rowData['status'])) : 'active',
                ];

                // Set validation rules
                $this->studentModel->setValidationRules();
                
                try {
                    if (!$this->studentModel->insert($studentData)) {
                        $modelErrors = $this->studentModel->errors();
                        $errors[] = "Row $rowNum: " . implode(', ', $modelErrors);
                        $errorCount++;
                    } else {
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row $rowNum: " . $e->getMessage();
                    $errorCount++;
                }
            }
            fclose($handle);
        }

        $message = "CSV import completed. Success: $successCount, Errors: $errorCount";
        if ($errorCount > 0) {
            $message .= "\nErrors:\n" . implode("\n", array_slice($errors, 0, 10)); // Show first 10 errors
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
            'csrf_token' => csrf_hash()
        ]);
    }

    public function edit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $student = $this->studentModel->find($id);
        
        if (!$student) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        // Remove password from response
        unset($student['password']);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $student
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $student = $this->studentModel->find($id);
        if (!$student) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $data = [
            'student_id'    => $this->request->getPost('student_id'),
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'address'       => $this->request->getPost('address'),
            'batch_id'      => $this->request->getPost('batch_id') ?: null,
            'session'       => $this->request->getPost('session'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'program_id'    => $this->request->getPost('program_id') ?: null,
            'status'        => $this->request->getPost('status'),
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        // Set validation rules for update
        $this->studentModel->setUpdateValidationRules($id);

        if (!$this->studentModel->update($id, $data)) {
            $errors = $this->studentModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check the form.',
                'errors'  => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Student updated successfully',
            'csrf_token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $student = $this->studentModel->find($id);
        if (!$student) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        if (!$this->studentModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete student. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }

    public function downloadTemplate()
    {
        // Get sample data from database
        $batches = $this->batchModel->where('status', 'active')->limit(3)->findAll();
        $departments = $this->departmentModel->where('status', 'active')->limit(3)->findAll();
        $programs = $this->programModel->where('status', 'active')->limit(3)->findAll();
        
        // Create CSV content
        $csvData = [];
        
        // Headers
        $csvData[] = ['Student ID', 'Student Name', 'Batch', 'Session', 'Department', 'Program', 'Status'];
        
        // Sample data rows with real database values
        $sampleCount = 3;
        $hasData = count($batches) > 0 && count($departments) > 0 && count($programs) > 0;
        
        if ($hasData) {
            // Use actual data from database
            for ($i = 0; $i < $sampleCount && $i < count($batches) && $i < count($departments) && $i < count($programs); $i++) {
                $csvData[] = [
                    'STU00' . ($i + 1),
                    'Student ' . ($i + 1) . ' Name',
                    $batches[$i]['name'],
                    '2024-2025',
                    $departments[$i]['name'],
                    $programs[$i]['name'],
                    'active'
                ];
            }
        } else {
            // Provide generic examples if no data exists
            $csvData[] = ['STU001', 'John Doe', '2024-2028', '2024-2025', 'Computer Science', 'BS Computer Science', 'active'];
            $csvData[] = ['STU002', 'Jane Smith', '2024-2028', '2024-2025', 'Mathematics', 'BS Mathematics', 'active'];
            $csvData[] = ['STU003', 'Bob Johnson', '2023-2027', '2023-2024', 'Physics', 'BS Physics', 'active'];
        }
        
        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        // Set response headers
        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="students_import_template.csv"');
        
        // Note: Not adding BOM to avoid header matching issues
        // The upload handler will strip BOM if present
        return $csvContent;
    }
}

