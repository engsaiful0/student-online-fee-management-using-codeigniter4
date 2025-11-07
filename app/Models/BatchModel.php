<?php

namespace App\Models;

use CodeIgniter\Model;

class BatchModel extends Model
{
    protected $table            = 'batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'name', 'start_year', 'end_year', 'description', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'code'        => 'required|min_length[2]|max_length[20]|is_unique[batches.code]',
        'name'        => 'required|min_length[3]|max_length[100]',
        'start_year'  => 'required|numeric|min_length[4]|max_length[4]',
        'end_year'    => 'required|numeric|min_length[4]|max_length[4]',
        'description' => 'permit_empty',
        'status'      => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'code'        => 'required|min_length[2]|max_length[20]',
        'name'        => 'required|min_length[3]|max_length[100]',
        'start_year'  => 'required|numeric|min_length[4]|max_length[4]',
        'end_year'    => 'required|numeric|min_length[4]|max_length[4]',
        'description' => 'permit_empty',
        'status'      => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'code' => [
            'is_unique' => 'This batch code is already in use.',
        ],
        'start_year' => [
            'numeric' => 'Start year must be a valid year.',
            'min_length' => 'Start year must be 4 digits.',
            'max_length' => 'Start year must be 4 digits.',
        ],
        'end_year' => [
            'numeric' => 'End year must be a valid year.',
            'min_length' => 'End year must be 4 digits.',
            'max_length' => 'End year must be 4 digits.',
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
        $rules['code'] = 'required|min_length[2]|max_length[20]|is_unique[batches.code,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }
}

