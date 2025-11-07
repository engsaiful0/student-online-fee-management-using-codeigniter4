<!-- Student Information Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Welcome, <?= esc($student['name'] ?? 'Student') ?>!</h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-person-badge"></i> Student ID: <?= esc($student['student_id'] ?? 'N/A') ?> | 
                        <i class="bi bi-envelope"></i> <?= esc($student['email'] ?? 'N/A') ?>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?= ($student['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?> fs-6">
                        <?= ucfirst($student['status'] ?? 'active') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon primary">
                <i class="bi bi-book"></i>
            </div>
            <div class="stat-value"><?= count($enrolledCourses ?? []) ?></div>
            <div class="stat-label">Enrolled Courses</div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card success">
            <div class="stat-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value"><?= count(array_filter($enrolledCourses ?? [], function($c) { return ($c['status'] ?? '') === 'completed'; })) ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-value"><?= count(array_filter($enrolledCourses ?? [], function($c) { return ($c['status'] ?? '') === 'enrolled'; })) ?></div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card info">
            <div class="stat-icon info">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="stat-value"><?= array_sum(array_column($enrolledCourses ?? [], 'credit')) ?></div>
            <div class="stat-label">Total Credits</div>
        </div>
    </div>
</div>

<!-- Content Cards -->
<div class="row">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="bi bi-book-check"></i> My Enrolled Courses</h4>
            </div>
            
            <?php if (empty($enrolledCourses)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> You are not enrolled in any courses yet. Please contact the administrator to enroll in courses.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit Hours</th>
                                <th>Batch</th>
                                <th>Session</th>
                                <th>Semester</th>
                                <th>Teacher</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrolledCourses as $course): 
                                // Calculate session from batch_semester dates
                                $session = '-';
                                if (!empty($course['start_date']) && !empty($course['end_date'])) {
                                    $startYear = date('Y', strtotime($course['start_date']));
                                    $endYear = date('Y', strtotime($course['end_date']));
                                    $session = $startYear . '-' . $endYear;
                                } elseif (!empty($course['semester_name'])) {
                                    $session = $course['semester_name'];
                                }
                            ?>
                                <tr>
                                    <td><strong><?= esc($course['course_code'] ?? 'N/A') ?></strong></td>
                                    <td><?= esc($course['course_title'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['credit'] ?? '0') ?></td>
                                    <td>
                                        <?php if (!empty($course['batch_name'])): ?>
                                            <span class="badge bg-info"><?= esc($course['batch_name']) ?> (<?= esc($course['batch_code'] ?? '') ?>)</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($session) ?></td>
                                    <td>
                                        <?php if (!empty($course['semester_name'])): ?>
                                            <span class="badge bg-secondary"><?= esc($course['semester_name']) ?> (<?= esc($course['semester_code'] ?? '') ?>)</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($course['teacher_name'] ?? 'Not Assigned') ?></td>
                                    <td><?= !empty($course['enrollment_date']) ? date('M d, Y', strtotime($course['enrollment_date'])) : (!empty($course['created_at']) ? date('M d, Y', strtotime($course['created_at'])) : '-') ?></td>
                                    <td>
                                        <?php 
                                        $status = $course['status'] ?? 'enrolled';
                                        $statusClass = $status === 'enrolled' ? 'success' : ($status === 'completed' ? 'primary' : 'secondary');
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($status) ?></span>
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

