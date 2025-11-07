<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-credit-card"></i> Fees Payment</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestFeeModal">
            <i class="bi bi-plus-circle"></i> Request Fee
        </button>
    </div>

    <!-- Fees Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="text-primary">Total Fees</h5>
                    <h3 class="mb-0"><?= number_format(array_sum(array_column($fees ?? [], 'amount')), 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h5 class="text-success">Paid Amount</h5>
                    <h3 class="mb-0"><?= number_format(array_sum(array_column($fees ?? [], 'paid_amount')), 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h5 class="text-warning">Pending</h5>
                    <h3 class="mb-0"><?= number_format(array_sum(array_column($fees ?? [], 'amount')) - array_sum(array_column($fees ?? [], 'paid_amount')), 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h5 class="text-info">Pending Authorization</h5>
                    <h3 class="mb-0"><?= count(array_filter($fees ?? [], function($f) { return $f['status'] === 'pending'; })) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Fees Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Fee Title</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Remaining</th>
                    <th>Due Date</th>
                    <th>Payment Date</th>
                    <th>Transaction ID</th>
                    <th>Receipt</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted">
                            <i class="bi bi-info-circle"></i> No fees found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fees as $fee): 
                        $remainingAmount = $fee['amount'] - $fee['paid_amount'];
                        $canPay = $remainingAmount > 0 && $fee['status'] !== 'paid';
                    ?>
                        <tr>
                            <td>
                                <span class="badge bg-secondary"><?= ucfirst(str_replace('_', ' ', $fee['fee_type'])) ?></span>
                            </td>
                            <td><?= esc($fee['fee_title']) ?></td>
                            <td>
                                <?php if (!empty($fee['course_name'])): ?>
                                    <?= esc($fee['course_name']) ?> (<?= esc($fee['course_code'] ?? '') ?>)
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= number_format($fee['amount'], 2) ?></strong></td>
                            <td><?= number_format($fee['paid_amount'], 2) ?></td>
                            <td>
                                <strong class="<?= $remainingAmount > 0 ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($remainingAmount, 2) ?>
                                </strong>
                            </td>
                            <td><?= $fee['due_date'] ? date('M d, Y', strtotime($fee['due_date'])) : '-' ?></td>
                            <td><?= $fee['payment_date'] ? date('M d, Y H:i', strtotime($fee['payment_date'])) : '-' ?></td>
                            <td><?= esc($fee['transaction_id'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($fee['receipt_number'])): ?>
                                    <span class="badge bg-info"><?= esc($fee['receipt_number']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $status = $fee['status'] ?? 'pending';
                                $statusClass = $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : ($status === 'partial' ? 'info' : 'danger'));
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td>
                                <?php if ($canPay): ?>
                                    <button class="btn btn-sm btn-primary" onclick="makePayment(<?= $fee['id'] ?>, <?= $fee['amount'] ?>, <?= $fee['paid_amount'] ?>)" title="Make Payment">
                                        <i class="bi bi-credit-card"></i> Pay
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" id="fee_id" name="fee_id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="mb-3">
                        <label class="form-label">Fee Amount</label>
                        <input type="text" class="form-control" id="fee_amount_display" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Already Paid</label>
                        <input type="text" class="form-control" id="paid_amount_display" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Remaining Amount</label>
                        <input type="text" class="form-control" id="remaining_amount_display" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="payment_amount" name="payment_amount" required>
                        <small class="text-muted">Enter the amount you are paying</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="online">Online Payment</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID if applicable">
                    </div>
                    
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Submit Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Request Fee Modal -->
<div class="modal fade" id="requestFeeModal" tabindex="-1" aria-labelledby="requestFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestFeeModalLabel">Request Fee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="requestFeeForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token_fee">
                    
                    <div class="mb-3">
                        <label for="fee_type" class="form-label">Fee Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="fee_type" name="fee_type" required>
                            <option value="course_fee">Course Fee</option>
                            <option value="tuition_fee">Tuition Fee</option>
                            <option value="registration_fee">Registration Fee</option>
                            <option value="examination_fee">Examination Fee</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="course_offering_id" class="form-label">Course (Optional)</label>
                        <select class="form-select" id="course_offering_id" name="course_offering_id">
                            <option value="">Select Course</option>
                            <?php foreach ($enrolledCourses as $course): ?>
                                <option value="<?= $course['course_offering_id'] ?? '' ?>"><?= esc($course['course_title']) ?> (<?= esc($course['course_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Select a course if this fee is related to a specific course</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fee_title" class="form-label">Fee Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fee_title" name="fee_title" required placeholder="e.g., Course Fee for Web Development">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Fee description..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required placeholder="0.00">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitFeeRequestBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Submit Request</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Make Payment Form Submission
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitPaymentBtn');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('span:not(.spinner-border)');
        const originalText = btnText.text();
        
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Processing...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url('student/fees/makePayment') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                    
                    if (response.csrf_token) {
                        $('#csrf_token').val(response.csrf_token);
                    }
                } else {
                    showAlert('danger', response.message || 'Payment failed');
                    if (response.errors) {
                        displayErrors(response.errors);
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text(originalText);
            }
        });
    });
    
    // Request Fee Form Submission
    $('#requestFeeForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitFeeRequestBtn');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('span:not(.spinner-border)');
        const originalText = btnText.text();
        
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Submitting...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url('student/fees/createFeeRequest') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#requestFeeModal').modal('hide');
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message || 'Request failed');
                    if (response.errors) {
                        displayErrors(response.errors);
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text(originalText);
            }
        });
    });
});

function makePayment(feeId, totalAmount, paidAmount) {
    const remainingAmount = totalAmount - paidAmount;
    
    $('#fee_id').val(feeId);
    $('#fee_amount_display').val('<?= config('App')->currency ?? '' ?>' + parseFloat(totalAmount).toFixed(2));
    $('#paid_amount_display').val('<?= config('App')->currency ?? '' ?>' + parseFloat(paidAmount).toFixed(2));
    $('#remaining_amount_display').val('<?= config('App')->currency ?? '' ?>' + parseFloat(remainingAmount).toFixed(2));
    $('#payment_amount').val('');
    $('#payment_amount').attr('max', remainingAmount);
    $('#payment_method').val('');
    $('#transaction_id').val('');
    $('#remarks').val('');
    
    $('#paymentForm')[0].reset();
    $('#fee_id').val(feeId);
    
    $('#paymentModal').modal('show');
}

function displayErrors(errors) {
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    $.each(errors, function(field, message) {
        const input = $(`#${field}`);
        input.addClass('is-invalid');
        input.siblings('.invalid-feedback').text(message);
    });
}

function showAlert(type, message) {
    $('.alert').remove();
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.content-card').prepend(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}
</script>

