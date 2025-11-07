<?php

namespace App\Controllers;

use App\Models\StudentCourseEnrollmentModel;
use App\Models\StudentModel;
use App\Models\BatchModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;

class Student extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;
    protected $batchModel;
    protected $departmentModel;
    protected $programModel;

    public function __construct()
    {
        $this->enrollmentModel = new StudentCourseEnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->batchModel = new BatchModel();
        $this->departmentModel = new DepartmentModel();
        $this->programModel = new ProgramModel();
    }

    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        // Check if student is logged in
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access your dashboard');
            return redirect()->to('/auth/student');
        }

        // Get student information
        $student = $this->studentModel->find($studentId);
        if (!$student) {
            session()->setFlashdata('error', 'Student not found');
            return redirect()->to('/auth/student');
        }

        // Get enrolled courses for this student
        $enrolledCourses = $this->enrollmentModel->getEnrollmentsByStudent($studentId);

        $data = [
            'title' => 'Student Dashboard',
            'page' => 'student',
            'student' => $student,
            'enrolledCourses' => $enrolledCourses
        ];
        $content = view('student/dashboard_content', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function profile()
    {
        // Check if student is logged in
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access your profile');
            return redirect()->to('/auth/student');
        }

        // Get student information with related data
        $student = $this->studentModel->select('students.*, 
                                                batches.name as batch_name, 
                                                batches.code as batch_code,
                                                departments.name as department_name,
                                                departments.code as department_code,
                                                programs.name as program_name,
                                                programs.code as program_code')
                                        ->join('batches', 'batches.id = students.batch_id', 'left')
                                        ->join('departments', 'departments.id = students.department_id', 'left')
                                        ->join('programs', 'programs.id = students.program_id', 'left')
                                        ->where('students.id', $studentId)
                                        ->first();

        if (!$student) {
            session()->setFlashdata('error', 'Student not found');
            return redirect()->to('/auth/student');
        }

        // Remove password from display
        unset($student['password']);

        $data = [
            'title' => 'My Profile',
            'page' => 'student',
            'student' => $student
        ];
        $content = view('student/profile', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function updatePassword()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        // Get student ID from session
        $studentId = session()->get('student_id');
        
        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student session not found. Please login again.'
            ]);
        }

        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validation
        if (empty($newPassword) || empty($confirmPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All password fields are required.'
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'New password and confirm password do not match.'
            ]);
        }

        if (strlen($newPassword) < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'New password must be at least 6 characters long.'
            ]);
        }

        // Get student data
        $student = $this->studentModel->find($studentId);
        if (!$student) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found.'
            ]);
        }

        // Update password
        try {
            // The model's beforeUpdate callback will hash the password automatically
            if (!$this->studentModel->update($studentId, ['password' => $newPassword])) {
                $errors = $this->studentModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update password. ' . implode(', ', $errors)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password updated successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

