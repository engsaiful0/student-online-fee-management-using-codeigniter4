<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;

class Departments extends BaseController
{
    protected $departmentModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Departments',
            'page' => 'admin'
        ];
        $content = view('admin/departments/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $departments = $this->departmentModel->findAll();
        
        $data = [];
        foreach ($departments as $dept) {
            $data[] = [
                'id'          => $dept['id'],
                'code'        => $dept['code'],
                'name'        => $dept['name'],
                'description' => $dept['description'] ?? '-',
                'status'      => $dept['status'],
                'created_at'  => date('Y-m-d H:i:s', strtotime($dept['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function create()
    {
        // Log the request for debugging
        $postData = $this->request->getPost();
        log_message('debug', 'Department Create - Request Method: ' . $this->request->getMethod());
        log_message('debug', 'Department Create - Is AJAX: ' . ($this->request->isAJAX() ? 'Yes' : 'No'));
        log_message('debug', 'Department Create - Post Data: ' . json_encode($postData));
        log_message('debug', 'Department Create - All Headers: ' . json_encode($this->request->getHeaders()));
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'Department Create - Not an AJAX request');
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'code'        => $this->request->getPost('code'),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?? 'active',
        ];

        // Log the data being inserted
        log_message('debug', 'Department Data to Insert: ' . json_encode($data));

        // Check if required fields are empty
        if (empty($data['code']) || empty($data['name'])) {
            log_message('error', 'Department Create - Missing required fields');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code and Name are required fields.',
                'debug' => ['received_data' => $data]
            ]);
        }
        
        // Set validation rules for create
        $this->departmentModel->setValidationRules();
        
        // Try to insert
        try {
            if (!$this->departmentModel->insert($data)) {
                $errors = $this->departmentModel->errors();
                log_message('error', 'Department Insert Failed: ' . json_encode($errors));
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

            $insertId = $this->departmentModel->getInsertID();
            log_message('debug', 'Department Created Successfully with ID: ' . $insertId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Department created successfully',
                'id' => $insertId,
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Department Create Exception: ' . $e->getMessage());
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

        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $department
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if department exists
        $department = $this->departmentModel->find($id);
        if (!$department) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ]);
        }

        $data = [
            'code'        => $this->request->getPost('code'),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        // Set validation rules for update (excluding current ID from uniqueness check)
        $this->departmentModel->setUpdateValidationRules($id);

        if (!$this->departmentModel->update($id, $data)) {
            $errors = $this->departmentModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check the form.',
                'errors'  => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Department updated successfully',
            'csrf_token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $department = $this->departmentModel->find($id);
        if (!$department) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ]);
        }

        if (!$this->departmentModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete department. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Department deleted successfully'
        ]);
    }
}

