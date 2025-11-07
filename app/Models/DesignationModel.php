<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignationModel extends Model
{
    protected $table            = 'designations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'name', 'description', 'level', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'code'        => 'required|min_length[2]|max_length[20]|is_unique[designations.code]',
        'name'        => 'required|min_length[2]|max_length[100]',
        'description' => 'permit_empty',
        'level'       => 'permit_empty|integer|greater_than[0]|less_than_equal_to[100]',
        'status'      => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'code'        => 'required|min_length[2]|max_length[20]',
        'name'        => 'required|min_length[2]|max_length[100]',
        'description' => 'permit_empty',
        'level'       => 'permit_empty|integer|greater_than[0]|less_than_equal_to[100]',
        'status'      => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'code' => [
            'is_unique' => 'This designation code is already in use.',
        ],
        'level' => [
            'integer' => 'Level must be a number.',
            'greater_than' => 'Level must be greater than 0.',
            'less_than_equal_to' => 'Level cannot exceed 100.',
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
        $rules['code'] = 'required|min_length[2]|max_length[20]|is_unique[designations.code,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }
}

