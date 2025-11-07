<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-person-check"></i> Users Management</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="clearForm()">
            <i class="bi bi-plus-circle"></i> Add New User
        </button>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="usersTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Permissions</th>
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
    
    <!-- Loading indicator -->
    <div id="loadingIndicator" style="display: none; text-align: center; padding: 20px;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Loading users...</p>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="id">
                    <?= csrf_field() ?>
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g., +1234567890">
                    </div>
                    
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <option value="">Select Role</option>
                        </select>
                        <small class="form-text text-muted">Select a role to assign permissions to this user.</small>
                        <div id="rolePermissions" class="mt-2" style="display: none;">
                            <small class="text-muted"><strong>Role Permissions:</strong></small>
                            <div id="permissionsList" class="mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger" id="password_required">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave empty to keep current password">
                        <small class="form-text text-muted">Default: admin123 (for new users)</small>
                        <div class="invalid-feedback"></div>
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
                        <span>Save User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Permissions View Modal -->
<div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionsModalLabel">Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="permissionsModalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading permissions...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for jQuery to be loaded
(function() {
    function initUsersTable() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            console.log('Waiting for jQuery and DataTables to load...');
            setTimeout(initUsersTable, 100);
            return;
        }
        
        console.log('jQuery version:', jQuery.fn.jquery);
        
        let usersTable;
        let isEditMode = false;
        
        jQuery(document).ready(function($) {
            console.log('Document ready - initializing Users DataTable');
            
            // Check if table exists
            if ($('#usersTable').length === 0) {
                console.error('Users table not found!');
                return;
            }
            
            // Show loading indicator
            $('#loadingIndicator').show();
            
            // Initialize DataTable
            console.log('Initializing DataTable...');
            usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('admin/users/getData') ?>',
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataSrc: function(json) {
                console.log('DataTable Response:', json);
                console.log('Data count:', json.data ? json.data.length : 0);
                
                // Hide loading indicator
                $('#loadingIndicator').hide();
                
                if (json.error) {
                    console.error('Error:', json.error);
                    showAlert('danger', json.error);
                    return [];
                }
                
                if (!json.data) {
                    console.warn('No data property in response');
                    return [];
                }
                
                if (!Array.isArray(json.data)) {
                    console.warn('Data is not an array:', typeof json.data);
                    return [];
                }
                
                console.log('Returning data array with', json.data.length, 'items');
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', error, thrown);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                // Hide loading indicator
                $('#loadingIndicator').hide();
                
                showAlert('danger', 'Failed to load users data. Please refresh the page.');
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'role',
                render: function(data) {
                    if (data === 'No Role Assigned') {
                        return '<span class="badge bg-secondary">No Role</span>';
                    }
                    return '<span class="badge bg-primary">' + data + '</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.role_id) {
                        return '<button class="btn btn-sm btn-info" onclick="window.viewPermissions(' + row.id + ', ' + row.role_id + ')" title="View Permissions"><i class="bi bi-eye"></i> View</button>';
                    }
                    return '<span class="text-muted">-</span>';
                },
                orderable: false
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
                        <button class="btn btn-sm btn-warning" onclick="editUser(${row.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                },
                orderable: false
            }
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable: "No users found",
            processing: "Loading users...",
            zeroRecords: "No matching users found"
        }
    });
    
    console.log('DataTable initialized successfully');
    
            // Reload table after initialization to ensure data loads
            setTimeout(function() {
                if (usersTable) {
                    usersTable.ajax.reload(null, false);
                }
            }, 500);
            
            // Make functions available globally
            window.usersTable = usersTable;
            window.isEditMode = false;
            
            // Define loadRoles function
            window.loadRoles = function() {
                jQuery.ajax({
                    url: '<?= base_url('admin/users/getRoles') ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const select = jQuery('#role_id');
                        select.html('<option value="">Select Role</option>');
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(role) {
                                select.append(`<option value="${role.id}">${role.name} (${role.slug})</option>`);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading roles:', error);
                    }
                });
            };
            
            // Load roles after everything is set up
            window.loadRoles();
            
            // Load permissions when role is selected
            jQuery('#role_id').on('change', function() {
                const roleId = jQuery(this).val();
                if (roleId) {
                    loadRolePermissions(roleId);
                } else {
                    jQuery('#rolePermissions').hide();
                    jQuery('#permissionsList').empty();
                }
            });
            
            window.loadRolePermissions = function(roleId) {
                jQuery.ajax({
                    url: `<?= base_url('admin/users/getRolePermissions') ?>/${roleId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            let permissionsHtml = '<div class="d-flex flex-wrap gap-1">';
                            response.data.forEach(function(permission) {
                                permissionsHtml += `<span class="badge bg-info">${permission}</span>`;
                            });
                            permissionsHtml += '</div>';
                            jQuery('#permissionsList').html(permissionsHtml);
                            jQuery('#rolePermissions').show();
                        } else {
                            jQuery('#permissionsList').html('<span class="text-muted">No permissions assigned to this role.</span>');
                            jQuery('#rolePermissions').show();
                        }
                    },
                    error: function() {
                        jQuery('#rolePermissions').hide();
                    }
                });
            };
            
            window.viewPermissions = function(userId, roleId) {
                // Show modal and load permissions
                const modal = new bootstrap.Modal(document.getElementById('permissionsModal'));
                const modalContent = jQuery('#permissionsModalContent');
                
                // Show loading state
                modalContent.html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading permissions...</p>
                    </div>
                `);
                
                modal.show();
                
                // Load permissions
                jQuery.ajax({
                    url: `<?= base_url('admin/users/getRolePermissions') ?>/${roleId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let html = `<h6 class="mb-3">Role: <strong>${response.role_name || 'Unknown'}</strong></h6>`;
                            
                            if (response.data && response.data.length > 0) {
                                html += '<div class="mb-3"><strong>Permissions:</strong></div>';
                                html += '<div class="d-flex flex-wrap gap-2">';
                                response.data.forEach(function(permission) {
                                    html += `<span class="badge bg-primary fs-6">${permission}</span>`;
                                });
                                html += '</div>';
                                html += `<div class="mt-3"><small class="text-muted">Total: ${response.data.length} permission(s)</small></div>`;
                            } else {
                                html += '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No permissions assigned to this role.</div>';
                            }
                            
                            modalContent.html(html);
                        } else {
                            modalContent.html(`
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i> Failed to load permissions. Please try again.
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading permissions:', error);
                        modalContent.html(`
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> An error occurred while loading permissions. Please try again.
                            </div>
                        `);
                    }
                });
            };
            
            // Form submission
            jQuery('#userForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show spinner and disable button
                const saveBtn = jQuery('#saveBtn');
                const spinner = saveBtn.find('.spinner-border');
                const btnText = saveBtn.find('span:not(.spinner-border)');
                const originalText = btnText.text();
                
                saveBtn.prop('disabled', true);
                spinner.removeClass('d-none');
                btnText.text('Saving...');
                
                const formData = new FormData(this);
                
                const url = window.isEditMode 
                    ? `<?= base_url('admin/users/update') ?>/${jQuery('#user_id').val()}`
                    : '<?= base_url('admin/users/create') ?>';
                
                jQuery.ajax({
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
                            jQuery('#userModal').modal('hide');
                            window.usersTable.ajax.reload(null, false);
                            window.showAlert('success', response.message);
                            window.clearForm();
                            
                            if (response.csrf_token) {
                                window.updateCsrfTokenFromResponse(response.csrf_token);
                            }
                        } else {
                            window.showAlert('danger', response.message || 'Operation failed');
                            if (response.errors) {
                                window.displayErrors(response.errors);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred. Please try again.';
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }
                            if (xhr.responseJSON.errors) {
                                window.displayErrors(xhr.responseJSON.errors);
                            }
                        } else if (xhr.status === 0) {
                            errorMessage = 'Network error. Please check your connection.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Access denied. Please refresh the page.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please try again later.';
                        } else if (xhr.responseText) {
                            console.error('Response Text:', xhr.responseText);
                        }
                        
                        window.showAlert('danger', errorMessage);
                    },
                    complete: function() {
                        saveBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                        btnText.text(originalText);
                    }
                });
            });
            
            window.editUser = function(id) {
                window.isEditMode = true;
                jQuery('#userModalLabel').text('Edit User');
                jQuery('#password_required').addClass('d-none');
                
                const editBtn = jQuery(`button[onclick="editUser(${id})"]`);
                const originalHtml = editBtn.html();
                editBtn.prop('disabled', true);
                editBtn.html('<span class="spinner-border spinner-border-sm"></span>');
                
                window.loadRoles();
                
                jQuery.ajax({
                    url: `<?= base_url('admin/users/edit') ?>/${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            jQuery('#user_id').val(data.id);
                            setTimeout(function() {
                                jQuery('#name').val(data.name);
                                jQuery('#email').val(data.email);
                                jQuery('#phone').val(data.phone || '');
                                jQuery('#role_id').val(data.role_id || '');
                                jQuery('#status').val(data.status);
                                
                                // Load permissions if role is selected
                                if (data.role_id) {
                                    window.loadRolePermissions(data.role_id);
                                } else {
                                    jQuery('#rolePermissions').hide();
                                }
                                
                                jQuery('#userModal').modal('show');
                            }, 200);
                        } else {
                            window.showAlert('danger', response.message || 'Failed to load user data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Edit User Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        let errorMessage = 'Failed to load user data. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        window.showAlert('danger', errorMessage);
                    },
                    complete: function() {
                        editBtn.prop('disabled', false);
                        editBtn.html(originalHtml);
                    }
                });
            };
            
            window.deleteUser = function(id) {
                if (confirm('Are you sure you want to delete this user?')) {
                    const deleteBtn = jQuery(`button[onclick="deleteUser(${id})"]`);
                    const originalHtml = deleteBtn.html();
                    deleteBtn.prop('disabled', true);
                    deleteBtn.html('<span class="spinner-border spinner-border-sm"></span>');
                    
                    jQuery.ajax({
                        url: `<?= base_url('admin/users/delete') ?>/${id}`,
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
                                window.usersTable.ajax.reload(null, false);
                                window.showAlert('success', response.message);
                            } else {
                                window.showAlert('danger', response.message || 'Failed to delete user');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete User Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            let errorMessage = 'An error occurred while deleting.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            window.showAlert('danger', errorMessage);
                        },
                        complete: function() {
                            deleteBtn.prop('disabled', false);
                            deleteBtn.html(originalHtml);
                        }
                    });
                }
            };
            
            window.clearForm = function() {
                window.isEditMode = false;
                jQuery('#userForm')[0].reset();
                jQuery('#user_id').val('');
                jQuery('#userModalLabel').text('Add User');
                jQuery('#password_required').removeClass('d-none');
                window.loadRoles();
                jQuery('#rolePermissions').hide();
                jQuery('#permissionsList').empty();
                jQuery('.invalid-feedback').text('');
                jQuery('.form-control, .form-select').removeClass('is-invalid');
            };
            
            window.displayErrors = function(errors) {
                jQuery('.form-control, .form-select').removeClass('is-invalid');
                jQuery('.invalid-feedback').text('');
                
                jQuery.each(errors, function(field, message) {
                    const input = jQuery(`#${field}`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(message);
                });
            };
            
            window.updateCsrfTokenFromResponse = function(token) {
                jQuery('#csrf_token').val(token);
            };
            
            window.showAlert = function(type, message) {
                // Remove existing alerts first
                jQuery('.alert').remove();
                
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Try to find content-card, otherwise use body
                const container = jQuery('.content-card').length ? jQuery('.content-card').parent() : jQuery('body');
                container.prepend(alertHtml);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    jQuery('.alert').fadeOut(function() {
                        jQuery(this).remove();
                    });
                }, 5000);
            };
        });
    }
    
    // Start initialization
    initUsersTable();
})();
</script>

