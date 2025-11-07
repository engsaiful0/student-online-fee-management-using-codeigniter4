<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - Bootstrap Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin: 5px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 3px solid #0d6efd;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.2rem;
            width: 20px;
        }
        
        .menu-group {
            margin-top: 5px;
            list-style: none;
        }
        
        .menu-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            user-select: none;
        }
        
        .menu-group-header:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .menu-group-header i.bi-chevron-down {
            transition: transform 0.3s;
            font-size: 0.9rem;
        }
        
        .menu-group-header.collapsed i.bi-chevron-down {
            transform: rotate(-90deg);
        }
        
        .sub-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .sub-menu.expanded {
            max-height: 1000px;
        }
        
        .sub-menu li a {
            padding-left: 40px;
            font-size: 0.95rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            padding: 15px 30px;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        .stat-card.info { border-left-color: #0dcaf0; }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .stat-icon.primary { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .stat-icon.success { background: rgba(25, 135, 84, 0.1); color: #198754; }
        .stat-icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .stat-icon.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .stat-icon.info { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .card-header-custom {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .card-title-custom {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    <script>
        function toggleSubMenu(header) {
            const subMenu = header.nextElementSibling;
            header.classList.toggle('collapsed');
            subMenu.classList.toggle('expanded');
        }
        
        // Auto-expand menu groups that contain active items
        document.addEventListener('DOMContentLoaded', function() {
            const activeLinks = document.querySelectorAll('.sidebar-menu a.active');
            activeLinks.forEach(function(activeLink) {
                const subMenu = activeLink.closest('.sub-menu');
                if (subMenu) {
                    subMenu.classList.add('expanded');
                    const header = subMenu.previousElementSibling;
                    if (header) {
                        header.classList.remove('collapsed');
                    }
                }
            });
        });
    </script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-speedometer2"></i> Dashboard</h4>
        </div>
        <ul class="sidebar-menu">
            <?php if (isset($page) && $page === 'admin'): ?>
                <!-- Dashboard -->
                <li><a href="<?= base_url('admin/dashboard') ?>" class="<?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>"><i class="bi bi-house-door"></i> Dashboard</a></li>
                
                <!-- Teachers -->
                <li><a href="<?= base_url('admin/teachers') ?>" class="<?= strpos(uri_string(), 'admin/teachers') !== false ? 'active' : '' ?>"><i class="bi bi-person-badge"></i> Teachers</a></li>
                
                <!-- Academic Setup -->
                <li class="menu-group">
                    <div class="menu-group-header collapsed" onclick="toggleSubMenu(this)">
                        <span><i class="bi bi-folder"></i> Academic Setup</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="sub-menu">
                <li><a href="<?= base_url('admin/departments') ?>" class="<?= strpos(uri_string(), 'admin/departments') !== false ? 'active' : '' ?>"><i class="bi bi-building"></i> Departments</a></li>
                <li><a href="<?= base_url('admin/programs') ?>" class="<?= strpos(uri_string(), 'admin/programs') !== false ? 'active' : '' ?>"><i class="bi bi-book"></i> Programs</a></li>
                <li><a href="<?= base_url('admin/semesters') ?>" class="<?= strpos(uri_string(), 'admin/semesters') !== false ? 'active' : '' ?>"><i class="bi bi-calendar3"></i> Semesters</a></li>
                <li><a href="<?= base_url('admin/program-semesters') ?>" class="<?= strpos(uri_string(), 'admin/program-semesters') !== false ? 'active' : '' ?>"><i class="bi bi-diagram-3"></i> Program Semester Assignments</a></li>
                        <li><a href="<?= base_url('admin/batches') ?>" class="<?= strpos(uri_string(), 'admin/batches') !== false ? 'active' : '' ?>"><i class="bi bi-collection"></i> Batches</a></li>
                        <li><a href="<?= base_url('admin/batch-semesters') ?>" class="<?= strpos(uri_string(), 'admin/batch-semesters') !== false ? 'active' : '' ?>"><i class="bi bi-diagram-2"></i> Batch Semester Assignments</a></li>
                    </ul>
                </li>
                
                <!-- Course Management -->
                <li class="menu-group">
                    <div class="menu-group-header collapsed" onclick="toggleSubMenu(this)">
                        <span><i class="bi bi-folder"></i> Course Management</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/courses') ?>" class="<?= strpos(uri_string(), 'admin/courses') !== false ? 'active' : '' ?>"><i class="bi bi-book-half"></i> Courses</a></li>
                        <li><a href="<?= base_url('admin/course-offerings') ?>" class="<?= strpos(uri_string(), 'admin/course-offerings') !== false ? 'active' : '' ?>"><i class="bi bi-journal-bookmark"></i> Course Offerings</a></li>
                    </ul>
                </li>
                
                <!-- Student Management -->
                <li class="menu-group">
                    <div class="menu-group-header collapsed" onclick="toggleSubMenu(this)">
                        <span><i class="bi bi-folder"></i> Student Management</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/students') ?>" class="<?= strpos(uri_string(), 'admin/students') !== false ? 'active' : '' ?>"><i class="bi bi-people"></i> Students</a></li>
                        <li><a href="<?= base_url('admin/student-course-enrollments') ?>" class="<?= strpos(uri_string(), 'admin/student-course-enrollments') !== false ? 'active' : '' ?>"><i class="bi bi-book-check"></i> Student Course Enrollments</a></li>
                        <li><a href="<?= base_url('admin/enrolled-course-list') ?>" class="<?= strpos(uri_string(), 'admin/enrolled-course-list') !== false ? 'active' : '' ?>"><i class="bi bi-list-check"></i> Enrolled Course List</a></li>
                    </ul>
                </li>
                
                <!-- User Management -->
                <li class="menu-group">
                    <div class="menu-group-header collapsed" onclick="toggleSubMenu(this)">
                        <span><i class="bi bi-folder"></i> User Management</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/roles') ?>" class="<?= strpos(uri_string(), 'admin/roles') !== false ? 'active' : '' ?>"><i class="bi bi-shield-check"></i> Roles</a></li>
                        <li><a href="<?= base_url('admin/designations') ?>" class="<?= strpos(uri_string(), 'admin/designations') !== false ? 'active' : '' ?>"><i class="bi bi-award"></i> Designations</a></li>
                        <li><a href="<?= base_url('admin/users') ?>" class="<?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>"><i class="bi bi-person-check"></i> Users</a></li>
                    </ul>
                </li>
                
                <!-- Fees Management -->
                <li><a href="<?= base_url('admin/fees') ?>" class="<?= strpos(uri_string(), 'admin/fees') !== false ? 'active' : '' ?>"><i class="bi bi-cash-coin"></i> Fees Received</a></li>
                
                <!-- Reports -->
                <li><a href="<?= base_url('admin/reports') ?>" class="<?= strpos(uri_string(), 'admin/reports') !== false ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
                
                <!-- System -->
                <li class="menu-group">
                    <div class="menu-group-header collapsed" onclick="toggleSubMenu(this)">
                        <span><i class="bi bi-folder"></i> System</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="sub-menu">
                <li><a href="#"><i class="bi bi-gear"></i> Settings</a></li>
                <li><a href="<?= base_url('student') ?>"><i class="bi bi-person-circle"></i> Student Panel</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="<?= base_url('student/dashboard') ?>" class="<?= strpos(uri_string(), 'student/dashboard') !== false ? 'active' : '' ?>"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li><a href="<?= base_url('student/fees') ?>" class="<?= strpos(uri_string(), 'student/fees') !== false ? 'active' : '' ?>"><i class="bi bi-credit-card"></i> Fees Payment</a></li>
                <li><a href="<?= base_url('student/reports') ?>" class="<?= strpos(uri_string(), 'student/reports') !== false ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
                <li><a href="<?= base_url('student/profile') ?>" class="<?= strpos(uri_string(), 'student/profile') !== false ? 'active' : '' ?>"><i class="bi bi-person-circle"></i> Profile</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h1 class="page-title"><?= esc($title ?? 'Dashboard') ?></h1>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary"><?= isset($page) && $page === 'admin' ? 'Admin' : 'Student' ?></span>
                
                <?php if (isset($page) && $page === 'admin'): ?>
                <!-- Admin Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="adminProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <span id="adminNameDisplay"><?= session()->get('admin_name') ?? 'Admin' ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminProfileDropdown">
                        <li><h6 class="dropdown-header" id="adminEmailDisplay"><?= session()->get('admin_email') ?? 'admin@example.com' ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            <i class="bi bi-key"></i> Reset Password
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a></li>
                    </ul>
                </div>
                <?php else: ?>
                <!-- Student Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="studentProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <span id="studentNameDisplay"><?= session()->get('student_name') ?? 'Student' ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="studentProfileDropdown">
                        <li><h6 class="dropdown-header" id="studentEmailDisplay"><?= session()->get('student_email') ?? 'student@example.com' ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#studentResetPasswordModal">
                            <i class="bi bi-key"></i> Reset Password
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </div>

    <!-- Reset Password Modal -->
    <?php if (isset($page) && $page === 'admin'): ?>
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="resetPasswordForm">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="password_csrf_token">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                            <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="updatePasswordBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- jQuery (required for AJAX and DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- DataTables JS - Load after jQuery -->
        <?php if (isset($content) && (strpos($content, 'usersTable') !== false || strpos($content, 'studentsTable') !== false || strpos($content, 'departmentsTable') !== false || strpos($content, 'programsTable') !== false || strpos($content, 'coursesTable') !== false || strpos($content, 'teachersTable') !== false || strpos($content, 'feesTable') !== false || strpos($content, 'offeringsTable') !== false || strpos($content, 'batchesTable') !== false || strpos($content, 'batchSemestersTable') !== false || strpos($content, 'enrollmentsTable') !== false || strpos($content, 'availableCoursesTable') !== false)): ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <?php endif; ?>
    
    <?php if (isset($page) && $page === 'admin'): ?>
    <script>
    // Load admin info on page load
    $(document).ready(function() {
        loadAdminInfo();
    });
    
    function loadAdminInfo() {
        $.ajax({
            url: '<?= base_url('admin/profile/getAdminInfo') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $('#adminNameDisplay').text(response.data.name || 'Admin');
                    $('#adminEmailDisplay').text(response.data.email || 'admin@example.com');
                }
            },
            error: function() {
                // Silent fail - keep default values
            }
        });
    }
    
    // Password reset form submission
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        
        // Client-side validation
        if (newPassword !== confirmPassword) {
            $('#confirm_password').addClass('is-invalid');
            $('#confirm_password').siblings('.invalid-feedback').text('Passwords do not match');
            return;
        }
        
        if (newPassword.length < 6) {
            $('#new_password').addClass('is-invalid');
            $('#new_password').siblings('.invalid-feedback').text('Password must be at least 6 characters long');
            return;
        }
        
        // Show spinner and disable button
        const updateBtn = $('#updatePasswordBtn');
        const spinner = updateBtn.find('.spinner-border');
        const btnText = updateBtn.find('span:not(.spinner-border)');
        const originalText = btnText.text();
        
        updateBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Updating...');
        
        // Clear previous errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url('admin/profile/updatePassword') ?>',
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
                    $('#resetPasswordModal').modal('hide');
                    $('#resetPasswordForm')[0].reset();
                    showAlert('success', response.message);
                    
                    if (response.csrf_token) {
                        $('#password_csrf_token').val(response.csrf_token);
                    }
                } else {
                    showAlert('danger', response.message || 'Failed to update password');
                    if (response.errors) {
                        displayPasswordErrors(response.errors);
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
                        displayPasswordErrors(xhr.responseJSON.errors);
                    }
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                updateBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text(originalText);
            }
        });
    });
    
    function displayPasswordErrors(errors) {
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
        }, 5000);
    }
    
    // Reset form when modal is closed
    $('#resetPasswordModal').on('hidden.bs.modal', function() {
        $('#resetPasswordForm')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
    </script>
    <?php endif; ?>
    
    <!-- Student Reset Password Modal -->
    <?php if (isset($page) && $page === 'student'): ?>
    <div class="modal fade" id="studentResetPasswordModal" tabindex="-1" aria-labelledby="studentResetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="studentResetPasswordModalLabel"><i class="bi bi-key"></i> Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="studentResetPasswordForm">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="student_password_csrf_token">
                        
                        <div class="mb-3">
                            <label for="student_new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="student_new_password" name="new_password" required minlength="6">
                            <small class="text-muted">Password must be at least 6 characters long.</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="student_confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="student_confirm_password" name="confirm_password" required minlength="6">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="studentUpdatePasswordBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // Student Password Reset Form Submission
        $('#studentResetPasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate passwords match
            const newPassword = $('#student_new_password').val();
            const confirmPassword = $('#student_confirm_password').val();
            
            if (newPassword !== confirmPassword) {
                $('#student_confirm_password').addClass('is-invalid');
                $('#student_confirm_password').siblings('.invalid-feedback').text('Passwords do not match');
                return;
            }
            
            if (newPassword.length < 6) {
                $('#student_new_password').addClass('is-invalid');
                $('#student_new_password').siblings('.invalid-feedback').text('Password must be at least 6 characters long');
                return;
            }
            
            // Show spinner and disable button
            const updateBtn = $('#studentUpdatePasswordBtn');
            const spinner = updateBtn.find('.spinner-border');
            const btnText = updateBtn.find('span:not(.spinner-border)');
            const originalText = btnText.text();
            
            updateBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            btnText.text('Updating...');
            
            // Clear previous errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '<?= base_url('student/updatePassword') ?>',
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
                        $('#studentResetPasswordModal').modal('hide');
                        $('#studentResetPasswordForm')[0].reset();
                        showStudentAlert('success', response.message);
                        
                        if (response.csrf_token) {
                            $('#student_password_csrf_token').val(response.csrf_token);
                        }
                    } else {
                        showStudentAlert('danger', response.message || 'Failed to update password');
                        if (response.errors) {
                            displayStudentPasswordErrors(response.errors);
                        }
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        if (xhr.responseJSON.errors) {
                            displayStudentPasswordErrors(xhr.responseJSON.errors);
                        }
                    } else if (xhr.status === 0) {
                        errorMessage = 'Network error. Please check your connection.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Access denied. Please refresh the page.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                    
                    showStudentAlert('danger', errorMessage);
                },
                complete: function() {
                    updateBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                    btnText.text(originalText);
                }
            });
        });
        
        // Reset form when modal is closed
        $('#studentResetPasswordModal').on('hidden.bs.modal', function() {
            $('#studentResetPasswordForm')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        });
    });
    
    function displayStudentPasswordErrors(errors) {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.each(errors, function(field, message) {
            const input = $(`#student_${field}`);
            if (input.length === 0) {
                input = $(`#${field}`);
            }
            input.addClass('is-invalid');
            input.siblings('.invalid-feedback').text(message);
        });
    }
    
    function showStudentAlert(type, message) {
        $('.alert').remove();
        
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
    <?php endif; ?>
</body>
</html>

