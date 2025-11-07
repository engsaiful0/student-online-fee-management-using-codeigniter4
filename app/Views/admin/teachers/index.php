<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-person-badge"></i> Teachers Management</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teacherModal" onclick="clearForm()">
            <i class="bi bi-plus-circle"></i> Add New Teacher
        </button>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="teachersTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Qualification</th>
                    <th>Experience</th>
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

<!-- Teacher Modal -->
<div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teacherModalLabel">Add Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="teacherForm">
                <div class="modal-body">
                    <input type="hidden" id="teacher_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="e.g., EMP001">
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
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g., +1234567890">
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
                            <label for="designation_id" class="form-label">Designation</label>
                            <select class="form-select" id="designation_id" name="designation_id">
                                <option value="">Select Designation</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="qualification" class="form-label">Qualification</label>
                        <input type="text" class="form-control" id="qualification" name="qualification" placeholder="e.g., Ph.D. in Computer Science">
                    </div>
                    
                    <div class="mb-3">
                        <label for="specialization" class="form-label">Specialization</label>
                        <textarea class="form-control" id="specialization" name="specialization" rows="2" placeholder="Areas of specialization"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="experience_years" class="form-label">Experience (Years)</label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" min="0" max="50" placeholder="0">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Save Teacher</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let teachersTable;
let isEditMode = false;

$(document).ready(function() {
    loadDepartments();
    loadDesignations();
    
    // Initialize DataTable
    teachersTable = $('#teachersTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/teachers/getData') ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'employee_id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'department' },
            { data: 'designation' },
            { data: 'qualification' },
            { 
                data: 'experience_years',
                render: function(data) {
                    return data ? data + ' years' : '-';
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
                        <button class="btn btn-sm btn-warning" onclick="editTeacher(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTeacher(${row.id})" title="Delete">
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

function loadDepartments() {
    $.ajax({
        url: '<?= base_url('admin/teachers/getDepartments') ?>',
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

function loadDesignations() {
    $.ajax({
        url: '<?= base_url('admin/teachers/getDesignations') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const select = $('#designation_id');
            select.html('<option value="">Select Designation</option>');
            response.data.forEach(function(desig) {
                select.append(`<option value="${desig.id}">${desig.name} (${desig.code})</option>`);
            });
        }
    });
}

// Form submission
$('#teacherForm').on('submit', function(e) {
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
    
    const url = isEditMode 
        ? `<?= base_url('admin/teachers/update') ?>/${$('#teacher_id').val()}`
        : '<?= base_url('admin/teachers/create') ?>';
    
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
                $('#teacherModal').modal('hide');
                teachersTable.ajax.reload(null, false);
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

function editTeacher(id) {
    isEditMode = true;
    $('#teacherModalLabel').text('Edit Teacher');
    
    const editBtn = $(`button[onclick="editTeacher(${id})"]`);
    const originalHtml = editBtn.html();
    editBtn.prop('disabled', true);
    editBtn.html('<span class="spinner-border spinner-border-sm"></span>');
    
    loadDepartments();
    loadDesignations();
    
    $.ajax({
        url: `<?= base_url('admin/teachers/edit') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#teacher_id').val(data.id);
                setTimeout(function() {
                    $('#employee_id').val(data.employee_id || '');
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone || '');
                    $('#department_id').val(data.department_id || '');
                    $('#designation_id').val(data.designation_id || '');
                    $('#qualification').val(data.qualification || '');
                    $('#specialization').val(data.specialization || '');
                    $('#experience_years').val(data.experience_years || 0);
                    $('#status').val(data.status);
                    $('#teacherModal').modal('show');
                }, 200);
            } else {
                showAlert('danger', response.message || 'Failed to load teacher data');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Failed to load teacher data. Please try again.');
        },
        complete: function() {
            editBtn.prop('disabled', false);
            editBtn.html(originalHtml);
        }
    });
}

function deleteTeacher(id) {
    if (confirm('Are you sure you want to delete this teacher?')) {
        const deleteBtn = $(`button[onclick="deleteTeacher(${id})"]`);
        const originalHtml = deleteBtn.html();
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: `<?= base_url('admin/teachers/delete') ?>/${id}`,
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
                    teachersTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to delete teacher');
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
    $('#teacherForm')[0].reset();
    $('#teacher_id').val('');
    $('#teacherModalLabel').text('Add Teacher');
    loadDepartments();
    loadDesignations();
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

function updateCsrfTokenFromResponse(token) {
    $('#csrf_token').val(token);
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

