<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-diagram-2"></i> Batch Semester Assignments</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignmentModal" onclick="clearForm()">
            <i class="bi bi-plus-circle"></i> Assign Semester to Batch
        </button>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="assignmentsTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Batch</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>Semester</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalLabel">Assign Semester to Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignmentForm">
                <div class="modal-body">
                    <input type="hidden" id="assignment_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                            <select class="form-select" id="batch_id" name="batch_id" required>
                                <option value="">Select Batch</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="semester_id" name="semester_id" required>
                                <option value="">Select Semester</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Save Assignment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let assignmentsTable;
let isEditMode = false;

$(document).ready(function() {
    loadBatches();
    loadSemesters();
    
    // Initialize DataTable
    assignmentsTable = $('#assignmentsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/batch-semesters/getData') ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: 'batch_name',
                render: function(data, type, row) {
                    return `${data} (${row.batch_code})`;
                }
            },
            { data: 'department_name' },
            { 
                data: 'program_name',
                render: function(data, type, row) {
                    return `${data} (${row.program_code})`;
                }
            },
            { 
                data: 'semester_name',
                render: function(data, type, row) {
                    return `${data} (${row.semester_code})`;
                }
            },
            { data: 'start_date' },
            { data: 'end_date' },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'active' ? 'success' : data === 'completed' ? 'info' : 'secondary';
                    return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'created_at' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning" onclick="editAssignment(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAssignment(${row.id})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                },
                orderable: false
            }
        ],
        order: [[0, 'desc']]
    });
});

function loadBatches() {
    $.ajax({
        url: '<?= base_url('admin/batch-semesters/getBatches') ?>',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#batch_id').html('<option value="">Loading batches...</option>');
        },
        success: function(response) {
            const select = $('#batch_id');
            select.html('<option value="">Select Batch</option>');
            response.data.forEach(function(batch) {
                select.append(`<option value="${batch.id}">${batch.name} (${batch.code}) - ${batch.start_year}-${batch.end_year}</option>`);
            });
        },
        error: function() {
            console.error('Failed to load batches');
            $('#batch_id').html('<option value="">Error loading batches</option>');
        }
    });
}

function loadSemesters() {
    $.ajax({
        url: '<?= base_url('admin/batch-semesters/getSemesters') ?>',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#semester_id').html('<option value="">Loading semesters...</option>');
        },
        success: function(response) {
            const select = $('#semester_id');
            select.html('<option value="">Select Semester</option>');
            response.data.forEach(function(sem) {
                select.append(`<option value="${sem.id}">${sem.name} (${sem.code}) - ${sem.program_name}</option>`);
            });
        },
        error: function() {
            console.error('Failed to load semesters');
            $('#semester_id').html('<option value="">Error loading semesters</option>');
        }
    });
}

$('#assignmentForm').on('submit', function(e) {
    e.preventDefault();
    
    // Show spinner and disable button
    const saveBtn = $('#saveBtn');
    const spinner = saveBtn.find('.spinner-border');
    const btnText = saveBtn.find('span:not(.spinner-border)');
    const originalText = btnText.text();
    
    saveBtn.prop('disabled', true);
    spinner.removeClass('d-none');
    btnText.text('Saving...');
    
    // Ensure CSRF token is included
    const csrfToken = $('#csrf_token').val();
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showAlert('danger', 'Security token missing. Please refresh the page.');
        saveBtn.prop('disabled', false);
        spinner.addClass('d-none');
        btnText.text(originalText);
        return;
    }
    
    const formData = new FormData(this);
    
    const url = isEditMode 
        ? `<?= base_url('admin/batch-semesters/update') ?>/${$('#assignment_id').val()}`
        : '<?= base_url('admin/batch-semesters/create') ?>';
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function(xhr) {
            console.log('Sending AJAX request...');
        },
        success: function(response) {
            console.log('AJAX Success Response:', response);
            
            if (response.success) {
                $('#assignmentModal').modal('hide');
                assignmentsTable.ajax.reload(null, false);
                showAlert('success', response.message);
                clearForm();
                
                // Update CSRF token after successful request
                if (response.csrf_token) {
                    updateCsrfTokenFromResponse(response.csrf_token);
                }
            } else {
                console.error('Server returned success=false:', response);
                showAlert('danger', response.message || 'Operation failed');
                if (response.errors) {
                    console.error('Validation Errors:', response.errors);
                    displayErrors(response.errors);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error Details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = 'An error occurred. Please try again.';
            
            if (xhr.responseJSON) {
                console.error('Response JSON:', xhr.responseJSON);
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                // Handle CSRF token errors
                if (xhr.status === 403 || xhr.responseJSON.message?.includes('CSRF')) {
                    errorMessage = 'Session expired. Please refresh the page and try again.';
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
                
                // Display validation errors
                if (xhr.responseJSON.errors) {
                    displayErrors(xhr.responseJSON.errors);
                }
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else {
                errorMessage = 'Server error: ' + xhr.status + ' - ' + xhr.statusText;
            }
            
            showAlert('danger', errorMessage);
        },
        complete: function() {
            // Hide spinner and re-enable button
            saveBtn.prop('disabled', false);
            spinner.addClass('d-none');
            btnText.text(originalText);
        }
    });
});

// Function to update CSRF token from response
function updateCsrfTokenFromResponse(token) {
    const tokenInput = $('#csrf_token');
    if (tokenInput.length) {
        tokenInput.val(token);
    }
}

function editAssignment(id) {
    isEditMode = true;
    $('#assignmentModalLabel').text('Edit Assignment');
    
    // Show spinner on edit button
    const editBtn = $(`button[onclick="editAssignment(${id})"]`);
    const originalHtml = editBtn.html();
    editBtn.prop('disabled', true);
    editBtn.html('<span class="spinner-border spinner-border-sm"></span>');
    
    // Load dropdowns first
    loadBatches();
    loadSemesters();
    
    $.ajax({
        url: `<?= base_url('admin/batch-semesters/edit') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#assignment_id').val(data.id);
                
                // Set values after a short delay to ensure dropdowns are loaded
                setTimeout(function() {
                    $('#batch_id').val(data.batch_id);
                    $('#semester_id').val(data.semester_id);
                    $('#start_date').val(data.start_date || '');
                    $('#end_date').val(data.end_date || '');
                    $('#status').val(data.status);
                    $('#assignmentModal').modal('show');
                }, 200);
            } else {
                showAlert('danger', response.message || 'Failed to load assignment data');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while loading assignment data.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert('danger', errorMessage);
        },
        complete: function() {
            editBtn.prop('disabled', false);
            editBtn.html(originalHtml);
        }
    });
}

function deleteAssignment(id) {
    if (confirm('Are you sure you want to delete this assignment?')) {
        // Show loading indicator on delete button
        const deleteBtn = $(`button[onclick="deleteAssignment(${id})"]`);
        const originalHtml = deleteBtn.html();
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: `<?= base_url('admin/batch-semesters/delete') ?>/${id}`,
            type: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    assignmentsTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to delete assignment');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while deleting.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                deleteBtn.prop('disabled', false);
                deleteBtn.html(originalHtml);
            }
        });
    }
}

function clearForm() {
    isEditMode = false;
    $('#assignmentForm')[0].reset();
    $('#assignment_id').val('');
    $('#assignmentModalLabel').text('Assign Semester to Batch');
    loadBatches();
    loadSemesters();
    $('.invalid-feedback').text('');
    $('.form-control, .form-select').removeClass('is-invalid');
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
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alertHtml);
    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}
</script>

