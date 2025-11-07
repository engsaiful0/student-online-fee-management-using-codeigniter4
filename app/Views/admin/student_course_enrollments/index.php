<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        color: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stats-card.info {
        background: linear-gradient(135deg, #3494E6 0%, #EC6EAD 100%);
    }
    .stats-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stats-card .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .stats-card .stats-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 10px 0;
    }
    .stats-card .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .quick-enroll-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    .quick-enroll-card h5 {
        color: white;
        margin-bottom: 20px;
    }
    .quick-enroll-card .form-label {
        color: white;
        font-weight: 500;
    }
    .quick-enroll-card .form-select,
    .quick-enroll-card .form-control {
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 8px;
    }
    .course-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s;
        background: white;
    }
    .course-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }
    .course-card.full {
        opacity: 0.6;
        border-color: #dc3545;
    }
    .course-card .course-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }
    .course-card .course-code {
        font-size: 1.1rem;
        font-weight: bold;
        color: #667eea;
    }
    .course-card .course-title {
        font-size: 1rem;
        color: #495057;
        margin-top: 5px;
    }
    .course-card .course-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    .course-card .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .course-card .capacity-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    .progress-bar-custom {
        height: 10px;
        border-radius: 10px;
        flex: 1;
    }
    .enroll-btn {
        width: 100%;
        padding: 12px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .enroll-btn:hover {
        transform: scale(1.02);
    }
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .enrollment-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .student-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        border-radius: 20px;
        background: #f8f9fa;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .step.active {
        background: #667eea;
        color: white;
    }
    .step.completed {
        background: #28a745;
        color: white;
    }
    .step-arrow {
        color: #6c757d;
    }
    .available-courses-section {
        margin-top: 25px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>

<div class="content-card">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-book-check"></i> Student Course Enrollments</h4>
            <p class="text-muted mb-0">Quickly enroll students in courses with an intuitive interface</p>
        </div>
    </div>

    <!-- Quick Enrollment Form -->
    <div class="quick-enroll-card">
        <h5><i class="bi bi-lightning-charge"></i> Quick Enrollment</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="quick_student_select" class="form-label">Select Student <span class="text-white-50">*</span></label>
                <select class="form-select" id="quick_student_select" name="student_id">
                    <option value="">-- Choose Student --</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="quick_batch_filter" class="form-label">Filter by Batch</label>
                <select class="form-select" id="quick_batch_filter" name="batch_id">
                    <option value="">All Batches</option>
                    <?php if (!empty($batches)): ?>
                        <?php foreach ($batches as $batch): ?>
                            <option value="<?= $batch['id'] ?>"><?= esc($batch['name']) ?> (<?= esc($batch['code']) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="quick_student_search" class="form-label">Search Student</label>
                <input type="text" class="form-control" id="quick_student_search" placeholder="Type name, ID, or email...">
            </div>
        </div>
    </div>

    <!-- Step Indicator -->
    <div id="stepIndicator" class="step-indicator" style="display: none;">
        <div class="step completed">
            <i class="bi bi-check-circle"></i>
            <span>Student Selected</span>
        </div>
        <i class="bi bi-arrow-right step-arrow"></i>
        <div class="step active">
            <i class="bi bi-book"></i>
            <span>Choose Course</span>
        </div>
        <i class="bi bi-arrow-right step-arrow"></i>
        <div class="step">
            <i class="bi bi-check2"></i>
            <span>Enrolled</span>
        </div>
    </div>

    <!-- Selected Student Info -->
    <div id="selectedStudentInfo" class="student-info-card" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1" id="selectedStudentName">-</h5>
                <p class="mb-0" id="selectedStudentDetails">-</p>
            </div>
            <div>
                <button type="button" class="btn btn-light btn-sm" onclick="clearStudentSelection()">
                    <i class="bi bi-x-circle"></i> Change Student
        </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div id="statsSection" style="display: none;">
    <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Total Enrolled</div>
                            <div class="stats-value" id="statTotalEnrolled">0</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Total Credits</div>
                            <div class="stats-value" id="statTotalCredits">0</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-award"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Active Courses</div>
                            <div class="stats-value" id="statActive">0</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Completed</div>
                            <div class="stats-value" id="statCompleted">0</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Courses Section -->
    <div id="availableCoursesSection" class="available-courses-section" style="display: none;">
        <div class="section-header">
            <h5 class="mb-0"><i class="bi bi-grid"></i> Available Courses</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" id="courseSearch" placeholder="Search courses..." style="width: 250px;">
                <select class="form-select form-select-sm" id="courseFilter" style="width: 200px;">
                    <option value="">All Courses</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= esc($course['code']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </select>
            </div>
        </div>
        <div id="availableCoursesGrid" class="row">
            <!-- Course cards will be loaded here -->
        </div>
        <div id="noCoursesMessage" class="empty-state" style="display: none;">
            <i class="bi bi-inbox"></i>
            <h5>No Available Courses</h5>
            <p>All courses for this student's batch are already enrolled or no courses are available.</p>
        </div>
    </div>

    <!-- Enrolled Courses Section -->
    <div id="enrollmentsSection" style="display: none;">
        <div class="enrollment-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Currently Enrolled Courses</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshEnrollments()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        <div class="table-responsive">
            <table id="enrollmentsTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Credit</th>
                        <th>Batch</th>
                        <th>Session</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Enrollment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Empty State -->
    <div id="emptyState" class="empty-state">
        <i class="bi bi-person-plus"></i>
        <h5>Get Started with Enrollment</h5>
        <p>Select a student from the dropdown above to view available courses and enroll them.</p>
    </div>
</div>

<script>
let enrollmentsTable;
let selectedStudentId = null;
let studentData = null;
let availableCoursesData = [];

$(document).ready(function() {
    loadStudents();
    
    // Initialize enrollments table
    enrollmentsTable = $('#enrollmentsTable').DataTable({
        processing: true,
        serverSide: false,
        data: [],
        columns: [
            { data: 'id' },
            { data: 'course_code' },
            { data: 'course_title' },
            { data: 'credit' },
            { data: 'batch' },
            { data: 'session' },
            { data: 'teacher' },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'enrolled' ? 'success' : data === 'completed' ? 'info' : 'secondary';
                    return `<span class="badge bg-${badgeClass} badge-status">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'enrollment_date' },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.status === 'enrolled') {
                        return `
                            <button class="btn btn-sm btn-danger" onclick="unenrollStudent(${row.id})" title="Unenroll">
                                <i class="bi bi-x-circle"></i> Unenroll
                            </button>
                        `;
                    }
                    return '<span class="text-muted">-</span>';
                },
                orderable: false
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        language: {
            emptyTable: "No enrollments found for this student"
        }
    });

    // Student search with debounce
    let searchTimeout;
    $('#quick_student_search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadStudents();
        }, 500);
    });

    // Batch filter change
    $('#quick_batch_filter').on('change', function() {
        loadStudents();
    });

    // Course search and filter
    $('#courseSearch, #courseFilter').on('input change', function() {
        filterAvailableCourses();
    });
});

function loadStudents() {
    const batchId = $('#quick_batch_filter').val();
    const search = $('#quick_student_search').val();
    
    $.ajax({
        url: '<?= base_url('admin/student-course-enrollments/getStudents') ?>',
        type: 'GET',
        data: {
            batch_id: batchId,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            const select = $('#quick_student_select');
            select.html('<option value="">-- Choose Student --</option>');
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(student) {
                    select.append(`<option value="${student.id}" data-batch="${student.batch}">${student.name} (${student.student_id || student.email})</option>`);
                });
            } else {
                select.append('<option value="" disabled>No students found</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading students:', error);
            showAlert('danger', 'Failed to load students. Please refresh the page.');
        }
    });
}

$('#quick_student_select').on('change', function() {
    const studentId = $(this).val();
    selectedStudentId = studentId;
    
    if (studentId) {
        $('#emptyState').hide();
        $('#stepIndicator').show();
        $('#statsSection').show();
        $('#enrollmentsSection').show();
        $('#availableCoursesSection').show();
        
        // Get selected option data
        const selectedOption = $(this).find('option:selected');
        const studentName = selectedOption.text();
        const studentBatch = selectedOption.data('batch') || 'Not Assigned';
        
        // Update student info card
        $('#selectedStudentName').text(studentName);
        $('#selectedStudentDetails').html(`
            <i class="bi bi-people"></i> Batch: ${studentBatch}<br>
            <i class="bi bi-envelope"></i> ID: ${studentId}
        `);
        $('#selectedStudentInfo').show();
        
        // Get student data
        $.ajax({
            url: '<?= base_url('admin/student-course-enrollments/getStudents') ?>',
            type: 'GET',
            data: { search: studentId },
            dataType: 'json',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    studentData = response.data.find(s => s.id == studentId);
                }
            }
        });
        
        loadEnrollments(studentId);
        loadEnrollmentStats(studentId);
        loadAvailableCourses(studentId);
    } else {
        $('#emptyState').show();
        $('#stepIndicator').hide();
        $('#statsSection').hide();
        $('#enrollmentsSection').hide();
        $('#availableCoursesSection').hide();
        $('#selectedStudentInfo').hide();
        enrollmentsTable.clear().draw();
    }
});

function clearStudentSelection() {
    $('#quick_student_select').val('').trigger('change');
    $('#quick_student_search').val('');
    $('#quick_batch_filter').val('');
    loadStudents();
}

function refreshEnrollments() {
    if (selectedStudentId) {
        loadEnrollments(selectedStudentId);
        loadEnrollmentStats(selectedStudentId);
        loadAvailableCourses(selectedStudentId);
    }
}

function loadEnrollmentStats(studentId) {
    $.ajax({
        url: `<?= base_url('admin/student-course-enrollments/getEnrollmentStats') ?>/${studentId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                $('#statTotalEnrolled').text(response.data.total_enrolled);
                $('#statTotalCredits').text(response.data.total_credits);
                $('#statActive').text(response.data.active);
                $('#statCompleted').text(response.data.completed);
            }
        },
        error: function() {
            console.error('Error loading statistics');
        }
    });
}

function loadEnrollments(studentId) {
    $.ajax({
        url: `<?= base_url('admin/student-course-enrollments/getEnrollments') ?>/${studentId}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            enrollmentsTable.clear().draw();
        },
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                enrollmentsTable.clear().rows.add(response.data).draw();
            } else {
                enrollmentsTable.clear().draw();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading enrollments:', error);
            showAlert('danger', 'Failed to load enrollments: ' + (xhr.responseJSON?.message || error));
            enrollmentsTable.clear().draw();
        }
    });
}

function loadAvailableCourses(studentId) {
    $.ajax({
        url: `<?= base_url('admin/student-course-enrollments/getAvailableCourses') ?>/${studentId}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#availableCoursesGrid').html('<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        },
        success: function(response) {
            if (response.data && response.data.length > 0) {
                availableCoursesData = response.data;
                renderAvailableCourses(response.data);
                $('#noCoursesMessage').hide();
            } else {
                $('#availableCoursesGrid').html('');
                $('#noCoursesMessage').show();
            }
        },
        error: function() {
            showAlert('danger', 'Failed to load available courses');
            $('#availableCoursesGrid').html('');
        }
    });
}

function renderAvailableCourses(courses) {
    let html = '';
    courses.forEach(function(course) {
        const percentage = course.percentage || 0;
        const colorClass = percentage >= 100 ? 'danger' : percentage >= 80 ? 'warning' : 'success';
        const isFull = course.is_full;
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="course-card ${isFull ? 'full' : ''}">
                    <div class="course-header">
                        <div>
                            <div class="course-code">${course.course_code}</div>
                            <div class="course-title">${course.course_title}</div>
                        </div>
                        <span class="badge bg-${colorClass}">${course.credit} Credits</span>
                    </div>
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="bi bi-people"></i>
                            <span>${course.batch}</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-calendar"></i>
                            <span>${course.session}</span>
                        </div>
                    </div>
                    <div class="capacity-info">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Capacity</small>
                                <small class="text-muted"><strong>${course.enrolled}/${course.capacity}</strong></small>
                            </div>
                            <div class="progress progress-bar-custom">
                                <div class="progress-bar bg-${colorClass}" role="progressbar" 
                                     style="width: ${percentage}%" 
                                     aria-valuenow="${percentage}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    ${isFull ? 
                        '<button class="btn btn-secondary enroll-btn" disabled><i class="bi bi-x-circle"></i> Course Full</button>' :
                        `<button class="btn btn-primary enroll-btn" onclick="enrollInCourse(${course.id}, '${course.course_code}')">
                            <i class="bi bi-plus-circle"></i> Enroll Now
                        </button>`
                    }
                </div>
            </div>
        `;
    });
    $('#availableCoursesGrid').html(html);
}

function filterAvailableCourses() {
    const search = $('#courseSearch').val().toLowerCase();
    const filter = $('#courseFilter').val();
    
    let filtered = availableCoursesData;
    
    if (search) {
        filtered = filtered.filter(course => 
            course.course_code.toLowerCase().includes(search) ||
            course.course_title.toLowerCase().includes(search)
        );
    }
    
    if (filter) {
        filtered = filtered.filter(course => course.course_id == filter);
    }
    
    if (filtered.length > 0) {
        renderAvailableCourses(filtered);
        $('#noCoursesMessage').hide();
    } else {
        $('#availableCoursesGrid').html('');
        $('#noCoursesMessage').show();
    }
}

function enrollInCourse(courseOfferingId, courseCode) {
    if (!selectedStudentId) {
        showAlert('danger', 'Please select a student first');
        return;
    }

    if (!confirm(`Are you sure you want to enroll this student in ${courseCode}?`)) {
        return;
    }

    // Disable button and show loading
    const btn = $(`button[onclick*="${courseOfferingId}"]`);
    const originalHtml = btn.html();
    btn.prop('disabled', true);
    btn.html('<span class="spinner-border spinner-border-sm"></span> Enrolling...');

    $.ajax({
        url: '<?= base_url('admin/student-course-enrollments/enroll') ?>',
        type: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>',
            student_id: selectedStudentId,
            course_offering_id: courseOfferingId
        },
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadEnrollments(selectedStudentId);
                loadEnrollmentStats(selectedStudentId);
                loadAvailableCourses(selectedStudentId);
            } else {
                showAlert('danger', response.message || 'Failed to enroll student');
                btn.prop('disabled', false);
                btn.html(originalHtml);
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while enrolling.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert('danger', errorMessage);
            btn.prop('disabled', false);
            btn.html(originalHtml);
        }
    });
}

function unenrollStudent(enrollmentId) {
    if (!confirm('Are you sure you want to unenroll this student from the course?')) {
        return;
    }

    const unenrollBtn = $(`button[onclick="unenrollStudent(${enrollmentId})"]`);
    const originalHtml = unenrollBtn.html();
    unenrollBtn.prop('disabled', true);
    unenrollBtn.html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: `<?= base_url('admin/student-course-enrollments/unenroll') ?>/${enrollmentId}`,
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
                showAlert('success', response.message);
                loadEnrollments(selectedStudentId);
                loadEnrollmentStats(selectedStudentId);
                loadAvailableCourses(selectedStudentId);
            } else {
                showAlert('danger', response.message || 'Failed to unenroll student');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while unenrolling.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert('danger', errorMessage);
        },
        complete: function() {
            unenrollBtn.prop('disabled', false);
            unenrollBtn.html(originalHtml);
        }
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i> ${message}
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
