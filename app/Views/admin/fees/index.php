<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-cash-coin"></i> Fees Received</h4>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-warning" id="pendingBtn" onclick="showPendingFees()">
                <i class="bi bi-clock-history"></i> Pending Payments
            </button>
            <button type="button" class="btn btn-secondary" id="allBtn" onclick="showAllFees()">
                <i class="bi bi-list"></i> All Fees
            </button>
        </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="feesTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Fee Type</th>
                    <th>Fee Title</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Remaining</th>
                    <th>Due Date</th>
                    <th>Payment Date</th>
                    <th>Payment Method</th>
                    <th>Transaction ID</th>
                    <th>Receipt</th>
                    <th>Status</th>
                    <th>Authorized By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Authorize Payment Modal -->
<div class="modal fade" id="authorizeModal" tabindex="-1" aria-labelledby="authorizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="authorizeModalLabel">Authorize Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="authorizeForm">
                <div class="modal-body">
                    <input type="hidden" id="fee_id_auth" name="fee_id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token_auth">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Are you sure you want to authorize this payment? Once authorized, a receipt will be generated.
                    </div>
                    
                    <div id="feeDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="rejectPayment()">Reject</button>
                    <button type="submit" class="btn btn-success" id="authorizeBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Authorize Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">Reject Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="fee_id_reject" name="fee_id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token_reject">
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Rejecting this payment will reset the paid amount to zero.
                    </div>
                    
                    <div class="mb-3">
                        <label for="reject_remarks" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_remarks" name="remarks" rows="3" required placeholder="Enter reason for rejection..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="rejectBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Reject Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let feesTable;
let currentView = 'all';

$(document).ready(function() {
    if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
        setTimeout(arguments.callee, 100);
        return;
    }
    
    // Initialize DataTable
    feesTable = $('#feesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/fees/getData') ?>',
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataSrc: function(json) {
                if (json.error) {
                    console.error('Error:', json.error);
                    showAlert('danger', json.error);
                    return [];
                }
                return json.data || [];
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', error, thrown);
                showAlert('danger', 'Failed to load fees data. Please refresh the page.');
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'student_name',
                render: function(data, type, row) {
                    return `${data} <br><small class="text-muted">ID: ${row.student_id}</small>`;
                }
            },
            { data: 'fee_type' },
            { data: 'fee_title' },
            { data: 'course' },
            { data: 'amount' },
            { data: 'paid_amount' },
            { 
                data: 'remaining_amount',
                render: function(data) {
                    const amount = parseFloat(data.replace(/,/g, ''));
                    return `<strong class="${amount > 0 ? 'text-danger' : 'text-success'}">${data}</strong>`;
                }
            },
            { data: 'due_date' },
            { data: 'payment_date' },
            { data: 'payment_method' },
            { data: 'transaction_id' },
            { 
                data: 'receipt_number',
                render: function(data) {
                    if (data && data !== '-') {
                        return `<span class="badge bg-info">${data}</span>`;
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const statusClass = data === 'paid' ? 'success' : (data === 'pending' ? 'warning' : (data === 'partial' ? 'info' : 'danger'));
                    return `<span class="badge bg-${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'authorized_by' },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.status === 'pending') {
                        return `
                            <button class="btn btn-sm btn-success" onclick="authorizePayment(${row.id})" title="Authorize">
                                <i class="bi bi-check-circle"></i> Authorize
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectPaymentModal(${row.id})" title="Reject">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        `;
                    }
                    return '<span class="text-muted">-</span>';
                },
                orderable: false
            }
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable: "No fees found",
            processing: "Loading fees...",
            zeroRecords: "No matching fees found"
        }
    });
});

function showPendingFees() {
    currentView = 'pending';
    $('#pendingBtn').removeClass('btn-secondary').addClass('btn-warning');
    $('#allBtn').removeClass('btn-warning').addClass('btn-secondary');
    
    feesTable.ajax.url('<?= base_url('admin/fees/getPendingFees') ?>').load();
}

function showAllFees() {
    currentView = 'all';
    $('#allBtn').removeClass('btn-secondary').addClass('btn-warning');
    $('#pendingBtn').removeClass('btn-warning').addClass('btn-secondary');
    
    feesTable.ajax.url('<?= base_url('admin/fees/getData') ?>').load();
}

function authorizePayment(feeId) {
    $('#fee_id_auth').val(feeId);
    
    // Fetch fee details
    $.ajax({
        url: `<?= base_url('admin/fees/getData') ?>`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data) {
                const fee = response.data.find(f => f.id == feeId);
                if (fee) {
                    let detailsHtml = `
                        <div class="mb-3">
                            <strong>Student:</strong> ${fee.student_name} (${fee.student_id})<br>
                            <strong>Fee:</strong> ${fee.fee_title}<br>
                            <strong>Amount:</strong> ${fee.amount}<br>
                            <strong>Paid:</strong> ${fee.paid_amount}<br>
                            <strong>Payment Method:</strong> ${fee.payment_method}<br>
                            <strong>Transaction ID:</strong> ${fee.transaction_id || '-'}
                        </div>
                    `;
                    $('#feeDetails').html(detailsHtml);
                }
            }
            $('#authorizeModal').modal('show');
        },
        error: function() {
            $('#authorizeModal').modal('show');
        }
    });
}

function rejectPaymentModal(feeId) {
    $('#fee_id_reject').val(feeId);
    $('#reject_remarks').val('');
    $('#rejectModal').modal('show');
}

// Authorize Form Submission
$('#authorizeForm').on('submit', function(e) {
    e.preventDefault();
    
    const authorizeBtn = $('#authorizeBtn');
    const spinner = authorizeBtn.find('.spinner-border');
    const btnText = authorizeBtn.find('span:not(.spinner-border)');
    const originalText = btnText.text();
    
    authorizeBtn.prop('disabled', true);
    spinner.removeClass('d-none');
    btnText.text('Authorizing...');
    
    const feeId = $('#fee_id_auth').val();
    
    $.ajax({
        url: `<?= base_url('admin/fees/authorizePayment') ?>/${feeId}`,
        type: 'POST',
        data: {
            <?= csrf_token() ?>: $('#csrf_token_auth').val()
        },
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                $('#authorizeModal').modal('hide');
                feesTable.ajax.reload(null, false);
                showAlert('success', response.message + (response.receipt_number ? ' Receipt: ' + response.receipt_number : ''));
                
                if (response.csrf_token) {
                    $('#csrf_token_auth').val(response.csrf_token);
                }
            } else {
                showAlert('danger', response.message || 'Authorization failed');
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
            authorizeBtn.prop('disabled', false);
            spinner.addClass('d-none');
            btnText.text(originalText);
        }
    });
});

// Reject Form Submission
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    
    const rejectBtn = $('#rejectBtn');
    const spinner = rejectBtn.find('.spinner-border');
    const btnText = rejectBtn.find('span:not(.spinner-border)');
    const originalText = btnText.text();
    
    rejectBtn.prop('disabled', true);
    spinner.removeClass('d-none');
    btnText.text('Rejecting...');
    
    const feeId = $('#fee_id_reject').val();
    const formData = new FormData(this);
    
    $.ajax({
        url: `<?= base_url('admin/fees/rejectPayment') ?>/${feeId}`,
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
                $('#rejectModal').modal('hide');
                feesTable.ajax.reload(null, false);
                showAlert('success', response.message);
                
                if (response.csrf_token) {
                    $('#csrf_token_reject').val(response.csrf_token);
                }
            } else {
                showAlert('danger', response.message || 'Rejection failed');
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
            rejectBtn.prop('disabled', false);
            spinner.addClass('d-none');
            btnText.text(originalText);
        }
    });
});

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

