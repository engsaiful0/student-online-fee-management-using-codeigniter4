<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admins';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'password', 'phone', 'role_id', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[admins.email]',
        'password' => 'required|min_length[6]',
        'phone'    => 'permit_empty|max_length[20]',
        'role_id'  => 'permit_empty|integer',
        'status'   => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email',
        'password' => 'permit_empty|min_length[6]',
        'phone'    => 'permit_empty|max_length[20]',
        'role_id'  => 'permit_empty|integer',
        'status'   => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'This email is already registered.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Authenticate admin
     */
    public function authenticate(string $email, string $password)
    {
        $admin = $this->findByEmail($email);
        
        if (!$admin) {
            return false;
        }

        if (!$this->verifyPassword($password, $admin['password'])) {
            return false;
        }

        if ($admin['status'] !== 'active') {
            return false;
        }

        // Remove password from return
        unset($admin['password']);
        return $admin;
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
        $rules['email'] = 'required|valid_email|is_unique[admins.email,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }
}

