<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['department_id', 'code', 'name', 'description', 'credit_hours', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'department_id' => 'required|integer|is_not_unique[departments.id]',
        'code'          => 'required|min_length[2]|max_length[20]|is_unique[courses.code]',
        'name'          => 'required|min_length[3]|max_length[100]',
        'description'   => 'permit_empty',
        'credit_hours'  => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
        'status'        => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'department_id' => 'required|integer|is_not_unique[departments.id]',
        'code'          => 'required|min_length[2]|max_length[20]',
        'name'          => 'required|min_length[3]|max_length[100]',
        'description'   => 'permit_empty',
        'credit_hours'  => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
        'status'        => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'code' => [
            'is_unique' => 'This course code is already in use.',
        ],
        'credit_hours' => [
            'integer' => 'Credit hours must be a number.',
            'greater_than' => 'Credit hours must be greater than 0.',
            'less_than_equal_to' => 'Credit hours cannot exceed 10.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Override validation rules for create/update
     */
    public function setValidationRules($rules = null)
    {
        if ($rules === null) {
            // For create operations, use create rules
            $rules = $this->validationRulesCreate;
        }
        
        $this->validationRules = $rules;
        return $this;
    }
    
    /**
     * Set validation rules specifically for update
     */
    public function setUpdateValidationRules($id)
    {
        $rules = $this->validationRulesUpdate;
        // Add uniqueness check excluding current ID
        $rules['code'] = 'required|min_length[2]|max_length[20]|is_unique[courses.code,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }

    /**
     * Get courses with department name
     */
    public function getCoursesWithDepartment()
    {
        return $this->select('courses.*, departments.name as department_name, departments.code as department_code')
                    ->join('departments', 'departments.id = courses.department_id')
                    ->findAll();
    }
}

