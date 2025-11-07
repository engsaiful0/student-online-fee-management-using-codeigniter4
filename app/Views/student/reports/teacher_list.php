<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-person-badge"></i> Teacher List Report</h4>
        <div class="btn-group" role="group">
            <a href="<?= base_url('student/reports/teacher-list/pdf') ?>" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('student/reports/teacher-list/excel') ?>" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
    
    <p class="text-muted mb-3">Generated on: <?= date('Y-m-d H:i:s') ?></p>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Qualification</th>
                    <th>Experience</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">No teachers found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?= esc($teacher['employee_id'] ?? '-') ?></td>
                            <td><strong><?= esc($teacher['name']) ?></strong></td>
                            <td><?= esc($teacher['email']) ?></td>
                            <td><?= esc($teacher['phone'] ?? '-') ?></td>
                            <td><?= esc($teacher['department_name']) ?></td>
                            <td><?= esc($teacher['designation_name']) ?></td>
                            <td><?= esc($teacher['qualification'] ?? '-') ?></td>
                            <td><?= $teacher['experience_years'] ?? '0' ?> years</td>
                            <td>
                                <span class="badge bg-<?= $teacher['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($teacher['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

