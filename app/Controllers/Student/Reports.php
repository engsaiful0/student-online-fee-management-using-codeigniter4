<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\TeacherModel;
use App\Models\FeeModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Reports extends BaseController
{
    protected $departmentModel;
    protected $teacherModel;
    protected $feeModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->teacherModel = new TeacherModel();
        $this->feeModel = new FeeModel();
    }

    public function index()
    {
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to('/auth/student');
        }

        $data = [
            'title' => 'Reports',
            'page' => 'student'
        ];
        $content = view('student/reports/index', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    // Department List Report
    public function departmentList($format = 'view')
    {
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to('/auth/student');
        }

        $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();

        if ($format === 'pdf') {
            return $this->exportDepartmentListPDF($departments);
        } elseif ($format === 'excel') {
            return $this->exportDepartmentListExcel($departments);
        }

        $data = [
            'title' => 'Department List Report',
            'page' => 'student',
            'departments' => $departments,
            'report_type' => 'department_list'
        ];
        $content = view('student/reports/department_list', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    // Teacher List Report
    public function teacherList($format = 'view')
    {
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to('/auth/student');
        }

        $teachers = $this->teacherModel->getTeachersWithDetails();
        $teachers = array_map(function($teacher) {
            return array_merge($teacher, [
                'department_name' => $teacher['department_name'] ?? 'Not Assigned',
                'designation_name' => $teacher['designation_name'] ?? 'Not Assigned',
            ]);
        }, $teachers);

        if ($format === 'pdf') {
            return $this->exportTeacherListPDF($teachers);
        } elseif ($format === 'excel') {
            return $this->exportTeacherListExcel($teachers);
        }

        $data = [
            'title' => 'Teacher List Report',
            'page' => 'student',
            'teachers' => $teachers,
            'report_type' => 'teacher_list'
        ];
        $content = view('student/reports/teacher_list', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    // Student Payment Status Report (for logged-in student only)
    public function studentPaymentStatus($format = 'view')
    {
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to('/auth/student');
        }

        $fees = $this->feeModel->getFeesByStudent($studentId);
        $totalAmount = array_sum(array_column($fees, 'amount'));
        $totalPaid = array_sum(array_column($fees, 'paid_amount'));
        $totalPending = $totalAmount - $totalPaid;
        
        $paymentStatus = [
            'total_fees' => $totalAmount,
            'paid_amount' => $totalPaid,
            'pending_amount' => $totalPending,
            'status' => $totalPending > 0 ? ($totalPaid > 0 ? 'Partial' : 'Unpaid') : 'Paid',
            'fees_count' => count($fees),
            'fees' => $fees
        ];

        if ($format === 'pdf') {
            return $this->exportStudentPaymentStatusPDF($paymentStatus);
        } elseif ($format === 'excel') {
            return $this->exportStudentPaymentStatusExcel($paymentStatus);
        }

        $data = [
            'title' => 'My Payment Status Report',
            'page' => 'student',
            'paymentStatus' => $paymentStatus,
            'report_type' => 'student_payment_status'
        ];
        $content = view('student/reports/student_payment_status', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    // Student Payment Report (Detailed - for logged-in student only)
    public function studentPaymentReport($format = 'view')
    {
        $studentId = session()->get('student_id');
        if (!$studentId) {
            session()->setFlashdata('error', 'Please login to access this page');
            return redirect()->to('/auth/student');
        }

        $fees = $this->feeModel->getFeesByStudent($studentId);

        if ($format === 'pdf') {
            return $this->exportStudentPaymentReportPDF($fees);
        } elseif ($format === 'excel') {
            return $this->exportStudentPaymentReportExcel($fees);
        }

        $data = [
            'title' => 'My Payment Report',
            'page' => 'student',
            'fees' => $fees,
            'report_type' => 'student_payment_report'
        ];
        $content = view('student/reports/student_payment_report', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }

    // PDF Export Methods (same as admin but with student-specific data)
    private function exportDepartmentListPDF($departments)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generateDepartmentListHTML($departments);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'department_list_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    private function exportTeacherListPDF($teachers)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generateTeacherListHTML($teachers);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'teacher_list_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    private function exportStudentPaymentStatusPDF($paymentStatus)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generateStudentPaymentStatusHTML($paymentStatus);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'my_payment_status_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    private function exportStudentPaymentReportPDF($fees)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generateStudentPaymentReportHTML($fees);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'my_payment_report_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    // Excel Export Methods
    private function exportDepartmentListExcel($departments)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Code', 'Name', 'Description', 'Status', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $row = 2;
        foreach ($departments as $dept) {
            $sheet->setCellValue('A' . $row, $dept['code']);
            $sheet->setCellValue('B' . $row, $dept['name']);
            $sheet->setCellValue('C' . $row, $dept['description'] ?? '-');
            $sheet->setCellValue('D' . $row, ucfirst($dept['status']));
            $sheet->setCellValue('E' . $row, $dept['created_at'] ? date('Y-m-d', strtotime($dept['created_at'])) : '-');
            $row++;
        }
        
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->setTitle('Department List');
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'department_list_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function exportTeacherListExcel($teachers)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Employee ID', 'Name', 'Email', 'Phone', 'Department', 'Designation', 'Qualification', 'Experience (Years)', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $row = 2;
        foreach ($teachers as $teacher) {
            $sheet->setCellValue('A' . $row, $teacher['employee_id'] ?? '-');
            $sheet->setCellValue('B' . $row, $teacher['name']);
            $sheet->setCellValue('C' . $row, $teacher['email']);
            $sheet->setCellValue('D' . $row, $teacher['phone'] ?? '-');
            $sheet->setCellValue('E' . $row, $teacher['department_name']);
            $sheet->setCellValue('F' . $row, $teacher['designation_name']);
            $sheet->setCellValue('G' . $row, $teacher['qualification'] ?? '-');
            $sheet->setCellValue('H' . $row, $teacher['experience_years'] ?? '0');
            $sheet->setCellValue('I' . $row, ucfirst($teacher['status']));
            $row++;
        }
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->setTitle('Teacher List');
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'teacher_list_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function exportStudentPaymentStatusExcel($paymentStatus)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Summary section
        $sheet->setCellValue('A1', 'Payment Status Summary');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
        
        $sheet->setCellValue('A2', 'Total Fees:');
        $sheet->setCellValue('B2', number_format($paymentStatus['total_fees'], 2));
        $sheet->setCellValue('A3', 'Paid Amount:');
        $sheet->setCellValue('B3', number_format($paymentStatus['paid_amount'], 2));
        $sheet->setCellValue('A4', 'Pending Amount:');
        $sheet->setCellValue('B4', number_format($paymentStatus['pending_amount'], 2));
        $sheet->setCellValue('A5', 'Status:');
        $sheet->setCellValue('B5', $paymentStatus['status']);
        
        // Headers
        $headers = ['Fee Type', 'Fee Title', 'Course', 'Amount', 'Paid Amount', 'Remaining', 'Due Date', 'Status'];
        $col = 'A';
        $startRow = 7;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $col++;
        }
        
        $sheet->getStyle('A' . $startRow . ':H' . $startRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $row = $startRow + 1;
        foreach ($paymentStatus['fees'] as $fee) {
            $remaining = $fee['amount'] - $fee['paid_amount'];
            $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $fee['fee_type'])));
            $sheet->setCellValue('B' . $row, $fee['fee_title']);
            $sheet->setCellValue('C' . $row, $fee['course_name'] ?? '-');
            $sheet->setCellValue('D' . $row, number_format($fee['amount'], 2));
            $sheet->setCellValue('E' . $row, number_format($fee['paid_amount'], 2));
            $sheet->setCellValue('F' . $row, number_format($remaining, 2));
            $sheet->setCellValue('G' . $row, $fee['due_date'] ? date('Y-m-d', strtotime($fee['due_date'])) : '-');
            $sheet->setCellValue('H' . $row, ucfirst($fee['status']));
            $row++;
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->setTitle('My Payment Status');
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'my_payment_status_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function exportStudentPaymentReportExcel($fees)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Fee Type', 'Fee Title', 'Course', 'Amount', 'Paid Amount', 'Remaining', 'Due Date', 'Payment Date', 'Status', 'Receipt Number'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $row = 2;
        foreach ($fees as $fee) {
            $remaining = $fee['amount'] - $fee['paid_amount'];
            $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $fee['fee_type'])));
            $sheet->setCellValue('B' . $row, $fee['fee_title']);
            $sheet->setCellValue('C' . $row, $fee['course_name'] ?? '-');
            $sheet->setCellValue('D' . $row, number_format($fee['amount'], 2));
            $sheet->setCellValue('E' . $row, number_format($fee['paid_amount'], 2));
            $sheet->setCellValue('F' . $row, number_format($remaining, 2));
            $sheet->setCellValue('G' . $row, $fee['due_date'] ? date('Y-m-d', strtotime($fee['due_date'])) : '-');
            $sheet->setCellValue('H' . $row, $fee['payment_date'] ? date('Y-m-d H:i', strtotime($fee['payment_date'])) : '-');
            $sheet->setCellValue('I' . $row, ucfirst($fee['status']));
            $sheet->setCellValue('J' . $row, $fee['receipt_number'] ?? '-');
            $row++;
        }
        
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->setTitle('My Payment Report');
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'my_payment_report_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    // HTML Generation Methods for PDF
    private function generateDepartmentListHTML($departments)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Department List</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #4472C4; color: white; padding: 10px; text-align: left; }
            td { border: 1px solid #ddd; padding: 8px; }
            tr:nth-child(even) { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>Department List Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<table><tr><th>Code</th><th>Name</th><th>Description</th><th>Status</th></tr>';
        
        foreach ($departments as $dept) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($dept['code'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($dept['name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($dept['description'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . ucfirst($dept['status']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        return $html;
    }

    private function generateTeacherListHTML($teachers)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Teacher List</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
            th { background-color: #4472C4; color: white; padding: 8px; text-align: left; }
            td { border: 1px solid #ddd; padding: 6px; }
            tr:nth-child(even) { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>Teacher List Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<table><tr><th>Employee ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Department</th><th>Designation</th><th>Qualification</th><th>Experience</th><th>Status</th></tr>';
        
        foreach ($teachers as $teacher) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($teacher['employee_id'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($teacher['name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($teacher['email'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($teacher['phone'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($teacher['department_name'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($teacher['designation_name'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($teacher['qualification'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . ($teacher['experience_years'] ?? '0') . ' years</td>';
            $html .= '<td>' . ucfirst($teacher['status']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        return $html;
    }

    private function generateStudentPaymentStatusHTML($paymentStatus)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>My Payment Status</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; color: #333; }
            .summary { margin: 20px 0; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; }
            .summary-item { margin: 10px 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #4472C4; color: white; padding: 10px; text-align: left; }
            td { border: 1px solid #ddd; padding: 8px; }
            tr:nth-child(even) { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>My Payment Status Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<div class="summary">';
        $html .= '<div class="summary-item"><strong>Total Fees:</strong> ' . number_format($paymentStatus['total_fees'], 2) . '</div>';
        $html .= '<div class="summary-item"><strong>Paid Amount:</strong> ' . number_format($paymentStatus['paid_amount'], 2) . '</div>';
        $html .= '<div class="summary-item"><strong>Pending Amount:</strong> ' . number_format($paymentStatus['pending_amount'], 2) . '</div>';
        $html .= '<div class="summary-item"><strong>Status:</strong> ' . htmlspecialchars($paymentStatus['status'] ?? '', ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '</div>';
        $html .= '<table><tr><th>Fee Type</th><th>Fee Title</th><th>Course</th><th>Amount</th><th>Paid</th><th>Remaining</th><th>Due Date</th><th>Status</th></tr>';
        
        foreach ($paymentStatus['fees'] as $fee) {
            $remaining = $fee['amount'] - $fee['paid_amount'];
            $html .= '<tr>';
            $html .= '<td>' . ucfirst(str_replace('_', ' ', $fee['fee_type'] ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars($fee['fee_title'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($fee['course_name'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . number_format($fee['amount'] ?? 0, 2) . '</td>';
            $html .= '<td>' . number_format($fee['paid_amount'] ?? 0, 2) . '</td>';
            $html .= '<td>' . number_format($remaining, 2) . '</td>';
            $html .= '<td>' . ($fee['due_date'] ? date('Y-m-d', strtotime($fee['due_date'])) : '-') . '</td>';
            $html .= '<td>' . ucfirst($fee['status'] ?? '') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        return $html;
    }

    private function generateStudentPaymentReportHTML($fees)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>My Payment Report</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 9px; }
            th { background-color: #4472C4; color: white; padding: 8px; text-align: left; }
            td { border: 1px solid #ddd; padding: 6px; }
            tr:nth-child(even) { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>My Payment Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<table><tr><th>Fee Type</th><th>Fee Title</th><th>Course</th><th>Amount</th><th>Paid</th><th>Remaining</th><th>Due Date</th><th>Payment Date</th><th>Status</th><th>Receipt</th></tr>';
        
        foreach ($fees as $fee) {
            $remaining = $fee['amount'] - $fee['paid_amount'];
            $html .= '<tr>';
            $html .= '<td>' . ucfirst(str_replace('_', ' ', $fee['fee_type'] ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars($fee['fee_title'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($fee['course_name'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . number_format($fee['amount'] ?? 0, 2) . '</td>';
            $html .= '<td>' . number_format($fee['paid_amount'] ?? 0, 2) . '</td>';
            $html .= '<td>' . number_format($remaining, 2) . '</td>';
            $html .= '<td>' . ($fee['due_date'] ? date('Y-m-d', strtotime($fee['due_date'])) : '-') . '</td>';
            $html .= '<td>' . ($fee['payment_date'] ? date('Y-m-d H:i', strtotime($fee['payment_date'])) : '-') . '</td>';
            $html .= '<td>' . ucfirst($fee['status'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($fee['receipt_number'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        return $html;
    }
}

