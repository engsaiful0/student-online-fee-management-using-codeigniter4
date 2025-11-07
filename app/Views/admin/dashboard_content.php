<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Welcome, <?= esc(session()->get('admin_name') ?? 'Admin') ?>!</h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-envelope"></i> <?= esc(session()->get('admin_email') ?? 'admin@example.com') ?>
                        <span class="mx-2">|</span>
                        <i class="bi bi-calendar"></i> <?= date('F d, Y') ?>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6">Administrator</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Students -->
    <div class="col-md-3 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon primary">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value"><?= $stats['students'] ?? 0 ?></div>
            <div class="stat-label">Total Students</div>
            <div class="stat-sublabel"><?= $stats['students_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Teachers -->
    <div class="col-md-3 mb-4">
        <div class="stat-card success">
            <div class="stat-icon success">
                <i class="bi bi-person-badge"></i>
            </div>
            <div class="stat-value"><?= $stats['teachers'] ?? 0 ?></div>
            <div class="stat-label">Total Teachers</div>
            <div class="stat-sublabel"><?= $stats['teachers_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Courses -->
    <div class="col-md-3 mb-4">
        <div class="stat-card info">
            <div class="stat-icon info">
                <i class="bi bi-book"></i>
            </div>
            <div class="stat-value"><?= $stats['courses'] ?? 0 ?></div>
            <div class="stat-label">Total Courses</div>
            <div class="stat-sublabel"><?= $stats['courses_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Departments -->
    <div class="col-md-3 mb-4">
        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="bi bi-building"></i>
            </div>
            <div class="stat-value"><?= $stats['departments'] ?? 0 ?></div>
            <div class="stat-label">Departments</div>
            <div class="stat-sublabel"><?= $stats['departments_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Programs -->
    <div class="col-md-3 mb-4">
        <div class="stat-card secondary">
            <div class="stat-icon" style="background: rgba(108, 117, 125, 0.1); color: #6c757d;">
                <i class="bi bi-journal-text"></i>
            </div>
            <div class="stat-value"><?= $stats['programs'] ?? 0 ?></div>
            <div class="stat-label">Programs</div>
            <div class="stat-sublabel"><?= $stats['programs_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Batches -->
    <div class="col-md-3 mb-4">
        <div class="stat-card danger">
            <div class="stat-icon danger">
                <i class="bi bi-collection"></i>
            </div>
            <div class="stat-value"><?= $stats['batches'] ?? 0 ?></div>
            <div class="stat-label">Batches</div>
            <div class="stat-sublabel"><?= $stats['batches_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Course Offerings -->
    <div class="col-md-3 mb-4">
        <div class="stat-card" style="border-left-color: #20c997;">
            <div class="stat-icon" style="background: rgba(32, 201, 151, 0.1); color: #20c997;">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <div class="stat-value"><?= $stats['course_offerings'] ?? 0 ?></div>
            <div class="stat-label">Course Offerings</div>
            <div class="stat-sublabel"><?= $stats['course_offerings_active'] ?? 0 ?> Active</div>
        </div>
    </div>
    
    <!-- Enrollments -->
    <div class="col-md-3 mb-4">
        <div class="stat-card" style="border-left-color: #fd7e14;">
            <div class="stat-icon" style="background: rgba(253, 126, 20, 0.1); color: #fd7e14;">
                <i class="bi bi-book-check"></i>
            </div>
            <div class="stat-value"><?= $stats['enrollments'] ?? 0 ?></div>
            <div class="stat-label">Enrollments</div>
            <div class="stat-sublabel">Active Enrollments</div>
        </div>
    </div>
</div>

<!-- Financial Overview -->
<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h5 class="text-success mb-3"><i class="bi bi-cash-stack"></i> Total Fees</h5>
                <h3 class="mb-0"><?= number_format($stats['total_fees_amount'] ?? 0, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h5 class="text-primary mb-3"><i class="bi bi-check-circle"></i> Paid Amount</h5>
                <h3 class="mb-0"><?= number_format($stats['total_paid_amount'] ?? 0, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h5 class="text-warning mb-3"><i class="bi bi-clock-history"></i> Pending Payments</h5>
                <h3 class="mb-0"><?= $stats['pending_fees'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities and Quick Actions -->
<div class="row">
    <!-- Recent Students -->
    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-people"></i> Recent Students</h5>
                <a href="<?= base_url('admin/students') ?>" class="btn btn-sm btn-primary">View All</a>
            </div>
            <?php if (empty($recentStudents)): ?>
                <p class="text-muted text-center py-3">No students found</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudents as $student): ?>
                                <tr>
                                    <td><?= esc($student['student_id'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['name'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['email'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($student['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($student['status'] ?? 'active') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Teachers -->
    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Recent Teachers</h5>
                <a href="<?= base_url('admin/teachers') ?>" class="btn btn-sm btn-primary">View All</a>
            </div>
            <?php if (empty($recentTeachers)): ?>
                <p class="text-muted text-center py-3">No teachers found</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTeachers as $teacher): ?>
                                <tr>
                                    <td><?= esc($teacher['employee_id'] ?? 'N/A') ?></td>
                                    <td><?= esc($teacher['name'] ?? 'N/A') ?></td>
                                    <td><?= esc($teacher['email'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($teacher['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($teacher['status'] ?? 'active') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Pending Payments -->
<div class="row">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pending Payments</h5>
                <a href="<?= base_url('admin/fees') ?>" class="btn btn-sm btn-warning">View All</a>
            </div>
            <?php if (empty($pendingPayments)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No pending payments at the moment.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Fee Title</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Remaining</th>
                                <th>Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingPayments as $fee): 
                                $remaining = ($fee['amount'] ?? 0) - ($fee['paid_amount'] ?? 0);
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($fee['student_name'] ?? 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= esc($fee['student_student_id'] ?? 'N/A') ?></small>
                                    </td>
                                    <td><?= esc($fee['fee_title'] ?? 'N/A') ?></td>
                                    <td><?= number_format($fee['amount'] ?? 0, 2) ?></td>
                                    <td class="text-success"><?= number_format($fee['paid_amount'] ?? 0, 2) ?></td>
                                    <td class="text-danger"><?= number_format($remaining, 2) ?></td>
                                    <td><?= $fee['payment_date'] ? date('M d, Y H:i', strtotime($fee['payment_date'])) : '-' ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/fees') ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="content-card">
            <h5 class="mb-3"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-primary w-100">
                        <i class="bi bi-person-plus"></i><br>
                        Add Student
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-success w-100">
                        <i class="bi bi-person-badge"></i><br>
                        Add Teacher
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?= base_url('admin/course-offerings') ?>" class="btn btn-outline-info w-100">
                        <i class="bi bi-journal-plus"></i><br>
                        Create Course Offering
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?= base_url('admin/fees') ?>" class="btn btn-outline-warning w-100">
                        <i class="bi bi-cash-coin"></i><br>
                        View Fees
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-sublabel {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 5px;
}
</style>

