<?php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table            = 'semesters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['program_id', 'name', 'code', 'start_date', 'end_date', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'program_id' => 'required|integer|is_not_unique[programs.id]',
        'name'       => 'required|min_length[2]|max_length[50]',
        'code'       => 'required|min_length[2]|max_length[20]',
        'start_date' => 'permit_empty|valid_date',
        'end_date'   => 'permit_empty|valid_date',
        'status'     => 'required|in_list[active,inactive,completed]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get semesters with program and department info
     */
    public function getSemestersWithProgram()
    {
        return $this->select('semesters.*, programs.name as program_name, programs.code as program_code, departments.name as department_name')
                    ->join('programs', 'programs.id = semesters.program_id')
                    ->join('departments', 'departments.id = programs.department_id')
                    ->findAll();
    }
}

