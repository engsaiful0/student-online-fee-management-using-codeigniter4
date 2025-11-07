<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'slug', 'description', 'permissions', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'name'        => 'required|min_length[2]|max_length[50]|is_unique[roles.name]',
        'slug'        => 'required|min_length[2]|max_length[50]|is_unique[roles.slug]|alpha_dash',
        'description' => 'permit_empty',
        'permissions' => 'permit_empty',
        'status'      => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'name'        => 'required|min_length[2]|max_length[50]',
        'slug'        => 'required|min_length[2]|max_length[50]|alpha_dash',
        'description' => 'permit_empty',
        'permissions' => 'permit_empty',
        'status'      => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'name' => [
            'is_unique' => 'This role name is already in use.',
        ],
        'slug' => [
            'is_unique' => 'This role slug is already in use.',
            'alpha_dash' => 'Slug can only contain letters, numbers, dashes and underscores.',
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
        $rules['name'] = 'required|min_length[2]|max_length[50]|is_unique[roles.name,id,' . $id . ']';
        $rules['slug'] = 'required|min_length[2]|max_length[50]|alpha_dash|is_unique[roles.slug,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }

    /**
     * Generate slug from name
     */
    public function generateSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        return $slug;
    }
}

