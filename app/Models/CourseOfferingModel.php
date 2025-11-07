<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseOfferingModel extends Model
{
    protected $table            = 'course_offerings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['course_id', 'batch_semester_id', 'capacity', 'enrolled_count', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'course_id'         => 'required|integer|is_not_unique[courses.id]',
        'batch_semester_id' => 'required|integer|is_not_unique[batch_semesters.id]',
        'capacity'          => 'permit_empty|integer|greater_than[0]|less_than_equal_to[500]',
        'enrolled_count'    => 'permit_empty|integer|greater_than_equal_to[0]',
        'status'            => 'required|in_list[active,inactive,full,completed]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get course offerings with all related details
     */
    public function getOfferingsWithDetails()
    {
        return $this->select('course_offerings.*, 
                            courses.code as course_code, 
                            courses.name as course_name,
                            courses.credit_hours as course_credit_hours,
                            batches.code as batch_code, 
                            batches.name as batch_name,
                            semesters.name as semester_name,
                            semesters.code as semester_code,
                            programs.name as program_name,
                            programs.code as program_code,
                            departments.name as department_name')
                    ->join('courses', 'courses.id = course_offerings.course_id', 'left')
                    ->join('batch_semesters', 'batch_semesters.id = course_offerings.batch_semester_id', 'left')
                    ->join('batches', 'batches.id = batch_semesters.batch_id', 'left')
                    ->join('semesters', 'semesters.id = batch_semesters.semester_id', 'left')
                    ->join('programs', 'programs.id = semesters.program_id', 'left')
                    ->join('departments', 'departments.id = programs.department_id', 'left')
                    ->orderBy('course_offerings.id', 'DESC')
                    ->findAll();
    }

    /**
     * Check if offering already exists
     */
    public function offeringExists($courseId, $batchSemesterId, $excludeId = null)
    {
        $builder = $this->where('course_id', $courseId)
                       ->where('batch_semester_id', $batchSemesterId);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}

