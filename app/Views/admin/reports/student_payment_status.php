<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-credit-card"></i> Student Payment Status Report</h4>
        <div class="btn-group" role="group">
            <a href="<?= base_url('admin/reports/student-payment-status/pdf') ?>" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('admin/reports/student-payment-status/excel') ?>" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
    
    <p class="text-muted mb-3">Generated on: <?= date('Y-m-d H:i:s') ?></p>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Total Fees</th>
                    <th>Paid Amount</th>
                    <th>Pending Amount</th>
                    <th>Status</th>
                    <th>Fees Count</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paymentStatus)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No payment data found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($paymentStatus as $status): ?>
                        <tr>
                            <td><strong><?= esc($status['student_id']) ?></strong></td>
                            <td><?= esc($status['student_name']) ?></td>
                            <td><?= esc($status['email']) ?></td>
                            <td><?= number_format($status['total_fees'], 2) ?></td>
                            <td class="text-success"><?= number_format($status['paid_amount'], 2) ?></td>
                            <td class="text-danger"><?= number_format($status['pending_amount'], 2) ?></td>
                            <td>
                                <?php
                                $statusClass = $status['status'] === 'Paid' ? 'success' : ($status['status'] === 'Partial' ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= esc($status['status']) ?></span>
                            </td>
                            <td><?= $status['fees_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

