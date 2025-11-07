<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeModel extends Model
{
    protected $table            = 'fees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['student_id', 'fee_type', 'course_offering_id', 'fee_title', 'description', 'amount', 'paid_amount', 'due_date', 'payment_date', 'payment_method', 'transaction_id', 'receipt_number', 'status', 'authorized_by', 'authorized_at', 'remarks'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'student_id'  => 'required|integer|is_not_unique[students.id]',
        'fee_type'    => 'required|in_list[course_fee,tuition_fee,registration_fee,examination_fee,other]',
        'fee_title'   => 'required|min_length[3]|max_length[255]',
        'amount'      => 'required|decimal',
        'due_date'    => 'permit_empty|valid_date',
        'status'      => 'required|in_list[pending,paid,partial,overdue,cancelled]',
    ];
    protected $validationRulesUpdate = [
        'fee_type'    => 'permit_empty|in_list[course_fee,tuition_fee,registration_fee,examination_fee,other]',
        'fee_title'   => 'permit_empty|min_length[3]|max_length[255]',
        'amount'      => 'permit_empty|decimal',
        'paid_amount' => 'permit_empty|decimal',
        'status'      => 'permit_empty|in_list[pending,paid,partial,overdue,cancelled]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get fees for a student with course details
     */
    public function getFeesByStudent($studentId)
    {
        return $this->select('fees.*,
                            courses.code as course_code,
                            courses.name as course_name,
                            batches.name as batch_name,
                            batches.code as batch_code,
                            COALESCE(admins.name, "Not Assigned") as authorized_by_name')
                    ->join('course_offerings', 'course_offerings.id = fees.course_offering_id', 'left')
                    ->join('courses', 'courses.id = course_offerings.course_id', 'left')
                    ->join('batch_semesters', 'batch_semesters.id = course_offerings.batch_semester_id', 'left')
                    ->join('batches', 'batches.id = batch_semesters.batch_id', 'left')
                    ->join('admins', 'admins.id = fees.authorized_by', 'left')
                    ->where('fees.student_id', $studentId)
                    ->where('fees.status !=', 'cancelled')
                    ->orderBy('fees.due_date', 'ASC')
                    ->orderBy('fees.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending fees for authorization
     */
    public function getPendingFees()
    {
        return $this->select('fees.*,
                            students.name as student_name,
                            students.student_id as student_student_id,
                            students.email as student_email,
                            courses.code as course_code,
                            courses.name as course_name,
                            batches.name as batch_name,
                            batches.code as batch_code')
                    ->join('students', 'students.id = fees.student_id')
                    ->join('course_offerings', 'course_offerings.id = fees.course_offering_id', 'left')
                    ->join('courses', 'courses.id = course_offerings.course_id', 'left')
                    ->join('batch_semesters', 'batch_semesters.id = course_offerings.batch_semester_id', 'left')
                    ->join('batches', 'batches.id = batch_semesters.batch_id', 'left')
                    ->where('fees.status', 'pending')
                    ->orderBy('fees.payment_date', 'DESC')
                    ->orderBy('fees.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all fees for admin
     */
    public function getAllFees()
    {
        return $this->select('fees.*,
                            students.name as student_name,
                            students.student_id as student_student_id,
                            students.email as student_email,
                            courses.code as course_code,
                            courses.name as course_name,
                            batches.name as batch_name,
                            batches.code as batch_code,
                            COALESCE(admins.name, "Not Assigned") as authorized_by_name')
                    ->join('students', 'students.id = fees.student_id')
                    ->join('course_offerings', 'course_offerings.id = fees.course_offering_id', 'left')
                    ->join('courses', 'courses.id = course_offerings.course_id', 'left')
                    ->join('batch_semesters', 'batch_semesters.id = course_offerings.batch_semester_id', 'left')
                    ->join('batches', 'batches.id = batch_semesters.batch_id', 'left')
                    ->join('admins', 'admins.id = fees.authorized_by', 'left')
                    ->orderBy('fees.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Set validation rules for create operation
     */
    public function setCreateValidationRules()
    {
        $this->validationRules = $this->validationRulesCreate;
        return $this;
    }

    /**
     * Set validation rules for update operation
     */
    public function setUpdateValidationRules($id = null)
    {
        $this->validationRules = $this->validationRulesUpdate;
        return $this;
    }
}

