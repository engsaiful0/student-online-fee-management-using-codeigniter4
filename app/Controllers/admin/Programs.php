<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProgramModel;
use App\Models\DepartmentModel;

class Programs extends BaseController
{
    protected $programModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Programs',
            'page' => 'admin'
        ];
        $content = view('admin/programs/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $programs = $this->programModel->getProgramsWithDepartment();
        
        $data = [];
        foreach ($programs as $prog) {
            $data[] = [
                'id'             => $prog['id'],
                'department_name' => $prog['department_name'],
                'code'           => $prog['code'],
                'name'           => $prog['name'],
                'description'    => $prog['description'] ?? '-',
                'duration_years' => $prog['duration_years'],
                'status'         => $prog['status'],
                'created_at'     => date('Y-m-d H:i:s', strtotime($prog['created_at'])),
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
            'duration_years' => $this->request->getPost('duration_years') ?? 4,
            'status'        => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['department_id']) || empty($data['code']) || empty($data['name'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department, Code, and Name are required fields.',
                'debug' => ['received_data' => $data]
            ]);
        }

        try {
            if (!$this->programModel->insert($data)) {
                $errors = $this->programModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                    'debug'   => [
                        'received_data' => $data,
                        'model_errors' => $errors
                    ]
                ]);
            }

            $insertId = $this->programModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program created successfully',
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

        $program = $this->programModel->find($id);
        
        if (!$program) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $program
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        // Check if program exists
        $program = $this->programModel->find($id);
        if (!$program) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program not found'
            ]);
        }

        $data = [
            'department_id' => $this->request->getPost('department_id'),
            'code'          => $this->request->getPost('code'),
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'duration_years' => $this->request->getPost('duration_years'),
            'status'        => $this->request->getPost('status'),
        ];

        try {
            if (!$this->programModel->update($id, $data)) {
                $errors = $this->programModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                    'debug'   => [
                        'received_data' => $data,
                        'model_errors' => $errors
                    ]
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program updated successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $program = $this->programModel->find($id);
        if (!$program) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program not found'
            ]);
        }

        try {
            if (!$this->programModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete program. Please try again.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

