<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-people"></i> Students Management</h4>
        <div>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#csvUploadModal">
                <i class="bi bi-upload"></i> Upload CSV
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="clearForm()">
                <i class="bi bi-plus-circle"></i> Add New Student
            </button>
        </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="studentsTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Batch</th>
                    <th>Session</th>
                    <th>Department</th>
                    <th>Program</th>
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

<!-- Student Modal (Manual Entry) -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="studentForm">
                <div class="modal-body">
                    <input type="hidden" id="student_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="student_id_field" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="student_id_field" name="student_id" placeholder="e.g., STU001">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger" id="password_required">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave empty to keep current password">
                        <small class="form-text text-muted">Default: student123 (for new students)</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="session" class="form-label">Session</label>
                            <input type="text" class="form-control" id="session" name="session" placeholder="e.g., 2024-2025">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="program_id" class="form-label">Program</label>
                            <select class="form-select" id="program_id" name="program_id">
                                <option value="">Select Program</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Save Student</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSV Upload Modal -->
<div class="modal fade" id="csvUploadModal" tabindex="-1" aria-labelledby="csvUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="csvUploadModalLabel">Upload CSV File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="csvUploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csv_csrf_token">
                    
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <small class="form-text text-muted">
                            Required CSV format: Student ID, Student Name, Batch, Session, Department, Program, Status
                        </small>
                        <div class="mt-2">
                            <a href="<?= base_url('admin/students/downloadTemplate') ?>" class="btn btn-sm btn-outline-primary" download>
                                <i class="bi bi-download"></i> Download CSV Template
                            </a>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> CSV Format Requirements:</h6>
                        <ul class="mb-0">
                            <li>First row must contain headers: <code>Student ID, Student Name, Batch, Session, Department, Program, Status</code></li>
                            <li>Batch, Department, and Program should match existing records (by name or code)</li>
                            <li>Status should be: active or inactive</li>
                            <li>Default password for all students: <strong>student123</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="uploadBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Upload CSV</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let studentsTable;
let isEditMode = false;

$(document).ready(function() {
    loadBatches();
    loadDepartments();
    loadPrograms();
    
    // Initialize DataTable
    studentsTable = $('#studentsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/students/getData') ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'student_id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'batch_name',
                render: function(data, type, row) {
                    if (data === '-') return '-';
                    return `${data} (${row.batch_code})`;
                }
            },
            { data: 'session' },
            { 
                data: 'department_name',
                render: function(data, type, row) {
                    if (data === '-') return '-';
                    return `${data} (${row.department_code})`;
                }
            },
            { 
                data: 'program_name',
                render: function(data, type, row) {
                    if (data === '-') return '-';
                    return `${data} (${row.program_code})`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'active' ? 'success' : 'secondary';
                    return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'created_at' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning" onclick="editStudent(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteStudent(${row.id})" title="Delete">
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
        url: '<?= base_url('admin/students/getBatches') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const select = $('#batch_id');
            select.html('<option value="">Select Batch</option>');
            response.data.forEach(function(batch) {
                select.append(`<option value="${batch.id}">${batch.name} (${batch.code})</option>`);
            });
        }
    });
}

function loadDepartments() {
    $.ajax({
        url: '<?= base_url('admin/students/getDepartments') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const select = $('#department_id');
            select.html('<option value="">Select Department</option>');
            response.data.forEach(function(dept) {
                select.append(`<option value="${dept.id}">${dept.name} (${dept.code})</option>`);
            });
        }
    });
}

function loadPrograms() {
    $.ajax({
        url: '<?= base_url('admin/students/getPrograms') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const select = $('#program_id');
            select.html('<option value="">Select Program</option>');
            response.data.forEach(function(prog) {
                select.append(`<option value="${prog.id}">${prog.name} (${prog.code})</option>`);
            });
        }
    });
}

// Manual Student Form submission
$('#studentForm').on('submit', function(e) {
    e.preventDefault();
    
    // Show spinner and disable button
    const saveBtn = $('#saveBtn');
    const spinner = saveBtn.find('.spinner-border');
    const btnText = saveBtn.find('span:not(.spinner-border)');
    const originalText = btnText.text();
    
    saveBtn.prop('disabled', true);
    spinner.removeClass('d-none');
    btnText.text('Saving...');
    
    const formData = new FormData(this);
    
    // Ensure CSRF token is included
    const csrfToken = $('#csrf_token').val();
    if (!csrfToken) {
        showAlert('danger', 'Security token missing. Please refresh the page.');
        saveBtn.prop('disabled', false);
        spinner.addClass('d-none');
        btnText.text(originalText);
        return;
    }
    
    const url = isEditMode 
        ? `<?= base_url('admin/students/update') ?>/${$('#student_id').val()}`
        : '<?= base_url('admin/students/create') ?>';
    
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
        success: function(response) {
            if (response.success) {
                $('#studentModal').modal('hide');
                studentsTable.ajax.reload(null, false);
                showAlert('success', response.message);
                clearForm();
                
                if (response.csrf_token) {
                    updateCsrfTokenFromResponse(response.csrf_token);
                }
            } else {
                showAlert('danger', response.message || 'Operation failed');
                if (response.errors) {
                    displayErrors(response.errors);
                }
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred. Please try again.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (xhr.responseJSON.errors) {
                    displayErrors(xhr.responseJSON.errors);
                }
            }
            showAlert('danger', errorMessage);
        },
        complete: function() {
            saveBtn.prop('disabled', false);
            spinner.addClass('d-none');
            btnText.text(originalText);
        }
    });
});

// CSV Upload Form submission
$('#csvUploadForm').on('submit', function(e) {
    e.preventDefault();
    
    const uploadBtn = $('#uploadBtn');
    const spinner = uploadBtn.find('.spinner-border');
    const btnText = uploadBtn.find('span:not(.spinner-border)');
    const originalText = btnText.text();
    
    uploadBtn.prop('disabled', true);
    spinner.removeClass('d-none');
    btnText.text('Uploading...');
    
    const formData = new FormData(this);
    
    $.ajax({
        url: '<?= base_url('admin/students/uploadCsv') ?>',
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
                $('#csvUploadModal').modal('hide');
                studentsTable.ajax.reload(null, false);
                
                let message = response.message;
                if (response.error_count > 0 && response.errors) {
                    message += '\n\nErrors:\n' + response.errors.slice(0, 5).join('\n');
                    if (response.errors.length > 5) {
                        message += '\n... and ' + (response.errors.length - 5) + ' more errors';
                    }
                }
                
                showAlert('success', message.replace(/\n/g, '<br>'));
                $('#csvUploadForm')[0].reset();
                
                if (response.csrf_token) {
                    $('#csv_csrf_token').val(response.csrf_token);
                }
            } else {
                showAlert('danger', response.message || 'Upload failed');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred during upload. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert('danger', errorMessage);
        },
        complete: function() {
            uploadBtn.prop('disabled', false);
            spinner.addClass('d-none');
            btnText.text(originalText);
        }
    });
});

function updateCsrfTokenFromResponse(token) {
    $('#csrf_token').val(token);
    $('#csv_csrf_token').val(token);
}

function editStudent(id) {
    isEditMode = true;
    $('#studentModalLabel').text('Edit Student');
    $('#password_required').addClass('d-none');
    
    const editBtn = $(`button[onclick="editStudent(${id})"]`);
    const originalHtml = editBtn.html();
    editBtn.prop('disabled', true);
    editBtn.html('<span class="spinner-border spinner-border-sm"></span>');
    
    loadBatches();
    loadDepartments();
    loadPrograms();
    
    $.ajax({
        url: `<?= base_url('admin/students/edit') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#student_id').val(data.id);
                setTimeout(function() {
                    $('#student_id_field').val(data.student_id || '');
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone || '');
                    $('#address').val(data.address || '');
                    $('#batch_id').val(data.batch_id || '');
                    $('#session').val(data.session || '');
                    $('#department_id').val(data.department_id || '');
                    $('#program_id').val(data.program_id || '');
                    $('#status').val(data.status);
                    $('#studentModal').modal('show');
                }, 200);
            } else {
                showAlert('danger', response.message || 'Failed to load student data');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Failed to load student data. Please try again.');
        },
        complete: function() {
            editBtn.prop('disabled', false);
            editBtn.html(originalHtml);
        }
    });
}

function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        const deleteBtn = $(`button[onclick="deleteStudent(${id})"]`);
        const originalHtml = deleteBtn.html();
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: `<?= base_url('admin/students/delete') ?>/${id}`,
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
                    studentsTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to delete student');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'An error occurred while deleting.');
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
    $('#studentForm')[0].reset();
    $('#student_id').val('');
    $('#studentModalLabel').text('Add Student');
    $('#password_required').removeClass('d-none');
    loadBatches();
    loadDepartments();
    loadPrograms();
    $('.invalid-feedback').text('');
    $('.form-control, .form-select').removeClass('is-invalid');
}

function displayErrors(errors) {
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    $.each(errors, function(field, message) {
        const input = $(`#${field}`);
        if (input.length === 0) {
            input = $(`#${field}_field`); // Try with _field suffix
        }
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
    }, 5000);
}
</script>

