<div class="content-card">
    <h4 class="mb-4"><i class="bi bi-file-earmark-text"></i> Reports</h4>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-building"></i> Department List</h5>
                    <p class="card-text">View and export a complete list of all departments.</p>
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('student/reports/department-list') ?>" class="btn btn-primary">View</a>
                        <a href="<?= base_url('student/reports/department-list/pdf') ?>" class="btn btn-danger">Export PDF</a>
                        <a href="<?= base_url('student/reports/department-list/excel') ?>" class="btn btn-success">Export Excel</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-person-badge"></i> Teacher List</h5>
                    <p class="card-text">View and export a complete list of all teachers with their details.</p>
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('student/reports/teacher-list') ?>" class="btn btn-primary">View</a>
                        <a href="<?= base_url('student/reports/teacher-list/pdf') ?>" class="btn btn-danger">Export PDF</a>
                        <a href="<?= base_url('student/reports/teacher-list/excel') ?>" class="btn btn-success">Export Excel</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-credit-card"></i> My Payment Status</h5>
                    <p class="card-text">View and export your payment status summary.</p>
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('student/reports/student-payment-status') ?>" class="btn btn-primary">View</a>
                        <a href="<?= base_url('student/reports/student-payment-status/pdf') ?>" class="btn btn-danger">Export PDF</a>
                        <a href="<?= base_url('student/reports/student-payment-status/excel') ?>" class="btn btn-success">Export Excel</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-receipt"></i> My Payment Report</h5>
                    <p class="card-text">View and export detailed payment information for your fees.</p>
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('student/reports/student-payment-report') ?>" class="btn btn-primary">View</a>
                        <a href="<?= base_url('student/reports/student-payment-report/pdf') ?>" class="btn btn-danger">Export PDF</a>
                        <a href="<?= base_url('student/reports/student-payment-report/excel') ?>" class="btn btn-success">Export Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

