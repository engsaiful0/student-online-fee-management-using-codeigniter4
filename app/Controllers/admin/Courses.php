<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\DepartmentModel;

class Courses extends BaseController
{
    protected $courseModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Courses',
            'page' => 'admin'
        ];
        $content = view('admin/courses/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $courses = $this->courseModel->getCoursesWithDepartment();
        
        $data = [];
        foreach ($courses as $course) {
            $data[] = [
                'id'               => $course['id'],
                'code'             => $course['code'],
                'name'             => $course['name'],
                'department_id'    => $course['department_id'],
                'department_name'  => $course['department_name'],
                'department_code'  => $course['department_code'],
                'description'      => $course['description'] ?? '-',
                'credit_hours'     => $course['credit_hours'],
                'status'           => $course['status'],
                'created_at'       => date('Y-m-d H:i:s', strtotime($course['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function getDepartments()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $departments = $this->departmentModel->where('status', 'active')->findAll();
        return $this->response->setJSON(['data' => $departments]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'department_id' => $this->request->getPost('department_id'),
            'code'          => $this->request->getPost('code'),
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'credit_hours'  => $this->request->getPost('credit_hours') ?: 3,
            'status'        => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['department_id']) || empty($data['code']) || empty($data['name'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department, Code, and Name are required fields.',
            ]);
        }
        
        // Set validation rules for create
        $this->courseModel->setValidationRules();
        
        // Try to insert
        try {
            if (!$this->courseModel->insert($data)) {
                $errors = $this->courseModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->courseModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course created successfully',
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

    public function edit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $course = $this->courseModel->find($id);
        
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $course
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if course exists
        $course = $this->courseModel->find($id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ]);
        }

        $data = [
            'department_id' => $this->request->getPost('department_id'),
            'code'          => $this->request->getPost('code'),
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'credit_hours'  => $this->request->getPost('credit_hours'),
            'status'        => $this->request->getPost('status'),
        ];

        // Set validation rules for update (excluding current ID from uniqueness check)
        $this->courseModel->setUpdateValidationRules($id);

        if (!$this->courseModel->update($id, $data)) {
            $errors = $this->courseModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check the form.',
                'errors'  => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Course updated successfully',
            'csrf_token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ]);
        }

        if (!$this->courseModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete course. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }
}

