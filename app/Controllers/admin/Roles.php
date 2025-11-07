<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Roles',
            'page' => 'admin'
        ];
        $content = view('admin/roles/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $roles = $this->roleModel->findAll();
        
        $data = [];
        foreach ($roles as $role) {
            $permissions = !empty($role['permissions']) ? json_decode($role['permissions'], true) : [];
            $permissionsCount = is_array($permissions) ? count($permissions) : 0;
            
            $data[] = [
                'id'              => $role['id'],
                'name'            => $role['name'],
                'slug'            => $role['slug'],
                'description'     => $role['description'] ?? '-',
                'permissions'     => $permissionsCount > 0 ? $permissionsCount . ' permission(s)' : 'No permissions',
                'status'          => $role['status'],
                'created_at'      => date('Y-m-d H:i:s', strtotime($role['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        $name = $this->request->getPost('name');
        $slug = $this->request->getPost('slug');
        
        // Auto-generate slug if not provided
        if (empty($slug) && !empty($name)) {
            $slug = $this->roleModel->generateSlug($name);
        }

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'permissions' => $this->request->getPost('permissions') ? json_encode(explode(',', $this->request->getPost('permissions'))) : null,
            'status'      => $this->request->getPost('status') ?? 'active',
        ];

        // Check if required fields are empty
        if (empty($data['name']) || empty($data['slug'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Name and Slug are required fields.',
            ]);
        }
        
        // Set validation rules for create
        $this->roleModel->setValidationRules();
        
        // Try to insert
        try {
            if (!$this->roleModel->insert($data)) {
                $errors = $this->roleModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            $insertId = $this->roleModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role created successfully',
                'id' => $insertId,
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
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

        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        // Decode permissions if exists
        if (!empty($role['permissions'])) {
            $permissions = json_decode($role['permissions'], true);
            $role['permissions'] = is_array($permissions) ? implode(',', $permissions) : '';
        } else {
            $role['permissions'] = '';
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $role
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Check if role exists
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        $name = $this->request->getPost('name');
        $slug = $this->request->getPost('slug');
        
        // Auto-generate slug if not provided
        if (empty($slug) && !empty($name)) {
            $slug = $this->roleModel->generateSlug($name);
        }

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'permissions' => $this->request->getPost('permissions') ? json_encode(explode(',', $this->request->getPost('permissions'))) : null,
            'status'      => $this->request->getPost('status'),
        ];

        // Set validation rules for update (excluding current ID from uniqueness check)
        $this->roleModel->setUpdateValidationRules($id);

        if (!$this->roleModel->update($id, $data)) {
            $errors = $this->roleModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check the form.',
                'errors'  => $errors
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Role updated successfully',
            'csrf_token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        if (!$this->roleModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete role. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}

