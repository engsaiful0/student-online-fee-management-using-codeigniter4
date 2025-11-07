<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function adminLogin(): string
    {
        $data = [
            'title' => 'Admin Sign In',
            'page' => 'admin',
            'login_type' => 'admin'
        ];
        return view('auth/login', $data);
    }

    public function studentLogin(): string
    {
        $data = [
            'title' => 'Student Sign In',
            'page' => 'student',
            'login_type' => 'student'
        ];
        return view('auth/login', $data);
    }

    public function processLogin()
    {
        // Get login credentials
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $login_type = $this->request->getPost('login_type');

        // Basic validation
        if (empty($email) || empty($password)) {
            session()->setFlashdata('error', 'Email and password are required');
            return redirect()->back();
        }

        // Authenticate admin
        if ($login_type === 'admin') {
            $adminModel = new \App\Models\AdminModel();
            $admin = $adminModel->authenticate($email, $password);
            
            if ($admin) {
                // Set admin session data
                session()->set('admin_id', $admin['id']);
                session()->set('admin_name', $admin['name']);
                session()->set('admin_email', $admin['email']);
                session()->setFlashdata('success', 'Welcome back, ' . $admin['name'] . '!');
                return redirect()->to('/admin/dashboard');
            } else {
                session()->setFlashdata('error', 'Invalid email or password');
                return redirect()->back();
            }
        } else {
            // Authenticate student
            $studentModel = new \App\Models\StudentModel();
            $student = $studentModel->authenticate($email, $password);
            
            if ($student) {
                // Set student session data
                session()->set('student_id', $student['id']);
                session()->set('student_name', $student['name']);
                session()->set('student_email', $student['email']);
                session()->set('student_student_id', $student['student_id']);
                session()->setFlashdata('success', 'Welcome back, ' . $student['name'] . '!');
                return redirect()->to('/student/dashboard');
            } else {
                session()->setFlashdata('error', 'Invalid email or password');
                return redirect()->back();
            }
        }
    }

    public function logout()
    {
        // Clear session data
        session()->remove('admin_id');
        session()->remove('admin_name');
        session()->remove('admin_email');
        session()->remove('student_id');
        session()->remove('student_name');
        session()->remove('student_email');
        session()->destroy();
        
        session()->setFlashdata('success', 'You have been logged out successfully');
        return redirect()->to('/');
    }
}

