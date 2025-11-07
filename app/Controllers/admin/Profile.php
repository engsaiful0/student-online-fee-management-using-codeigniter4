<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Profile extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function updatePassword()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        // Get admin ID from session
        $adminId = session()->get('admin_id');
        
        if (!$adminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin session not found. Please login again.'
            ]);
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All password fields are required.'
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'New password and confirm password do not match.'
            ]);
        }

        if (strlen($newPassword) < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'New password must be at least 6 characters long.'
            ]);
        }

        // Get admin data
        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin not found.'
            ]);
        }

        // Verify current password
        if (!$this->adminModel->verifyPassword($currentPassword, $admin['password'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
        }

        // Update password
        try {
            // The model's beforeUpdate callback will hash the password automatically
            if (!$this->adminModel->update($adminId, ['password' => $newPassword])) {
                $errors = $this->adminModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update password. ' . implode(', ', $errors)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password updated successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function getAdminInfo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $adminId = session()->get('admin_id');
        
        if (!$adminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin session not found.'
            ]);
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin not found.'
            ]);
        }

        // Remove password from response
        unset($admin['password']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $admin
        ]);
    }
}

