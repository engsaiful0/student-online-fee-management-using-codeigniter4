<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BatchModel;

class Batches extends BaseController
{
    protected $batchModel;

    public function __construct()
    {
        $this->batchModel = new BatchModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Batches',
            'page' => 'admin'
        ];
        $content = view('admin/batches/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $batches = $this->batchModel->findAll();
        
        $data = [];
        foreach ($batches as $batch) {
            $data[] = [
                'id'          => $batch['id'],
                'code'        => $batch['code'],
                'name'        => $batch['name'],
                'start_year'  => $batch['start_year'],
                'end_year'    => $batch['end_year'],
                'description' => $batch['description'] ?? '-',
                'status'      => $batch['status'],
                'created_at'  => date('Y-m-d H:i:s', strtotime($batch['created_at'])),
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
            'start_year'  => $this->request->getPost('start_year'),
            'end_year'    => $this->request->getPost('end_year'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['code']) || empty($data['name']) || empty($data['start_year']) || empty($data['end_year'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code, Name, Start Year, and End Year are required fields.',
            ]);
        }

        // Validate end_year is greater than start_year
        if ($data['end_year'] <= $data['start_year']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'End year must be greater than start year.',
            ]);
        }
        
        // Set validation rules for create
        $this->batchModel->setValidationRules();
        
        // Try to insert
        try {
            if (!$this->batchModel->insert($data)) {
                $errors = $this->batchModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->batchModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Batch created successfully',
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

        $batch = $this->batchModel->find($id);
        
        if (!$batch) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Batch not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $batch
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if batch exists
        $batch = $this->batchModel->find($id);
        if (!$batch) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Batch not found'
            ]);
        }

        $data = [
            'code'        => $this->request->getPost('code'),
            'name'        => $this->request->getPost('name'),
            'start_year'  => $this->request->getPost('start_year'),
            'end_year'    => $this->request->getPost('end_year'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        // Validate end_year is greater than start_year
        if ($data['end_year'] <= $data['start_year']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'End year must be greater than start year.',
            ]);
        }

        // Set validation rules for update (excluding current ID from uniqueness check)
        $this->batchModel->setUpdateValidationRules($id);

        if (!$this->batchModel->update($id, $data)) {
            $errors = $this->batchModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check the form.',
                'errors'  => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Batch updated successfully',
            'csrf_token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $batch = $this->batchModel->find($id);
        if (!$batch) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Batch not found'
            ]);
        }

        if (!$this->batchModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete batch. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Batch deleted successfully'
        ]);
    }
}

