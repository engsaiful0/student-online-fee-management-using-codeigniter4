<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentCourseEnrollmentModel;
use App\Models\StudentModel;

class EnrolledCourseList extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;

    public function __construct()
    {
        $this->enrollmentModel = new StudentCourseEnrollmentModel();
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Enrolled Course List',
            'page' => 'admin'
        ];
        $content = view('admin/enrolled_course_list/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getStudents()
    {
        try {
            $students = $this->studentModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
            return $this->response->setJSON([
                'success' => true,
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getAvailableCourses($studentId)
    {
        try {
            if (empty($studentId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student ID is required',
                    'data' => []
                ]);
            }

            // Get student info
            $student = $this->studentModel->find($studentId);
            if (!$student) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student not found',
                    'data' => []
                ]);
            }

            // Get available courses (not enrolled)
            $courses = $this->enrollmentModel->getAvailableOfferingsForStudent($studentId);
            
            $data = [];
            foreach ($courses as $course) {
                $session = '-';
                if (!empty($course['start_date']) && !empty($course['end_date'])) {
                    $startYear = date('Y', strtotime($course['start_date']));
                    $endYear = date('Y', strtotime($course['end_date']));
                    $session = $startYear . '-' . $endYear;
                } elseif (!empty($course['semester_name'])) {
                    $session = $course['semester_name'];
                }
                
                $data[] = [
                    'id'           => $course['id'],
                    'course_code'  => $course['course_code'] ?? '-',
                    'course_title' => $course['course_title'] ?? '-',
                    'credit'       => $course['credit'] ?? 0,
                    'batch'        => ($course['batch_name'] ?? '-') . ' (' . ($course['batch_code'] ?? '-') . ')',
                    'session'      => $session,
                    'capacity'     => $course['capacity'] ?? 0,
                    'enrolled'     => $course['enrolled_count'] ?? 0,
                    'available'    => ($course['capacity'] ?? 0) - ($course['enrolled_count'] ?? 0),
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'student' => [
                    'id' => $student['id'],
                    'name' => $student['name'],
                    'student_id' => $student['student_id'],
                    'batch_id' => $student['batch_id']
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading available courses: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }
}

