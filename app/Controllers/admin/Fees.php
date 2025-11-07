<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FeeModel;

class Fees extends BaseController
{
    protected $feeModel;

    public function __construct()
    {
        $this->feeModel = new FeeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Fees Received',
            'page' => 'admin'
        ];
        $content = view('admin/fees/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        try {
            $fees = $this->feeModel->getAllFees();
            
            $data = [];
            foreach ($fees as $fee) {
                $remainingAmount = $fee['amount'] - $fee['paid_amount'];
                
                $data[] = [
                    'id'                => $fee['id'],
                    'student_name'      => $fee['student_name'] ?? 'N/A',
                    'student_id'        => $fee['student_student_id'] ?? 'N/A',
                    'fee_type'          => ucfirst(str_replace('_', ' ', $fee['fee_type'])),
                    'fee_title'         => $fee['fee_title'],
                    'course'            => $fee['course_name'] ? $fee['course_name'] . ' (' . ($fee['course_code'] ?? '') . ')' : '-',
                    'amount'            => number_format($fee['amount'], 2),
                    'paid_amount'       => number_format($fee['paid_amount'], 2),
                    'remaining_amount'  => number_format($remainingAmount, 2),
                    'due_date'          => $fee['due_date'] ? date('M d, Y', strtotime($fee['due_date'])) : '-',
                    'payment_date'      => $fee['payment_date'] ? date('M d, Y H:i', strtotime($fee['payment_date'])) : '-',
                    'payment_method'    => ucfirst($fee['payment_method'] ?? '-'),
                    'transaction_id'    => $fee['transaction_id'] ?? '-',
                    'receipt_number'    => $fee['receipt_number'] ?? '-',
                    'status'            => $fee['status'],
                    'authorized_by'     => $fee['authorized_by_name'] ?? '-',
                    'authorized_at'     => $fee['authorized_at'] ? date('M d, Y H:i', strtotime($fee['authorized_at'])) : '-',
                    'created_at'        => $fee['created_at'] ? date('M d, Y', strtotime($fee['created_at'])) : '-',
                ];
            }

            return $this->response->setJSON([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Fees getData error: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    public function getPendingFees()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        try {
            $fees = $this->feeModel->getPendingFees();
            
            $data = [];
            foreach ($fees as $fee) {
                $remainingAmount = $fee['amount'] - $fee['paid_amount'];
                
                $data[] = [
                    'id'                => $fee['id'],
                    'student_name'      => $fee['student_name'] ?? 'N/A',
                    'student_id'        => $fee['student_student_id'] ?? 'N/A',
                    'fee_type'          => ucfirst(str_replace('_', ' ', $fee['fee_type'])),
                    'fee_title'         => $fee['fee_title'],
                    'course'            => $fee['course_name'] ? $fee['course_name'] . ' (' . ($fee['course_code'] ?? '') . ')' : '-',
                    'amount'            => number_format($fee['amount'], 2),
                    'paid_amount'       => number_format($fee['paid_amount'], 2),
                    'remaining_amount'  => number_format($remainingAmount, 2),
                    'due_date'          => $fee['due_date'] ? date('M d, Y', strtotime($fee['due_date'])) : '-',
                    'payment_date'      => $fee['payment_date'] ? date('M d, Y H:i', strtotime($fee['payment_date'])) : '-',
                    'payment_method'    => ucfirst($fee['payment_method'] ?? '-'),
                    'transaction_id'    => $fee['transaction_id'] ?? '-',
                    'remarks'           => $fee['remarks'] ?? '-',
                ];
            }

            return $this->response->setJSON([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Fees getPendingFees error: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    public function authorizePayment($feeId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $fee = $this->feeModel->find($feeId);
        if (!$fee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fee not found'
            ]);
        }

        if ($fee['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only pending payments can be authorized'
            ]);
        }

        // Determine final status based on payment amount
        $remainingAmount = $fee['amount'] - $fee['paid_amount'];
        $newStatus = 'paid';
        if ($remainingAmount > 0) {
            $newStatus = 'partial';
        }

        // Generate receipt number
        $receiptNumber = 'RCP-' . date('Ymd') . '-' . str_pad($feeId, 6, '0', STR_PAD_LEFT);

        $data = [
            'status'         => $newStatus,
            'authorized_by'  => $adminId,
            'authorized_at'  => date('Y-m-d H:i:s'),
            'receipt_number' => $receiptNumber,
        ];

        try {
            if (!$this->feeModel->update($feeId, $data)) {
                $errors = $this->feeModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to authorize payment: ' . implode(', ', $errors)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment authorized successfully',
                'receipt_number' => $receiptNumber,
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function rejectPayment($feeId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $fee = $this->feeModel->find($feeId);
        if (!$fee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fee not found'
            ]);
        }

        $remarks = $this->request->getPost('remarks');

        $data = [
            'status'         => 'cancelled',
            'paid_amount'    => 0, // Reset paid amount
            'payment_date'   => null,
            'payment_method' => null,
            'transaction_id' => null,
            'remarks'        => $remarks ?? 'Payment rejected by admin',
        ];

        try {
            if (!$this->feeModel->update($feeId, $data)) {
                $errors = $this->feeModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to reject payment: ' . implode(', ', $errors)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment rejected successfully',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

