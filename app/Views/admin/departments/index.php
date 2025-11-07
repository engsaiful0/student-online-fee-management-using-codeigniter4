<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-building"></i> Departments Management</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick="clearForm()">
            <i class="bi bi-plus-circle"></i> Add New Department
        </button>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="departmentsTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
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

<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentModalLabel">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="departmentForm">
                <div class="modal-body">
                    <input type="hidden" id="department_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
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
                        <span>Save Department</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let departmentsTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    departmentsTable = $('#departmentsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/departments/getData') ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'code' },
            { data: 'name' },
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
                        <button class="btn btn-sm btn-warning" onclick="editDepartment(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteDepartment(${row.id})" title="Delete">
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

// Form submission
$('#departmentForm').on('submit', function(e) {
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
    
    // Log form data for debugging
    console.log('Form Data:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    const url = isEditMode 
        ? `<?= base_url('admin/departments/update') ?>/${$('#department_id').val()}`
        : '<?= base_url('admin/departments/create') ?>';
    
    console.log('AJAX Request URL:', url);
    console.log('Is Edit Mode:', isEditMode);
    
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
                $('#departmentModal').modal('hide');
                departmentsTable.ajax.reload(null, false); // Reload without resetting pagination
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
                if (response.debug) {
                    console.error('Debug Info:', response.debug);
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
                if (xhr.responseText) {
                    console.error('Response Text:', xhr.responseText);
                }
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

// Function to update CSRF token (not needed - token is in form)
function updateCsrfToken() {
    // CSRF token is already in the form, no need to update
    return true;
}

// Function to update CSRF token from response
function updateCsrfTokenFromResponse(token) {
    const tokenInput = $('#csrf_token');
    if (tokenInput.length) {
        tokenInput.val(token);
    }
}

function editDepartment(id) {
    isEditMode = true;
    $('#departmentModalLabel').text('Edit Department');
    
    $.ajax({
        url: `<?= base_url('admin/departments/edit') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#department_id').val(data.id);
                $('#code').val(data.code);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#status').val(data.status);
                $('#departmentModal').modal('show');
            }
        }
    });
}

function deleteDepartment(id) {
    if (confirm('Are you sure you want to delete this department?')) {
        // Show loading indicator
        const loadingHtml = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        
        $.ajax({
            url: `<?= base_url('admin/departments/delete') ?>/${id}`,
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
                    departmentsTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to delete department');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while deleting.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            }
        });
    }
}

function clearForm() {
    isEditMode = false;
    $('#departmentForm')[0].reset();
    $('#department_id').val('');
    $('#departmentModalLabel').text('Add Department');
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


