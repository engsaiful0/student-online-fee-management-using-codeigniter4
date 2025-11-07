<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-receipt"></i> Student Payment Report</h4>
        <div class="btn-group" role="group">
            <a href="<?= base_url('admin/reports/student-payment-report/pdf') ?>" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('admin/reports/student-payment-report/excel') ?>" class="btn btn-success">
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
                    <th>Fee Type</th>
                    <th>Fee Title</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Paid Amount</th>
                    <th>Remaining</th>
                    <th>Due Date</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Receipt Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted">No payment data found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fees as $fee): 
                        $remaining = $fee['amount'] - $fee['paid_amount'];
                    ?>
                        <tr>
                            <td><strong><?= esc($fee['student_student_id'] ?? '-') ?></strong></td>
                            <td><?= esc($fee['student_name'] ?? '-') ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst(str_replace('_', ' ', $fee['fee_type'])) ?></span></td>
                            <td><?= esc($fee['fee_title']) ?></td>
                            <td><?= esc($fee['course_name'] ?? '-') ?></td>
                            <td><?= number_format($fee['amount'], 2) ?></td>
                            <td class="text-success"><?= number_format($fee['paid_amount'], 2) ?></td>
                            <td class="<?= $remaining > 0 ? 'text-danger' : 'text-success' ?>"><?= number_format($remaining, 2) ?></td>
                            <td><?= $fee['due_date'] ? date('M d, Y', strtotime($fee['due_date'])) : '-' ?></td>
                            <td><?= $fee['payment_date'] ? date('M d, Y H:i', strtotime($fee['payment_date'])) : '-' ?></td>
                            <td>
                                <?php
                                $statusClass = $fee['status'] === 'paid' ? 'success' : ($fee['status'] === 'pending' ? 'warning' : ($fee['status'] === 'partial' ? 'info' : 'secondary'));
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($fee['status']) ?></span>
                            </td>
                            <td><?= esc($fee['receipt_number'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

