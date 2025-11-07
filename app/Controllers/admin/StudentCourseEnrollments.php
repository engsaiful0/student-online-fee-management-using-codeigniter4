<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentCourseEnrollmentModel;
use App\Models\StudentModel;
use App\Models\CourseOfferingModel;
use App\Models\BatchModel;
use App\Models\CourseModel;

class StudentCourseEnrollments extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;
    protected $courseOfferingModel;
    protected $batchModel;
    protected $courseModel;

    public function __construct()
    {
        $this->enrollmentModel = new StudentCourseEnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->courseOfferingModel = new CourseOfferingModel();
        $this->batchModel = new BatchModel();
        $this->courseModel = new CourseModel();
    }

    public function index()
    {
        // Get batches and courses for filters
        $batches = $this->batchModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $courses = $this->courseModel->where('status', 'active')->orderBy('code', 'ASC')->findAll();
        
        $data = [
            'title' => 'Student Course Enrollments',
            'page' => 'admin',
            'batches' => $batches,
            'courses' => $courses
        ];
        $content = view('admin/student_course_enrollments/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getStudents()
    {
        try {
            $batchId = $this->request->getGet('batch_id');
            $search = $this->request->getGet('search');
            
            $builder = $this->studentModel->where('status', 'active');
            
            // Filter by batch if provided
            if (!empty($batchId)) {
                $builder->where('batch_id', $batchId);
            }
            
            // Search filter
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('student_id', $search)
                    ->orLike('email', $search)
                    ->groupEnd();
            }
            
            $students = $builder->orderBy('name', 'ASC')->findAll();
            
            // Format student data with batch info
            $formattedStudents = [];
            foreach ($students as $student) {
                $batch = null;
                if (!empty($student['batch_id'])) {
                    $batch = $this->batchModel->find($student['batch_id']);
                }
                
                $formattedStudents[] = [
                    'id' => $student['id'],
                    'name' => $student['name'],
                    'student_id' => $student['student_id'] ?? '-',
                    'email' => $student['email'],
                    'batch' => $batch ? $batch['name'] . ' (' . $batch['code'] . ')' : 'Not Assigned',
                    'batch_id' => $student['batch_id'] ?? null
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedStudents
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading students: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getEnrollments($studentId)
    {
        try {
            if (empty($studentId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student ID is required',
                    'data' => []
                ]);
            }

            $enrollments = $this->enrollmentModel->getEnrollmentsByStudent($studentId);
            
            $data = [];
            foreach ($enrollments as $enrollment) {
                // Get session from batch_semester dates or semester info
                $session = '-';
                if (!empty($enrollment['start_date']) && !empty($enrollment['end_date'])) {
                    $startYear = date('Y', strtotime($enrollment['start_date']));
                    $endYear = date('Y', strtotime($enrollment['end_date']));
                    $session = $startYear . '-' . $endYear;
                } elseif (!empty($enrollment['semester_name'])) {
                    $session = $enrollment['semester_name'];
                }
                
                $data[] = [
                    'id'                => $enrollment['id'],
                    'course_code'       => $enrollment['course_code'] ?? '-',
                    'course_title'      => $enrollment['course_title'] ?? '-',
                    'credit'            => $enrollment['credit'] ?? 0,
                    'batch'             => ($enrollment['batch_name'] ?? '-') . ' (' . ($enrollment['batch_code'] ?? '-') . ')',
                    'session'           => $session,
                    'teacher'           => $enrollment['teacher_name'] ?? 'Not Assigned',
                    'status'            => $enrollment['status'] ?? 'enrolled',
                    'enrollment_date'   => $enrollment['enrollment_date'] ?? '-',
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading enrollments: ' . $e->getMessage(),
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

            // Get filter parameters
            $courseId = $this->request->getGet('course_id');
            $batchId = $this->request->getGet('batch_id');
            $search = $this->request->getGet('search');

            $courses = $this->enrollmentModel->getAvailableOfferingsForStudent($studentId);
            
            $data = [];
            foreach ($courses as $course) {
                // Apply filters
                if (!empty($courseId) && $course['course_id'] != $courseId) {
                    continue;
                }
                if (!empty($batchId) && $course['batch_id'] != $batchId) {
                    continue;
                }
                if (!empty($search)) {
                    $searchLower = strtolower($search);
                    $courseCode = strtolower($course['course_code'] ?? '');
                    $courseTitle = strtolower($course['course_title'] ?? '');
                    if (strpos($courseCode, $searchLower) === false && strpos($courseTitle, $searchLower) === false) {
                        continue;
                    }
                }
                
                $session = '-';
                if (!empty($course['start_date']) && !empty($course['end_date'])) {
                    $startYear = date('Y', strtotime($course['start_date']));
                    $endYear = date('Y', strtotime($course['end_date']));
                    $session = $startYear . '-' . $endYear;
                } elseif (!empty($course['semester_name'])) {
                    $session = $course['semester_name'];
                }
                
                $capacity = (int)($course['capacity'] ?? 0);
                $enrolled = (int)($course['enrolled_count'] ?? 0);
                $available = max(0, $capacity - $enrolled);
                $percentage = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
                
                $data[] = [
                    'id'           => $course['id'],
                    'course_id'    => $course['course_id'] ?? null,
                    'course_code'  => $course['course_code'] ?? '-',
                    'course_title' => $course['course_title'] ?? '-',
                    'credit'       => $course['credit'] ?? 0,
                    'batch'        => ($course['batch_name'] ?? '-') . ' (' . ($course['batch_code'] ?? '-') . ')',
                    'batch_id'     => $course['batch_id'] ?? null,
                    'session'      => $session,
                    'capacity'     => $capacity,
                    'enrolled'     => $enrolled,
                    'available'    => $available,
                    'percentage'   => $percentage,
                    'is_full'      => $enrolled >= $capacity,
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'student_batch_id' => $student['batch_id'] ?? null
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading available courses: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function enroll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $studentId = $this->request->getPost('student_id');
        $courseOfferingId = $this->request->getPost('course_offering_id');

        if (empty($studentId) || empty($courseOfferingId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student ID and Course Offering ID are required.',
            ]);
        }

        // Check if already enrolled
        if ($this->enrollmentModel->isEnrolled($studentId, $courseOfferingId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student is already enrolled in this course.',
            ]);
        }

        // Check course offering capacity
        $offering = $this->courseOfferingModel->find($courseOfferingId);
        if (!$offering) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course offering not found.',
            ]);
        }

        if ($offering['enrolled_count'] >= $offering['capacity']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course is full. Cannot enroll more students.',
            ]);
        }

        $data = [
            'student_id'         => $studentId,
            'course_offering_id' => $courseOfferingId,
            'status'             => 'enrolled',
            'enrollment_date'    => date('Y-m-d'),
        ];

        try {
            // Start transaction
            $db = \Config\Database::connect();
            $db->transStart();

            // Insert enrollment
            if (!$this->enrollmentModel->insert($data)) {
                $errors = $this->enrollmentModel->errors();
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to enroll student: ' . implode(', ', $errors),
                ]);
            }

            // Update enrolled count
            $this->courseOfferingModel->update($courseOfferingId, [
                'enrolled_count' => $offering['enrolled_count'] + 1
            ]);

            // Update offering status if full
            if ($offering['enrolled_count'] + 1 >= $offering['capacity']) {
                $this->courseOfferingModel->update($courseOfferingId, [
                    'status' => 'full'
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaction failed. Please try again.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student enrolled successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function unenroll($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment not found'
            ]);
        }

        try {
            // Start transaction
            $db = \Config\Database::connect();
            $db->transStart();

            // Get course offering
            $offering = $this->courseOfferingModel->find($enrollment['course_offering_id']);

            // Update enrollment status to dropped
            if (!$this->enrollmentModel->update($id, ['status' => 'dropped'])) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to unenroll student.'
                ]);
            }

            // Decrease enrolled count
            if ($offering && $offering['enrolled_count'] > 0) {
                $newCount = $offering['enrolled_count'] - 1;
                $updateData = ['enrolled_count' => $newCount];
                
                // Update status if no longer full
                if ($offering['status'] === 'full' && $newCount < $offering['capacity']) {
                    $updateData['status'] = 'active';
                }
                
                $this->courseOfferingModel->update($enrollment['course_offering_id'], $updateData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaction failed. Please try again.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student unenrolled successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk enroll students in a course
     */
    public function bulkEnroll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request - must be AJAX']);
        }

        $studentIds = $this->request->getPost('student_ids');
        $courseOfferingId = $this->request->getPost('course_offering_id');

        if (empty($studentIds) || !is_array($studentIds) || empty($courseOfferingId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student IDs and Course Offering ID are required.',
            ]);
        }

        // Check course offering
        $offering = $this->courseOfferingModel->find($courseOfferingId);
        if (!$offering) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course offering not found.',
            ]);
        }

        $capacity = (int)($offering['capacity'] ?? 0);
        $enrolled = (int)($offering['enrolled_count'] ?? 0);
        $available = max(0, $capacity - $enrolled);

        if (count($studentIds) > $available) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Only {$available} seats available. Cannot enroll " . count($studentIds) . " students.",
            ]);
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($studentIds as $studentId) {
                // Check if already enrolled
                if ($this->enrollmentModel->isEnrolled($studentId, $courseOfferingId)) {
                    $failedCount++;
                    $errors[] = "Student ID {$studentId} is already enrolled";
                    continue;
                }

                $data = [
                    'student_id'         => $studentId,
                    'course_offering_id' => $courseOfferingId,
                    'status'             => 'enrolled',
                    'enrollment_date'    => date('Y-m-d'),
                ];

                if ($this->enrollmentModel->insert($data)) {
                    $successCount++;
                    $enrolled++;
                } else {
                    $failedCount++;
                    $errors[] = "Failed to enroll student ID {$studentId}";
                }
            }

            // Update enrolled count
            $this->courseOfferingModel->update($courseOfferingId, [
                'enrolled_count' => $enrolled
            ]);

            // Update offering status if full
            if ($enrolled >= $capacity) {
                $this->courseOfferingModel->update($courseOfferingId, [
                    'status' => 'full'
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaction failed. Please try again.',
                ]);
            }

            $message = "Successfully enrolled {$successCount} student(s)";
            if ($failedCount > 0) {
                $message .= ". {$failedCount} failed.";
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get enrollment statistics for a student
     */
    public function getEnrollmentStats($studentId)
    {
        try {
            if (empty($studentId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student ID is required',
                    'data' => null
                ]);
            }

            $enrollments = $this->enrollmentModel->getEnrollmentsByStudent($studentId);
            
            $stats = [
                'total_enrolled' => 0,
                'total_credits' => 0,
                'completed' => 0,
                'active' => 0
            ];

            foreach ($enrollments as $enrollment) {
                $stats['total_enrolled']++;
                $stats['total_credits'] += (int)($enrollment['credit'] ?? 0);
                
                if ($enrollment['status'] === 'completed') {
                    $stats['completed']++;
                } else {
                    $stats['active']++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
}

