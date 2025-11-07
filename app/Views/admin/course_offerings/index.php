<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-journal-bookmark"></i> Course Offerings Management</h4>
        <div>
            <button type="button" class="btn btn-outline-primary me-2" onclick="refreshTable()" title="Refresh Data">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#offeringModal" onclick="clearForm()">
                <i class="bi bi-plus-circle"></i> Add New Course Offering
            </button>
        </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="offeringsTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Semester</th>
                    <th>Program</th>
                    <th>Department</th>
                    <th>Capacity</th>
                    <th>Enrolled</th>
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

<!-- Course Offering Modal -->
<div class="modal fade" id="offeringModal" tabindex="-1" aria-labelledby="offeringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="offeringModalLabel">Add Course Offering</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="offeringForm">
                <div class="modal-body">
                    <input type="hidden" id="offering_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="batch_semester_id" class="form-label">Batch-Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="batch_semester_id" name="batch_semester_id" required>
                                <option value="">Select Batch-Semester</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="500" value="30">
                            <small class="form-text text-muted">Default: 30 students</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="enrolled_count" class="form-label">Enrolled Count</label>
                            <input type="number" class="form-control" id="enrolled_count" name="enrolled_count" min="0" value="0">
                            <small class="form-text text-muted">Number of students currently enrolled</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="full">Full</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <span>Save Offering</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let offeringsTable;
let isEditMode = false;

// Wait for jQuery and DataTables to be loaded
(function() {
    function initOfferingsTable() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            console.log('Waiting for jQuery and DataTables to load...');
            setTimeout(initOfferingsTable, 100);
            return;
        }
        
        console.log('jQuery version:', jQuery.fn.jquery);
        console.log('DataTables available:', typeof jQuery.fn.DataTable !== 'undefined');
        
        jQuery(document).ready(function($) {
            console.log('Document ready - initializing Course Offerings DataTable');
            
            // Check if table exists
            if ($('#offeringsTable').length === 0) {
                console.error('Offerings table not found!');
                return;
            }
            
            loadCourses();
            loadBatchSemesters();
            
            // Initialize DataTable
            offeringsTable = $('#offeringsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/course-offerings/getData') ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: function(json) {
                console.log('DataTable Response:', json);
                if (json.error) {
                    console.error('Server Error:', json.error);
                    showAlert('danger', json.error);
                    return [];
                }
                return json.data || [];
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', error, thrown);
                console.error('Status:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                showAlert('danger', 'Failed to load course offerings data. Please refresh the page.');
                // Try to manually load data as fallback
                loadDataManually();
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'course_name',
                render: function(data, type, row) {
                    return `${data} (${row.course_code}) - ${row.course_credit_hours} CH`;
                }
            },
            { 
                data: 'batch_name',
                render: function(data, type, row) {
                    return `${data} (${row.batch_code})`;
                }
            },
            { 
                data: 'semester_name',
                render: function(data, type, row) {
                    return `${data} (${row.semester_code})`;
                }
            },
            { 
                data: 'program_name',
                render: function(data, type, row) {
                    return `${data} (${row.program_code})`;
                }
            },
            { data: 'department_name' },
            { data: 'capacity' },
            { 
                data: 'enrolled_count',
                render: function(data, type, row) {
                    const percentage = row.capacity > 0 ? Math.round((data / row.capacity) * 100) : 0;
                    const colorClass = percentage >= 100 ? 'danger' : percentage >= 80 ? 'warning' : 'success';
                    return `<span class="badge bg-${colorClass}">${data} / ${row.capacity}</span>`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'active' ? 'success' : data === 'full' ? 'danger' : data === 'completed' ? 'info' : 'secondary';
                    return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'created_at' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning" onclick="editOffering(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteOffering(${row.id})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                },
                orderable: false
            }
        ],
        order: [[0, 'desc']],
        language: {
            processing: "Loading course offerings...",
            emptyTable: "No course offerings found. Click 'Add New Course Offering' to create one.",
            zeroRecords: "No matching course offerings found"
        }
    });
    
    console.log('DataTable initialized successfully');
    });
    }
    
    // Start initialization
    initOfferingsTable();
})();

// Manual data loading fallback
function loadDataManually() {
    console.log('Attempting manual data load...');
    $.ajax({
        url: '<?= base_url('admin/course-offerings/getData') ?>',
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('Manual Load Response:', response);
            if (response.data && Array.isArray(response.data)) {
                offeringsTable.clear();
                if (response.data.length > 0) {
                    console.log('Adding ' + response.data.length + ' rows to table');
                    offeringsTable.rows.add(response.data).draw();
                    showAlert('success', 'Data loaded successfully');
                } else {
                    console.log('No data to display');
                    offeringsTable.draw();
                    showAlert('info', 'No course offerings found. Click "Add New Course Offering" to create one.');
                }
            } else if (response.error) {
                console.error('Server Error:', response.error);
                showAlert('danger', response.error);
            } else {
                console.error('Unexpected response format:', response);
                showAlert('danger', 'Unexpected response format from server');
            }
        },
        error: function(xhr, status, error) {
            console.error('Manual Load Error:', error);
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
            showAlert('danger', 'Unable to load course offerings. Status: ' + xhr.status + ' - ' + error);
        }
    });
}

function loadCourses() {
    $.ajax({
        url: '<?= base_url('admin/course-offerings/getCourses') ?>',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#course_id').html('<option value="">Loading courses...</option>');
        },
        success: function(response) {
            const select = $('#course_id');
            select.html('<option value="">Select Course</option>');
            response.data.forEach(function(course) {
                select.append(`<option value="${course.id}">${course.name} (${course.code}) - ${course.credit_hours} CH</option>`);
            });
        },
        error: function() {
            console.error('Failed to load courses');
            $('#course_id').html('<option value="">Error loading courses</option>');
        }
    });
}

function loadBatchSemesters() {
    $.ajax({
        url: '<?= base_url('admin/course-offerings/getBatchSemesters') ?>',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#batch_semester_id').html('<option value="">Loading batch-semesters...</option>');
        },
        success: function(response) {
            const select = $('#batch_semester_id');
            select.html('<option value="">Select Batch-Semester</option>');
            response.data.forEach(function(bs) {
                select.append(`<option value="${bs.id}">${bs.batch_name} (${bs.batch_code}) - ${bs.semester_name} (${bs.semester_code})</option>`);
            });
        },
        error: function() {
            console.error('Failed to load batch-semesters');
            $('#batch_semester_id').html('<option value="">Error loading batch-semesters</option>');
        }
    });
}

$('#offeringForm').on('submit', function(e) {
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
        ? `<?= base_url('admin/course-offerings/update') ?>/${$('#offering_id').val()}`
        : '<?= base_url('admin/course-offerings/create') ?>';
    
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
                $('#offeringModal').modal('hide');
                offeringsTable.ajax.reload(null, false);
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

function editOffering(id) {
    isEditMode = true;
    $('#offeringModalLabel').text('Edit Course Offering');
    
    // Show spinner on edit button
    const editBtn = $(`button[onclick="editOffering(${id})"]`);
    const originalHtml = editBtn.html();
    editBtn.prop('disabled', true);
    editBtn.html('<span class="spinner-border spinner-border-sm"></span>');
    
    // Load dropdowns first
    loadCourses();
    loadBatchSemesters();
    
    $.ajax({
        url: `<?= base_url('admin/course-offerings/edit') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#offering_id').val(data.id);
                
                // Set values after a short delay to ensure dropdowns are loaded
                setTimeout(function() {
                    $('#course_id').val(data.course_id);
                    $('#batch_semester_id').val(data.batch_semester_id);
                    $('#capacity').val(data.capacity);
                    $('#enrolled_count').val(data.enrolled_count);
                    $('#status').val(data.status);
                    $('#offeringModal').modal('show');
                }, 200);
            } else {
                showAlert('danger', response.message || 'Failed to load offering data');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while loading offering data.';
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

function deleteOffering(id) {
    if (confirm('Are you sure you want to delete this course offering?')) {
        // Show loading indicator on delete button
        const deleteBtn = $(`button[onclick="deleteOffering(${id})"]`);
        const originalHtml = deleteBtn.html();
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: `<?= base_url('admin/course-offerings/delete') ?>/${id}`,
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
                    offeringsTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to delete offering');
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
    $('#offeringForm')[0].reset();
    $('#offering_id').val('');
    $('#offeringModalLabel').text('Add Course Offering');
    loadCourses();
    loadBatchSemesters();
    $('.invalid-feedback').text('');
    $('.form-control, .form-select').removeClass('is-invalid');
    $('#capacity').val('30'); // Reset to default
    $('#enrolled_count').val('0'); // Reset to default
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

function refreshTable() {
    console.log('Refreshing table...');
    if (offeringsTable) {
        offeringsTable.ajax.reload(null, false);
    } else {
        loadDataManually();
    }
}

function showAlert(type, message) {
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

