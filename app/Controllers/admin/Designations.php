<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DesignationModel;

class Designations extends BaseController
{
    protected $designationModel;

    public function __construct()
    {
        $this->designationModel = new DesignationModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Designations',
            'page' => 'admin'
        ];
        $content = view('admin/designations/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $designations = $this->designationModel->orderBy('level', 'ASC')->orderBy('name', 'ASC')->findAll();
        
        $data = [];
        foreach ($designations as $designation) {
            $data[] = [
                'id'          => $designation['id'],
                'code'        => $designation['code'],
                'name'        => $designation['name'],
                'description' => $designation['description'] ?? '-',
                'level'       => $designation['level'] ?? '-',
                'status'      => $designation['status'],
                'created_at'  => date('Y-m-d H:i:s', strtotime($designation['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'code'        => $this->request->getPost('code'),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'level'       => $this->request->getPost('level') ?: null,
            'status'      => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['code']) || empty($data['name'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code and Name are required fields.',
            ]);
        }
        
        // Set validation rules for create
        $this->designationModel->setValidationRules();
        
        // Try to insert
        try {
            if (!$this->designationModel->insert($data)) {
                $errors = $this->designationModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->designationModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Designation created successfully',
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

        $designation = $this->designationModel->find($id);
        
        if (!$designation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Designation not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $designation
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if designation exists
        $designation = $this->designationModel->find($id);
        if (!$designation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Designation not found'
            ]);
        }

        $data = [
            'code'        => $this->request->getPost('code'),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'level'       => $this->request->getPost('level') ?: null,
            'status'      => $this->request->getPost('status'),
        ];

        // Set validation rules for update (excluding current ID from uniqueness check)
        $this->designationModel->setUpdateValidationRules($id);

        if (!$this->designationModel->update($id, $data)) {
            $errors = $this->designationModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check the form.',
                'errors'  => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Designation updated successfully',
            'csrf_token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $designation = $this->designationModel->find($id);
        if (!$designation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Designation not found'
            ]);
        }

        if (!$this->designationModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete designation. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Designation deleted successfully'
        ]);
    }
}

