<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table            = 'teachers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['employee_id', 'name', 'email', 'phone', 'department_id', 'designation_id', 'qualification', 'specialization', 'experience_years', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'employee_id'      => 'permit_empty|max_length[50]|is_unique[teachers.employee_id]',
        'name'             => 'required|min_length[3]|max_length[100]',
        'email'            => 'required|valid_email|is_unique[teachers.email]',
        'phone'            => 'permit_empty|max_length[20]',
        'department_id'    => 'permit_empty|integer|is_not_unique[departments.id]',
        'designation_id'   => 'permit_empty|integer|is_not_unique[designations.id]',
        'qualification'    => 'permit_empty|max_length[200]',
        'specialization'   => 'permit_empty',
        'experience_years' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[50]',
        'status'           => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'employee_id'      => 'permit_empty|max_length[50]',
        'name'             => 'required|min_length[3]|max_length[100]',
        'email'            => 'required|valid_email',
        'phone'            => 'permit_empty|max_length[20]',
        'department_id'    => 'permit_empty|integer|is_not_unique[departments.id]',
        'designation_id'   => 'permit_empty|integer|is_not_unique[designations.id]',
        'qualification'    => 'permit_empty|max_length[200]',
        'specialization'   => 'permit_empty',
        'experience_years' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[50]',
        'status'           => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'This email is already registered.',
        ],
        'employee_id' => [
            'is_unique' => 'This employee ID is already registered.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get teachers with department and designation details
     */
    public function getTeachersWithDetails()
    {
        return $this->select('teachers.*,
                            departments.name as department_name,
                            departments.code as department_code,
                            designations.name as designation_name,
                            designations.code as designation_code')
                    ->join('departments', 'departments.id = teachers.department_id', 'left')
                    ->join('designations', 'designations.id = teachers.designation_id', 'left')
                    ->findAll();
    }

    /**
     * Override validation rules for create/update
     */
    public function setValidationRules($rules = null)
    {
        if ($rules === null) {
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
        $rules['email'] = 'required|valid_email|is_unique[teachers.email,id,' . $id . ']';
        $rules['employee_id'] = 'permit_empty|max_length[50]|is_unique[teachers.employee_id,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }
}

