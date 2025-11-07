<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\RoleModel;

class Users extends BaseController
{
    protected $adminModel;
    protected $roleModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Users Management',
            'page' => 'admin'
        ];
        $content = view('admin/users/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        // Allow both AJAX and regular GET requests for DataTables
        try {
            // Get all admins (excluding soft deleted)
            $users = $this->adminModel->orderBy('id', 'DESC')->findAll();
            
            log_message('debug', 'Users getData - Found ' . count($users) . ' users');
            
            $data = [];
            foreach ($users as $user) {
                // Get role information if role_id exists
                $roleName = null;
                $roleSlug = null;
                if (!empty($user['role_id'])) {
                    $role = $this->roleModel->find($user['role_id']);
                    if ($role) {
                        $roleName = $role['name'];
                        $roleSlug = $role['slug'];
                    }
                }
                
                $data[] = [
                    'id'            => (int)$user['id'],
                    'name'          => $user['name'] ?? '',
                    'email'         => $user['email'] ?? '',
                    'phone'         => $user['phone'] ?? '-',
                    'role'          => ($roleName && $roleSlug) ? $roleName . ' (' . $roleSlug . ')' : 'No Role Assigned',
                    'role_id'       => !empty($user['role_id']) ? (int)$user['role_id'] : null,
                    'status'        => $user['status'] ?? 'active',
                    'created_at'    => $user['created_at'] ?? date('Y-m-d H:i:s'),
                ];
            }

            log_message('debug', 'Users getData - Returning ' . count($data) . ' records');
            
            return $this->response->setJSON([
                'data' => $data
            ])->setContentType('application/json');
        } catch (\Exception $e) {
            log_message('error', 'Users getData error: ' . $e->getMessage());
            log_message('error', 'Users getData stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ])->setContentType('application/json');
        }
    }

    public function getRoles()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $roles = $this->roleModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        return $this->response->setJSON(['data' => $roles]);
    }

    public function getRolePermissions($roleId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found',
                'data' => []
            ]);
        }

        $permissions = !empty($role['permissions']) ? json_decode($role['permissions'], true) : [];
        if (!is_array($permissions)) {
            $permissions = [];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $permissions,
            'role_name' => $role['name']
        ]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $roleId = $this->request->getPost('role_id');
        $roleId = (!empty($roleId) && $roleId !== '') ? (int)$roleId : null;
        
        // Validate role_id if provided
        if ($roleId !== null && !$this->roleModel->find($roleId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid role selected.',
                'errors'  => ['role_id' => 'The selected role does not exist.'],
            ]);
        }

        $password = $this->request->getPost('password');
        if (empty($password)) {
            $password = 'admin123'; // Default password
        }

        $data = [
            'name'     => trim($this->request->getPost('name')),
            'email'    => trim($this->request->getPost('email')),
            'password' => $password,
            'phone'    => $this->request->getPost('phone') ? trim($this->request->getPost('phone')) : null,
            'role_id'  => $roleId,
            'status'   => $this->request->getPost('status') ?? 'active',
        ];

        // Set validation rules
        $this->adminModel->setValidationRules();
        
        try {
            if (!$this->adminModel->insert($data)) {
                $errors = $this->adminModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->adminModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User created successfully',
                'id' => $insertId,
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Users create error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $user = $this->adminModel->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Remove password from response
        unset($user['password']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $user = $this->adminModel->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        $roleId = $this->request->getPost('role_id');
        $roleId = (!empty($roleId) && $roleId !== '') ? (int)$roleId : null;
        
        // Validate role_id if provided
        if ($roleId !== null && !$this->roleModel->find($roleId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid role selected.',
                'errors'  => ['role_id' => 'The selected role does not exist.'],
            ]);
        }

        $data = [
            'name'     => trim($this->request->getPost('name')),
            'email'    => trim($this->request->getPost('email')),
            'phone'    => $this->request->getPost('phone') ? trim($this->request->getPost('phone')) : null,
            'role_id'  => $roleId,
            'status'   => $this->request->getPost('status'),
        ];

        // Update password only if provided
        $password = $this->request->getPost('password');
        if (!empty($password) && trim($password) !== '') {
            $data['password'] = trim($password);
        }

        // Set validation rules for update
        $this->adminModel->setUpdateValidationRules($id);
        
        try {
            if (!$this->adminModel->update($id, $data)) {
                $errors = $this->adminModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User updated successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Users update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $user = $this->adminModel->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        try {
            if (!$this->adminModel->delete($id)) {
                $errors = $this->adminModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete user. ' . (!empty($errors) ? implode(', ', $errors) : 'Please try again.')
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Users delete error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

