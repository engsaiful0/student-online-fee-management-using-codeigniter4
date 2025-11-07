<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-building"></i> Department List Report</h4>
        <div class="btn-group" role="group">
            <a href="<?= base_url('student/reports/department-list/pdf') ?>" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('student/reports/department-list/excel') ?>" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
    
    <p class="text-muted mb-3">Generated on: <?= date('Y-m-d H:i:s') ?></p>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($departments)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No departments found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($departments as $dept): ?>
                        <tr>
                            <td><strong><?= esc($dept['code']) ?></strong></td>
                            <td><?= esc($dept['name']) ?></td>
                            <td><?= esc($dept['description'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= $dept['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($dept['status']) ?>
                                </span>
                            </td>
                            <td><?= $dept['created_at'] ? date('M d, Y', strtotime($dept['created_at'])) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

