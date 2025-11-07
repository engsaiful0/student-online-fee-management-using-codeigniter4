<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentCourseEnrollmentModel extends Model
{
    protected $table            = 'student_course_enrollments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['student_id', 'course_offering_id', 'teacher_id', 'status', 'enrollment_date'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'student_id'         => 'required|integer|is_not_unique[students.id]',
        'course_offering_id' => 'required|integer|is_not_unique[course_offerings.id]',
        'teacher_id'         => 'permit_empty|integer',
        'status'             => 'required|in_list[enrolled,dropped,completed]',
        'enrollment_date'    => 'permit_empty|valid_date',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get enrollments for a student with course details
     */
    public function getEnrollmentsByStudent($studentId)
    {
        return $this->select('student_course_enrollments.*,
                            courses.code as course_code,
                            courses.name as course_title,
                            courses.credit_hours as credit,
                            batches.name as batch_name,
                            batches.code as batch_code,
                            batch_semesters.start_date,
                            batch_semesters.end_date,
                            semesters.name as semester_name,
                            semesters.code as semester_code,
                            COALESCE(admins.name, "Not Assigned") as teacher_name,
                            course_offerings.status as offering_status')
                    ->join('course_offerings', 'course_offerings.id = student_course_enrollments.course_offering_id')
                    ->join('courses', 'courses.id = course_offerings.course_id')
                    ->join('batch_semesters', 'batch_semesters.id = course_offerings.batch_semester_id')
                    ->join('batches', 'batches.id = batch_semesters.batch_id')
                    ->join('semesters', 'semesters.id = batch_semesters.semester_id')
                    ->join('admins', 'admins.id = student_course_enrollments.teacher_id', 'left')
                    ->where('student_course_enrollments.student_id', $studentId)
                    ->where('student_course_enrollments.status !=', 'dropped')
                    ->findAll();
    }

    /**
     * Check if student is already enrolled in a course offering
     */
    public function isEnrolled($studentId, $courseOfferingId)
    {
        return $this->where('student_id', $studentId)
                   ->where('course_offering_id', $courseOfferingId)
                   ->where('status', 'enrolled')
                   ->countAllResults() > 0;
    }

    /**
     * Get available course offerings for a student (not yet enrolled)
     */
    public function getAvailableOfferingsForStudent($studentId)
    {
        $db = \Config\Database::connect();
        
        // Get student's batch_id
        $studentModel = new \App\Models\StudentModel();
        $student = $studentModel->find($studentId);
        
        if (!$student || !$student['batch_id']) {
            return [];
        }
        
        // Get enrolled course offering IDs
        $enrolledRecords = $this->select('course_offering_id')
                           ->where('student_id', $studentId)
                           ->where('status', 'enrolled')
                           ->findAll();
        $enrolledIds = array_column($enrolledRecords, 'course_offering_id');
        
        // Get available offerings for student's batch
        $builder = $db->table('course_offerings');
        $builder->select('course_offerings.*,
                        course_offerings.course_id,
                        courses.code as course_code,
                        courses.name as course_title,
                        courses.credit_hours as credit,
                        batches.id as batch_id,
                        batches.name as batch_name,
                        batches.code as batch_code,
                        batch_semesters.start_date,
                        batch_semesters.end_date,
                        semesters.name as semester_name,
                        semesters.code as semester_code')
                ->join('courses', 'courses.id = course_offerings.course_id')
                ->join('batch_semesters', 'batch_semesters.id = course_offerings.batch_semester_id')
                ->join('batches', 'batches.id = batch_semesters.batch_id')
                ->join('semesters', 'semesters.id = batch_semesters.semester_id')
                ->where('batch_semesters.batch_id', $student['batch_id'])
                ->where('course_offerings.status', 'active');
        
        if (!empty($enrolledIds)) {
            $builder->whereNotIn('course_offerings.id', $enrolledIds);
        }
        
        return $builder->get()->getResultArray();
    }
}

