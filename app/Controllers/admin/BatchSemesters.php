<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BatchSemesterModel;
use App\Models\BatchModel;
use App\Models\SemesterModel;

class BatchSemesters extends BaseController
{
    protected $batchSemesterModel;
    protected $batchModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->batchSemesterModel = new BatchSemesterModel();
        $this->batchModel = new BatchModel();
        $this->semesterModel = new SemesterModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Batch Semester Assignments',
            'page' => 'admin'
        ];
        $content = view('admin/batch_semesters/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $assignments = $this->batchSemesterModel->getAssignmentsWithDetails();
        
        $data = [];
        foreach ($assignments as $assignment) {
            $data[] = [
                'id'               => $assignment['id'],
                'batch_id'         => $assignment['batch_id'],
                'batch_code'       => $assignment['batch_code'],
                'batch_name'       => $assignment['batch_name'],
                'semester_id'      => $assignment['semester_id'],
                'semester_name'    => $assignment['semester_name'],
                'semester_code'    => $assignment['semester_code'],
                'program_name'     => $assignment['program_name'],
                'program_code'     => $assignment['program_code'],
                'department_name'  => $assignment['department_name'],
                'start_date'       => $assignment['start_date'] ? date('Y-m-d', strtotime($assignment['start_date'])) : '-',
                'end_date'         => $assignment['end_date'] ? date('Y-m-d', strtotime($assignment['end_date'])) : '-',
                'status'           => $assignment['status'],
                'created_at'       => date('Y-m-d H:i:s', strtotime($assignment['created_at'])),
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

    public function getSemesters()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $semesters = $this->semesterModel->getSemestersWithProgram();
        return $this->response->setJSON(['data' => $semesters]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'batch_id'    => $this->request->getPost('batch_id'),
            'semester_id' => $this->request->getPost('semester_id'),
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'status'      => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['batch_id']) || empty($data['semester_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Batch and Semester are required fields.',
            ]);
        }

        // Check if assignment already exists
        if ($this->batchSemesterModel->assignmentExists($data['batch_id'], $data['semester_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This semester is already assigned to this batch.',
            ]);
        }

        // Validate dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'End date must be after start date.',
                ]);
            }
        }

        try {
            if (!$this->batchSemesterModel->insert($data)) {
                $errors = $this->batchSemesterModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->batchSemesterModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semester assigned to batch successfully',
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
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $assignment = $this->batchSemesterModel->find($id);
        
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $assignment
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if assignment exists
        $assignment = $this->batchSemesterModel->find($id);
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment not found'
            ]);
        }

        $data = [
            'batch_id'    => $this->request->getPost('batch_id'),
            'semester_id' => $this->request->getPost('semester_id'),
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'status'      => $this->request->getPost('status'),
        ];

        // Check if assignment already exists (excluding current one)
        if ($this->batchSemesterModel->assignmentExists($data['batch_id'], $data['semester_id'], $id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This semester is already assigned to this batch.',
            ]);
        }

        // Validate dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'End date must be after start date.',
                ]);
            }
        }

        try {
            if (!$this->batchSemesterModel->update($id, $data)) {
                $errors = $this->batchSemesterModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment updated successfully',
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
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $assignment = $this->batchSemesterModel->find($id);
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment not found'
            ]);
        }

        try {
            if (!$this->batchSemesterModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete assignment. Please try again.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

