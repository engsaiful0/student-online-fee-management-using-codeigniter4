<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-person-circle"></i> My Profile</h4>
    </div>

    <div class="row">
        <!-- Profile Information Card -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="bi bi-person-badge"></i> Student ID:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($student['student_id'] ?? 'N/A') ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="bi bi-person"></i> Full Name:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($student['name'] ?? 'N/A') ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="bi bi-envelope"></i> Email:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($student['email'] ?? 'N/A') ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="bi bi-telephone"></i> Phone:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($student['phone'] ?? 'N/A') ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="bi bi-geo-alt"></i> Address:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($student['address'] ?? 'N/A') ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="bi bi-info-circle"></i> Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-<?= ($student['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($student['status'] ?? 'active') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong><i class="bi bi-collection"></i> Batch:</strong>
                        <div class="mt-1">
                            <?php if (!empty($student['batch_name'])): ?>
                                <span class="badge bg-primary">
                                    <?= esc($student['batch_name']) ?> (<?= esc($student['batch_code'] ?? '') ?>)
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Not Assigned</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong><i class="bi bi-calendar"></i> Session:</strong>
                        <div class="mt-1">
                            <?= esc($student['session'] ?? 'N/A') ?>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong><i class="bi bi-building"></i> Department:</strong>
                        <div class="mt-1">
                            <?php if (!empty($student['department_name'])): ?>
                                <span class="badge bg-secondary">
                                    <?= esc($student['department_name']) ?> (<?= esc($student['department_code'] ?? '') ?>)
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Not Assigned</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong><i class="bi bi-journal-text"></i> Program:</strong>
                        <div class="mt-1">
                            <?php if (!empty($student['program_name'])): ?>
                                <span class="badge bg-success">
                                    <?= esc($student['program_name']) ?> (<?= esc($student['program_code'] ?? '') ?>)
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Not Assigned</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong><i class="bi bi-calendar-plus"></i> Account Created:</strong>
                                <div class="mt-1">
                                    <?= $student['created_at'] ? date('F d, Y \a\t h:i A', strtotime($student['created_at'])) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong><i class="bi bi-calendar-check"></i> Last Updated:</strong>
                                <div class="mt-1">
                                    <?= $student['updated_at'] ? date('F d, Y \a\t h:i A', strtotime($student['updated_at'])) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

