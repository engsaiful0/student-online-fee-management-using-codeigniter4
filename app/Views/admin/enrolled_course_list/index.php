<!-- jQuery and DataTables - Load First -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-list-check"></i> Enrolled Course List</h4>
    </div>

    <!-- Student Selection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <label for="student_select" class="form-label">Select Student <span class="text-danger">*</span></label>
            <select class="form-select" id="student_select" name="student_id">
                <option value="">-- Select Student --</option>
            </select>
        </div>
    </div>

    <!-- Student Info Card -->
    <div id="studentInfoCard" class="card mb-4" style="display: none;">
        <div class="card-body">
            <h5 class="card-title">Student Information</h5>
            <div class="row">
                <div class="col-md-3">
                    <strong>Name:</strong> <span id="student_name">-</span>
                </div>
                <div class="col-md-3">
                    <strong>Student ID:</strong> <span id="student_id_display">-</span>
                </div>
                <div class="col-md-3">
                    <strong>Batch:</strong> <span id="student_batch">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Courses Table -->
    <div id="coursesSection" style="display: none;">
        <h5 class="mb-3">Available Courses (Not Previously Enrolled)</h5>
        <div class="table-responsive">
            <table id="coursesTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Credit Hours</th>
                        <th>Batch</th>
                        <th>Session</th>
                        <th>Capacity</th>
                        <th>Enrolled</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="noStudentSelected" class="alert alert-info">
        <i class="bi bi-info-circle"></i> Please select a student from the dropdown above to view available courses.
    </div>
    
    <div id="noCoursesAvailable" class="alert alert-warning" style="display: none;">
        <i class="bi bi-exclamation-triangle"></i> No available courses found for this student. The student may have enrolled in all assigned courses or there are no course offerings for their batch.
    </div>
</div>

<script>
let coursesTable;
let selectedStudentId = null;

$(document).ready(function() {
    loadStudents();
    
    // Initialize DataTable
    coursesTable = $('#coursesTable').DataTable({
        processing: true,
        serverSide: false,
        data: [],
        columns: [
            { data: 'course_code' },
            { data: 'course_title' },
            { data: 'credit' },
            { data: 'batch' },
            { data: 'session' },
            { data: 'capacity' },
            { data: 'enrolled' },
            { 
                data: 'available',
                render: function(data, type, row) {
                    const colorClass = data > 0 ? 'success' : 'danger';
                    return `<span class="badge bg-${colorClass}">${data}</span>`;
                }
            }
        ],
        order: [[1, 'asc']]
    });
});

function loadStudents() {
    $.ajax({
        url: '<?= base_url('admin/enrolled-course-list/getStudents') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const select = $('#student_select');
            select.html('<option value="">-- Select Student --</option>');
            if (response.success && response.data && response.data.length > 0) {
                response.data.forEach(function(student) {
                    select.append(`<option value="${student.id}">${student.name} (${student.student_id || student.email})</option>`);
                });
            } else {
                select.append('<option value="" disabled>No students available</option>');
                showAlert('warning', 'No active students found. Please add students first.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading students:', error);
            showAlert('danger', 'Failed to load students. Please refresh the page.');
            $('#student_select').html('<option value="">Error loading students</option>');
        }
    });
}

$('#student_select').on('change', function() {
    const studentId = $(this).val();
    selectedStudentId = studentId;
    
    if (studentId) {
        $('#coursesSection').show();
        $('#noStudentSelected').hide();
        $('#noCoursesAvailable').hide();
        loadAvailableCourses(studentId);
    } else {
        $('#coursesSection').hide();
        $('#studentInfoCard').hide();
        $('#noStudentSelected').show();
        $('#noCoursesAvailable').hide();
        coursesTable.clear().draw();
    }
});

function loadAvailableCourses(studentId) {
    $.ajax({
        url: `<?= base_url('admin/enrolled-course-list/getAvailableCourses') ?>/${studentId}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            coursesTable.clear().draw();
            $('#studentInfoCard').hide();
        },
        success: function(response) {
            if (response.success) {
                // Update student info
                if (response.student) {
                    $('#student_name').text(response.student.name || '-');
                    $('#student_id_display').text(response.student.student_id || '-');
                    $('#student_batch').text(response.student.batch_id ? 'Batch #' + response.student.batch_id : '-');
                    $('#studentInfoCard').show();
                }
                
                // Load courses
                if (response.data && response.data.length > 0) {
                    coursesTable.clear().rows.add(response.data).draw();
                    $('#noCoursesAvailable').hide();
                } else {
                    coursesTable.clear().draw();
                    $('#coursesSection').hide();
                    $('#noCoursesAvailable').show();
                }
            } else {
                showAlert('danger', response.message || 'Failed to load courses');
                $('#coursesSection').hide();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading courses:', error, xhr.responseText);
            showAlert('danger', 'Failed to load available courses');
            $('#coursesSection').hide();
        }
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

