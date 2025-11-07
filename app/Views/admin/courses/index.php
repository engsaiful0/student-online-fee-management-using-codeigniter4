<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-book-half"></i> Courses Management</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseModal" onclick="clearForm()">
            <i class="bi bi-plus-circle"></i> Add New Course
        </button>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="coursesTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Credit Hours</th>
                    <th>Description</th>
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

<!-- Course Modal -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="courseForm">
                <div class="modal-body">
                    <input type="hidden" id="course_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Course Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="credit_hours" class="form-label">Credit Hours</label>
                        <input type="number" class="form-control" id="credit_hours" name="credit_hours" min="1" max="10" value="3">
                        <small class="form-text text-muted">Default: 3 credit hours</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                        <span>Save Course</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let coursesTable;
let isEditMode = false;

$(document).ready(function() {
    loadDepartments();
    
    // Initialize DataTable
    coursesTable = $('#coursesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/courses/getData') ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'code' },
            { data: 'name' },
            { 
                data: 'department_name',
                render: function(data, type, row) {
                    return `${data} (${row.department_code})`;
                }
            },
            { data: 'credit_hours' },
            { data: 'description' },
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
                        <button class="btn btn-sm btn-warning" onclick="editCourse(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCourse(${row.id})" title="Delete">
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
        url: '<?= base_url('admin/courses/getDepartments') ?>',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#department_id').html('<option value="">Loading departments...</option>');
        },
        success: function(response) {
            const select = $('#department_id');
            select.html('<option value="">Select Department</option>');
            response.data.forEach(function(dept) {
                select.append(`<option value="${dept.id}">${dept.name} (${dept.code})</option>`);
            });
        },
        error: function() {
            console.error('Failed to load departments');
            $('#department_id').html('<option value="">Error loading departments</option>');
        }
    });
}

// Form submission
$('#courseForm').on('submit', function(e) {
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
        console.error('CSRF token not found!');
        showAlert('danger', 'Security token missing. Please refresh the page.');
        saveBtn.prop('disabled', false);
        spinner.addClass('d-none');
        btnText.text(originalText);
        return;
    }
    
    const url = isEditMode 
        ? `<?= base_url('admin/courses/update') ?>/${$('#course_id').val()}`
        : '<?= base_url('admin/courses/create') ?>';
    
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
                $('#courseModal').modal('hide');
                coursesTable.ajax.reload(null, false);
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

function editCourse(id) {
    isEditMode = true;
    $('#courseModalLabel').text('Edit Course');
    
    // Show spinner on edit button
    const editBtn = $(`button[onclick="editCourse(${id})"]`);
    const originalHtml = editBtn.html();
    editBtn.prop('disabled', true);
    editBtn.html('<span class="spinner-border spinner-border-sm"></span>');
    
    // Load departments first
    loadDepartments();
    
    $.ajax({
        url: `<?= base_url('admin/courses/edit') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#course_id').val(data.id);
                
                // Set values after a short delay to ensure dropdown is loaded
                setTimeout(function() {
                    $('#department_id').val(data.department_id);
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#credit_hours').val(data.credit_hours);
                    $('#description').val(data.description);
                    $('#status').val(data.status);
                    $('#courseModal').modal('show');
                }, 200);
            } else {
                showAlert('danger', response.message || 'Failed to load course data');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while loading course data.';
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

function deleteCourse(id) {
    if (confirm('Are you sure you want to delete this course?')) {
        // Show loading indicator on delete button
        const deleteBtn = $(`button[onclick="deleteCourse(${id})"]`);
        const originalHtml = deleteBtn.html();
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: `<?= base_url('admin/courses/delete') ?>/${id}`,
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
                    coursesTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to delete course');
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
    $('#courseForm')[0].reset();
    $('#course_id').val('');
    $('#courseModalLabel').text('Add Course');
    loadDepartments();
    $('.invalid-feedback').text('');
    $('.form-control, .form-select').removeClass('is-invalid');
    $('#credit_hours').val('3'); // Reset to default
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

