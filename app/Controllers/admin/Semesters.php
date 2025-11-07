<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SemesterModel;
use App\Models\ProgramModel;

class Semesters extends BaseController
{
    protected $semesterModel;
    protected $programModel;

    public function __construct()
    {
        $this->semesterModel = new SemesterModel();
        $this->programModel = new ProgramModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Semesters',
            'page' => 'admin'
        ];
        $content = view('admin/semesters/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $semesters = $this->semesterModel->getSemestersWithProgram();
        
        $data = [];
        foreach ($semesters as $sem) {
            $data[] = [
                'id'             => $sem['id'],
                'department_name' => $sem['department_name'],
                'program_name'   => $sem['program_name'],
                'program_code'   => $sem['program_code'],
                'name'           => $sem['name'],
                'code'           => $sem['code'],
                'start_date'     => $sem['start_date'] ? date('Y-m-d', strtotime($sem['start_date'])) : '-',
                'end_date'       => $sem['end_date'] ? date('Y-m-d', strtotime($sem['end_date'])) : '-',
                'status'         => $sem['status'],
                'created_at'     => date('Y-m-d H:i:s', strtotime($sem['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
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
            'program_id' => $this->request->getPost('program_id'),
            'name'       => $this->request->getPost('name'),
            'code'       => $this->request->getPost('code'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date'   => $this->request->getPost('end_date') ?: null,
            'status'     => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['program_id']) || empty($data['name']) || empty($data['code'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program, Name, and Code are required fields.',
                'debug' => ['received_data' => $data]
            ]);
        }

        try {
            if (!$this->semesterModel->insert($data)) {
                $errors = $this->semesterModel->errors();
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

            $insertId = $this->semesterModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semester created successfully',
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

        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semester not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $semester
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        // Check if semester exists
        $semester = $this->semesterModel->find($id);
        if (!$semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semester not found'
            ]);
        }

        $data = [
            'program_id' => $this->request->getPost('program_id'),
            'name'       => $this->request->getPost('name'),
            'code'       => $this->request->getPost('code'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date'   => $this->request->getPost('end_date') ?: null,
            'status'     => $this->request->getPost('status'),
        ];

        try {
            if (!$this->semesterModel->update($id, $data)) {
                $errors = $this->semesterModel->errors();
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
                'message' => 'Semester updated successfully',
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

        $semester = $this->semesterModel->find($id);
        if (!$semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semester not found'
            ]);
        }

        try {
            if (!$this->semesterModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete semester. Please try again.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semester deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

