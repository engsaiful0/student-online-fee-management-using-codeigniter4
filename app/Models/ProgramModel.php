<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table            = 'programs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['department_id', 'code', 'name', 'description', 'duration_years', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'department_id' => 'required|integer|is_not_unique[departments.id]',
        'code'          => 'required|min_length[2]|max_length[20]',
        'name'          => 'required|min_length[3]|max_length[100]',
        'description'   => 'permit_empty',
        'duration_years' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
        'status'        => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get programs with department name
     */
    public function getProgramsWithDepartment()
    {
        return $this->select('programs.*, departments.name as department_name')
                    ->join('departments', 'departments.id = programs.department_id')
                    ->findAll();
    }
}

