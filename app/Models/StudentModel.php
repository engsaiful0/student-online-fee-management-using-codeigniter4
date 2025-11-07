<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'password', 'student_id', 'phone', 'address', 'batch_id', 'session', 'department_id', 'program_id', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationRulesCreate = [
        'name'          => 'required|min_length[3]|max_length[100]',
        'email'         => 'required|valid_email|is_unique[students.email]',
        'password'      => 'required|min_length[6]',
        'student_id'    => 'permit_empty|max_length[50]|is_unique[students.student_id]',
        'phone'         => 'permit_empty|max_length[20]',
        'address'       => 'permit_empty',
        'batch_id'      => 'permit_empty|integer|is_not_unique[batches.id]',
        'session'       => 'permit_empty|max_length[50]',
        'department_id' => 'permit_empty|integer|is_not_unique[departments.id]',
        'program_id'    => 'permit_empty|integer|is_not_unique[programs.id]',
        'status'        => 'required|in_list[active,inactive]',
    ];
    protected $validationRulesUpdate = [
        'name'          => 'required|min_length[3]|max_length[100]',
        'email'         => 'required|valid_email',
        'password'      => 'permit_empty|min_length[6]',
        'student_id'    => 'permit_empty|max_length[50]',
        'phone'         => 'permit_empty|max_length[20]',
        'address'       => 'permit_empty',
        'batch_id'      => 'permit_empty|integer|is_not_unique[batches.id]',
        'session'       => 'permit_empty|max_length[50]',
        'department_id' => 'permit_empty|integer|is_not_unique[departments.id]',
        'program_id'    => 'permit_empty|integer|is_not_unique[programs.id]',
        'status'        => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'This email is already registered.',
        ],
        'student_id' => [
            'is_unique' => 'This student ID is already registered.',
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
     * Find student by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find student by student ID
     */
    public function findByStudentId(string $studentId)
    {
        return $this->where('student_id', $studentId)->first();
    }

    /**
     * Authenticate student
     */
    public function authenticate(string $email, string $password)
    {
        $student = $this->findByEmail($email);
        
        if (!$student) {
            return false;
        }

        if (!$this->verifyPassword($password, $student['password'])) {
            return false;
        }

        if ($student['status'] !== 'active') {
            return false;
        }

        // Remove password from return
        unset($student['password']);
        return $student;
    }

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
        $rules['email'] = 'required|valid_email|is_unique[students.email,id,' . $id . ']';
        $rules['student_id'] = 'permit_empty|max_length[50]|is_unique[students.student_id,id,' . $id . ']';
        $this->validationRules = $rules;
        return $this;
    }
}

