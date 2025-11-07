<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseOfferingModel;
use App\Models\CourseModel;
use App\Models\BatchSemesterModel;

class CourseOfferings extends BaseController
{
    protected $courseOfferingModel;
    protected $courseModel;
    protected $batchSemesterModel;

    public function __construct()
    {
        $this->courseOfferingModel = new CourseOfferingModel();
        $this->courseModel = new CourseModel();
        $this->batchSemesterModel = new BatchSemesterModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Course Offerings',
            'page' => 'admin'
        ];
        $content = view('admin/course_offerings/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        // DataTables sends AJAX requests, but we'll allow all requests for compatibility
        try {
            $offerings = $this->courseOfferingModel->getOfferingsWithDetails();
            
            // Log for debugging
            log_message('debug', 'Course Offerings Count: ' . count($offerings));
            
            $data = [];
            if (!empty($offerings)) {
                foreach ($offerings as $offering) {
                    $data[] = [
                        'id'                    => $offering['id'] ?? null,
                        'course_id'             => $offering['course_id'] ?? null,
                        'course_code'           => $offering['course_code'] ?? '-',
                        'course_name'           => $offering['course_name'] ?? '-',
                        'course_credit_hours'   => $offering['course_credit_hours'] ?? 0,
                        'batch_semester_id'     => $offering['batch_semester_id'] ?? null,
                        'batch_code'            => $offering['batch_code'] ?? '-',
                        'batch_name'            => $offering['batch_name'] ?? '-',
                        'semester_name'         => $offering['semester_name'] ?? '-',
                        'semester_code'         => $offering['semester_code'] ?? '-',
                        'program_name'          => $offering['program_name'] ?? '-',
                        'program_code'          => $offering['program_code'] ?? '-',
                        'department_name'       => $offering['department_name'] ?? '-',
                        'capacity'              => $offering['capacity'] ?? 0,
                        'enrolled_count'        => $offering['enrolled_count'] ?? 0,
                        'status'                => $offering['status'] ?? 'active',
                        'created_at'            => !empty($offering['created_at']) ? date('Y-m-d H:i:s', strtotime($offering['created_at'])) : '-',
                    ];
                }
            }

            $response = [
                'data' => $data
            ];
            
            // Log response for debugging
            log_message('debug', 'Course Offerings Response: ' . json_encode($response));
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Course Offerings getData Error: ' . $e->getMessage());
            log_message('error', 'Course Offerings getData Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    public function getCourses()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $courses = $this->courseModel->where('status', 'active')->findAll();
        return $this->response->setJSON(['data' => $courses]);
    }

    public function getBatchSemesters()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $assignments = $this->batchSemesterModel->getAssignmentsWithDetails();
        return $this->response->setJSON(['data' => $assignments]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $data = [
            'course_id'         => $this->request->getPost('course_id'),
            'batch_semester_id' => $this->request->getPost('batch_semester_id'),
            'capacity'          => $this->request->getPost('capacity') ?: 30,
            'enrolled_count'    => $this->request->getPost('enrolled_count') ?: 0,
            'status'            => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['course_id']) || empty($data['batch_semester_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course and Batch-Semester are required fields.',
            ]);
        }

        // Check if offering already exists
        if ($this->courseOfferingModel->offeringExists($data['course_id'], $data['batch_semester_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This course is already offered in this batch-semester.',
            ]);
        }

        // Validate enrolled_count doesn't exceed capacity
        if ($data['enrolled_count'] > $data['capacity']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrolled count cannot exceed capacity.',
            ]);
        }

        try {
            if (!$this->courseOfferingModel->insert($data)) {
                $errors = $this->courseOfferingModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->courseOfferingModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course offering created successfully',
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

        $offering = $this->courseOfferingModel->find($id);
        
        if (!$offering) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course offering not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $offering
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if offering exists
        $offering = $this->courseOfferingModel->find($id);
        if (!$offering) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course offering not found'
            ]);
        }

        $data = [
            'course_id'         => $this->request->getPost('course_id'),
            'batch_semester_id' => $this->request->getPost('batch_semester_id'),
            'capacity'          => $this->request->getPost('capacity'),
            'enrolled_count'    => $this->request->getPost('enrolled_count'),
            'status'            => $this->request->getPost('status'),
        ];

        // Check if offering already exists (excluding current one)
        if ($this->courseOfferingModel->offeringExists($data['course_id'], $data['batch_semester_id'], $id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This course is already offered in this batch-semester.',
            ]);
        }

        // Validate enrolled_count doesn't exceed capacity
        if ($data['enrolled_count'] > $data['capacity']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrolled count cannot exceed capacity.',
            ]);
        }

        try {
            if (!$this->courseOfferingModel->update($id, $data)) {
                $errors = $this->courseOfferingModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course offering updated successfully',
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

        $offering = $this->courseOfferingModel->find($id);
        if (!$offering) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course offering not found'
            ]);
        }

        try {
            if (!$this->courseOfferingModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete course offering. Please try again.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course offering deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

