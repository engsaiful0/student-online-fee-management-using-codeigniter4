<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TeacherModel;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;

class Teachers extends BaseController
{
    protected $teacherModel;
    protected $departmentModel;
    protected $designationModel;

    public function __construct()
    {
        $this->teacherModel = new TeacherModel();
        $this->departmentModel = new DepartmentModel();
        $this->designationModel = new DesignationModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Teachers Management',
            'page' => 'admin'
        ];
        $content = view('admin/teachers/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $teachers = $this->teacherModel->getTeachersWithDetails();
        
        $data = [];
        foreach ($teachers as $teacher) {
            $data[] = [
                'id'                => $teacher['id'],
                'employee_id'       => $teacher['employee_id'] ?? '-',
                'name'              => $teacher['name'],
                'email'             => $teacher['email'],
                'phone'             => $teacher['phone'] ?? '-',
                'department'        => $teacher['department_name'] ? $teacher['department_name'] . ' (' . $teacher['department_code'] . ')' : '-',
                'designation'       => $teacher['designation_name'] ? $teacher['designation_name'] . ' (' . $teacher['designation_code'] . ')' : '-',
                'qualification'     => $teacher['qualification'] ?? '-',
                'experience_years'  => $teacher['experience_years'] ?? 0,
                'status'            => $teacher['status'],
                'created_at'        => $teacher['created_at'],
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

        $departments = $this->departmentModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        return $this->response->setJSON(['data' => $departments]);
    }

    public function getDesignations()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $designations = $this->designationModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        return $this->response->setJSON(['data' => $designations]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'employee_id'      => $this->request->getPost('employee_id') ? trim($this->request->getPost('employee_id')) : null,
            'name'             => trim($this->request->getPost('name')),
            'email'            => trim($this->request->getPost('email')),
            'phone'            => $this->request->getPost('phone') ? trim($this->request->getPost('phone')) : null,
            'department_id'    => $this->request->getPost('department_id') ? (int)$this->request->getPost('department_id') : null,
            'designation_id'   => $this->request->getPost('designation_id') ? (int)$this->request->getPost('designation_id') : null,
            'qualification'    => $this->request->getPost('qualification') ? trim($this->request->getPost('qualification')) : null,
            'specialization'   => $this->request->getPost('specialization') ? trim($this->request->getPost('specialization')) : null,
            'experience_years' => $this->request->getPost('experience_years') ? (int)$this->request->getPost('experience_years') : 0,
            'status'           => $this->request->getPost('status'),
        ];

        // Set validation rules for create
        $this->teacherModel->setValidationRules();
        
        try {
            if (!$this->teacherModel->insert($data)) {
                $errors = $this->teacherModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->teacherModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher created successfully',
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

        $teacher = $this->teacherModel->find($id);
        if (!$teacher) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $teacher
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $teacher = $this->teacherModel->find($id);
        if (!$teacher) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher not found'
            ]);
        }

        $data = [
            'employee_id'      => $this->request->getPost('employee_id') ? trim($this->request->getPost('employee_id')) : null,
            'name'             => trim($this->request->getPost('name')),
            'email'            => trim($this->request->getPost('email')),
            'phone'            => $this->request->getPost('phone') ? trim($this->request->getPost('phone')) : null,
            'department_id'    => $this->request->getPost('department_id') ? (int)$this->request->getPost('department_id') : null,
            'designation_id'   => $this->request->getPost('designation_id') ? (int)$this->request->getPost('designation_id') : null,
            'qualification'    => $this->request->getPost('qualification') ? trim($this->request->getPost('qualification')) : null,
            'specialization'   => $this->request->getPost('specialization') ? trim($this->request->getPost('specialization')) : null,
            'experience_years' => $this->request->getPost('experience_years') ? (int)$this->request->getPost('experience_years') : 0,
            'status'           => $this->request->getPost('status'),
        ];

        // Set validation rules for update
        $this->teacherModel->setUpdateValidationRules($id);
        
        try {
            if (!$this->teacherModel->update($id, $data)) {
                $errors = $this->teacherModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher updated successfully',
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

        $teacher = $this->teacherModel->find($id);
        if (!$teacher) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher not found'
            ]);
        }

        if (!$this->teacherModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete teacher. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ]);
    }
}

