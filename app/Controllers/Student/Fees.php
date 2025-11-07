<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\FeeModel;
use App\Models\StudentCourseEnrollmentModel;

class Fees extends BaseController
{
    protected $feeModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->feeModel = new FeeModel();
        $this->enrollmentModel = new StudentCourseEnrollmentModel();
    }

    public function index()
    {
        // Check if student is logged in
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to('/auth/student');
        }

        // Get student fees
        $fees = $this->feeModel->getFeesByStudent($studentId);
        
        // Get enrolled courses for fee selection
        $enrolledCourses = $this->enrollmentModel->getEnrollmentsByStudent($studentId);

        $data = [
            'title' => 'Fees Payment',
            'page' => 'student',
            'fees' => $fees,
            'enrolledCourses' => $enrolledCourses
        ];
        $content = view('student/fees/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    public function makePayment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        // Check if student is logged in
        $studentId = session()->get('student_id');
        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to make a payment'
            ]);
        }

        $feeId = $this->request->getPost('fee_id');
        $paymentAmount = $this->request->getPost('payment_amount');
        $paymentMethod = $this->request->getPost('payment_method');
        $transactionId = $this->request->getPost('transaction_id');
        $remarks = $this->request->getPost('remarks');

        // Validate fee
        $fee = $this->feeModel->find($feeId);
        if (!$fee || $fee['student_id'] != $studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fee not found or unauthorized'
            ]);
        }

        // Validate payment amount
        if (empty($paymentAmount) || $paymentAmount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payment amount'
            ]);
        }

        $remainingAmount = $fee['amount'] - $fee['paid_amount'];
        if ($paymentAmount > $remainingAmount) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment amount cannot exceed remaining amount: ' . number_format($remainingAmount, 2)
            ]);
        }

        // Calculate new paid amount and status
        $newPaidAmount = $fee['paid_amount'] + $paymentAmount;
        $newStatus = 'pending'; // Always pending until authorized by admin
        
        if ($newPaidAmount >= $fee['amount']) {
            $newStatus = 'pending'; // Still pending until admin authorizes
        } elseif ($newPaidAmount > 0) {
            $newStatus = 'pending'; // Still pending
        }

        $data = [
            'paid_amount'    => $newPaidAmount,
            'payment_date'   => date('Y-m-d H:i:s'),
            'payment_method' => $paymentMethod ?? 'other',
            'transaction_id' => $transactionId ?? null,
            'status'         => $newStatus,
            'remarks'        => $remarks ?? null,
        ];

        try {
            if (!$this->feeModel->update($feeId, $data)) {
                $errors = $this->feeModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to process payment: ' . implode(', ', $errors)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment submitted successfully. Waiting for admin authorization.',
                'csrf_token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function createFeeRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request - must be AJAX']);
        }

        // Check if student is logged in
        $studentId = session()->get('student_id');
        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to make a payment'
            ]);
        }

        $feeType = $this->request->getPost('fee_type');
        $courseOfferingId = $this->request->getPost('course_offering_id');
        $feeTitle = $this->request->getPost('fee_title');
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount');
        $dueDate = $this->request->getPost('due_date');

        $data = [
            'student_id'        => $studentId,
            'fee_type'          => $feeType ?? 'course_fee',
            'course_offering_id' => !empty($courseOfferingId) ? (int)$courseOfferingId : null,
            'fee_title'         => trim($feeTitle),
            'description'       => $description ? trim($description) : null,
            'amount'            => (float)$amount,
            'paid_amount'       => 0,
            'due_date'          => $dueDate ? date('Y-m-d', strtotime($dueDate)) : null,
            'status'            => 'pending',
        ];

        $this->feeModel->setCreateValidationRules();
        
        try {
            if (!$this->feeModel->insert($data)) {
                $errors = $this->feeModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors'  => $errors,
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fee request created successfully',
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

