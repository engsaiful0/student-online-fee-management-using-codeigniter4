<?php

namespace App\Models;

use CodeIgniter\Model;

class BatchSemesterModel extends Model
{
    protected $table            = 'batch_semesters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['batch_id', 'semester_id', 'start_date', 'end_date', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'batch_id'    => 'required|integer|is_not_unique[batches.id]',
        'semester_id' => 'required|integer|is_not_unique[semesters.id]',
        'start_date'  => 'permit_empty|valid_date',
        'end_date'    => 'permit_empty|valid_date',
        'status'      => 'required|in_list[active,inactive,completed]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get batch-semester assignments with batch and semester info
     */
    public function getAssignmentsWithDetails()
    {
        return $this->select('batch_semesters.*, 
                            batches.code as batch_code, 
                            batches.name as batch_name,
                            semesters.name as semester_name,
                            semesters.code as semester_code,
                            programs.name as program_name,
                            programs.code as program_code,
                            departments.name as department_name')
                    ->join('batches', 'batches.id = batch_semesters.batch_id')
                    ->join('semesters', 'semesters.id = batch_semesters.semester_id')
                    ->join('programs', 'programs.id = semesters.program_id')
                    ->join('departments', 'departments.id = programs.department_id')
                    ->findAll();
    }

    /**
     * Check if assignment already exists
     */
    public function assignmentExists($batchId, $semesterId, $excludeId = null)
    {
        $builder = $this->where('batch_id', $batchId)
                       ->where('semester_id', $semesterId);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}

