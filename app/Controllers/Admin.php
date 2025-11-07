<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\ProgramModel;
use App\Models\CourseModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\AdminModel;
use App\Models\BatchModel;
use App\Models\CourseOfferingModel;
use App\Models\StudentCourseEnrollmentModel;
use App\Models\FeeModel;

class Admin extends BaseController
{
    protected $departmentModel;
    protected $programModel;
    protected $courseModel;
    protected $studentModel;
    protected $teacherModel;
    protected $adminModel;
    protected $batchModel;
    protected $courseOfferingModel;
    protected $enrollmentModel;
    protected $feeModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->programModel = new ProgramModel();
        $this->courseModel = new CourseModel();
        $this->studentModel = new StudentModel();
        $this->teacherModel = new TeacherModel();
        $this->adminModel = new AdminModel();
        $this->batchModel = new BatchModel();
        $this->courseOfferingModel = new CourseOfferingModel();
        $this->enrollmentModel = new StudentCourseEnrollmentModel();
        $this->feeModel = new FeeModel();
    }

    public function index(): string
    {
        return $this->dashboard();
    }

    public function dashboard(): string
    {
        // Get statistics (excluding soft deleted records)
        $stats = [
            'departments' => $this->departmentModel->countAllResults(),
            'departments_active' => $this->departmentModel->where('status', 'active')->countAllResults(),
            'programs' => $this->programModel->countAllResults(),
            'programs_active' => $this->programModel->where('status', 'active')->countAllResults(),
            'courses' => $this->courseModel->countAllResults(),
            'courses_active' => $this->courseModel->where('status', 'active')->countAllResults(),
            'students' => $this->studentModel->countAllResults(),
            'students_active' => $this->studentModel->where('status', 'active')->countAllResults(),
            'teachers' => $this->teacherModel->countAllResults(),
            'teachers_active' => $this->teacherModel->where('status', 'active')->countAllResults(),
            'users' => $this->adminModel->countAllResults(),
            'users_active' => $this->adminModel->where('status', 'active')->countAllResults(),
            'batches' => $this->batchModel->countAllResults(),
            'batches_active' => $this->batchModel->where('status', 'active')->countAllResults(),
            'course_offerings' => $this->courseOfferingModel->countAllResults(),
            'course_offerings_active' => $this->courseOfferingModel->where('status', 'active')->countAllResults(),
            'enrollments' => $this->enrollmentModel->where('status !=', 'dropped')->countAllResults(),
            'pending_fees' => $this->feeModel->where('status', 'pending')->countAllResults(),
        ];

        // Get fee totals (excluding cancelled fees)
        $feeTotal = $this->feeModel->where('status !=', 'cancelled')->selectSum('amount')->first();
        $paidTotal = $this->feeModel->where('status !=', 'cancelled')->selectSum('paid_amount')->first();
        
        $stats['total_fees_amount'] = $feeTotal['amount'] ?? 0;
        $stats['total_paid_amount'] = $paidTotal['paid_amount'] ?? 0;

        // Get recent activities
        $recentStudents = $this->studentModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        $recentTeachers = $this->teacherModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        $pendingPayments = $this->feeModel->getPendingFees();
        $pendingPayments = array_slice($pendingPayments, 0, 5);

        $data = [
            'title' => 'Admin Dashboard',
            'page' => 'admin',
            'stats' => $stats,
            'recentStudents' => $recentStudents,
            'recentTeachers' => $recentTeachers,
            'pendingPayments' => $pendingPayments
        ];
        $content = view('admin/dashboard_content', $data);
        return view('layouts/main', array_merge($data, ['content' => $content]));
    }
}

