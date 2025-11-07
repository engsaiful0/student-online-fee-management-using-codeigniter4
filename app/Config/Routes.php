<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->group('auth', function($routes) {
    $routes->get('admin', 'Auth::adminLogin');
    $routes->get('student', 'Auth::studentLogin');
    $routes->post('processLogin', 'Auth::processLogin');
    $routes->get('logout', 'Auth::logout');
});

// Admin Panel Routes
$routes->group('admin', function($routes) {
    $routes->get('/', 'Auth::adminLogin'); // Redirect to login
    $routes->get('login', 'Auth::adminLogin');
    $routes->get('dashboard', 'Admin::dashboard');
    
    // Departments CRUD
    $routes->group('departments', function($routes) {
        $routes->get('/', 'Admin\Departments::index');
        $routes->get('getData', 'Admin\Departments::getData');
        $routes->post('create', 'Admin\Departments::create');
        $routes->get('edit/(:num)', 'Admin\Departments::edit/$1');
        $routes->post('update/(:num)', 'Admin\Departments::update/$1');
        $routes->post('delete/(:num)', 'Admin\Departments::delete/$1');
    });
    
    // Programs CRUD
    $routes->group('programs', function($routes) {
        $routes->get('/', 'Admin\Programs::index');
        $routes->get('getData', 'Admin\Programs::getData');
        $routes->get('getDepartments', 'Admin\Programs::getDepartments');
        $routes->post('create', 'Admin\Programs::create');
        $routes->get('edit/(:num)', 'Admin\Programs::edit/$1');
        $routes->post('update/(:num)', 'Admin\Programs::update/$1');
        $routes->post('delete/(:num)', 'Admin\Programs::delete/$1');
    });
    
    // Semesters CRUD
    $routes->group('semesters', function($routes) {
        $routes->get('/', 'Admin\Semesters::index');
        $routes->get('getData', 'Admin\Semesters::getData');
        $routes->get('getPrograms', 'Admin\Semesters::getPrograms');
        $routes->post('create', 'Admin\Semesters::create');
        $routes->get('edit/(:num)', 'Admin\Semesters::edit/$1');
        $routes->post('update/(:num)', 'Admin\Semesters::update/$1');
        $routes->post('delete/(:num)', 'Admin\Semesters::delete/$1');
    });
    
    // Program Semester Assignments CRUD
    $routes->group('program-semesters', function($routes) {
        $routes->get('/', 'Admin\ProgramSemesters::index');
        $routes->get('getData', 'Admin\ProgramSemesters::getData');
        $routes->get('getPrograms', 'Admin\ProgramSemesters::getPrograms');
        $routes->post('create', 'Admin\ProgramSemesters::create');
        $routes->get('edit/(:num)', 'Admin\ProgramSemesters::edit/$1');
        $routes->post('update/(:num)', 'Admin\ProgramSemesters::update/$1');
        $routes->post('delete/(:num)', 'Admin\ProgramSemesters::delete/$1');
    });
    
    // Batches CRUD
    $routes->group('batches', function($routes) {
        $routes->get('/', 'Admin\Batches::index');
        $routes->get('getData', 'Admin\Batches::getData');
        $routes->post('create', 'Admin\Batches::create');
        $routes->get('edit/(:num)', 'Admin\Batches::edit/$1');
        $routes->post('update/(:num)', 'Admin\Batches::update/$1');
        $routes->post('delete/(:num)', 'Admin\Batches::delete/$1');
    });
    
    // Batch Semester Assignments CRUD
    $routes->group('batch-semesters', function($routes) {
        $routes->get('/', 'Admin\BatchSemesters::index');
        $routes->get('getData', 'Admin\BatchSemesters::getData');
        $routes->get('getBatches', 'Admin\BatchSemesters::getBatches');
        $routes->get('getSemesters', 'Admin\BatchSemesters::getSemesters');
        $routes->post('create', 'Admin\BatchSemesters::create');
        $routes->get('edit/(:num)', 'Admin\BatchSemesters::edit/$1');
        $routes->post('update/(:num)', 'Admin\BatchSemesters::update/$1');
        $routes->post('delete/(:num)', 'Admin\BatchSemesters::delete/$1');
    });
    
    // Courses CRUD
    $routes->group('courses', function($routes) {
        $routes->get('/', 'Admin\Courses::index');
        $routes->get('getData', 'Admin\Courses::getData');
        $routes->get('getDepartments', 'Admin\Courses::getDepartments');
        $routes->post('create', 'Admin\Courses::create');
        $routes->get('edit/(:num)', 'Admin\Courses::edit/$1');
        $routes->post('update/(:num)', 'Admin\Courses::update/$1');
        $routes->post('delete/(:num)', 'Admin\Courses::delete/$1');
    });
    
    // Course Offerings CRUD
    $routes->group('course-offerings', function($routes) {
        $routes->get('/', 'Admin\CourseOfferings::index');
        $routes->get('getData', 'Admin\CourseOfferings::getData');
        $routes->get('getCourses', 'Admin\CourseOfferings::getCourses');
        $routes->get('getBatchSemesters', 'Admin\CourseOfferings::getBatchSemesters');
        $routes->post('create', 'Admin\CourseOfferings::create');
        $routes->get('edit/(:num)', 'Admin\CourseOfferings::edit/$1');
        $routes->post('update/(:num)', 'Admin\CourseOfferings::update/$1');
        $routes->post('delete/(:num)', 'Admin\CourseOfferings::delete/$1');
    });
    
    // Roles CRUD
    $routes->group('roles', function($routes) {
        $routes->get('/', 'Admin\Roles::index');
        $routes->get('getData', 'Admin\Roles::getData');
        $routes->post('create', 'Admin\Roles::create');
        $routes->get('edit/(:num)', 'Admin\Roles::edit/$1');
        $routes->post('update/(:num)', 'Admin\Roles::update/$1');
        $routes->post('delete/(:num)', 'Admin\Roles::delete/$1');
    });
    
    // Designations CRUD
    $routes->group('designations', function($routes) {
        $routes->get('/', 'Admin\Designations::index');
        $routes->get('getData', 'Admin\Designations::getData');
        $routes->post('create', 'Admin\Designations::create');
        $routes->get('edit/(:num)', 'Admin\Designations::edit/$1');
        $routes->post('update/(:num)', 'Admin\Designations::update/$1');
        $routes->post('delete/(:num)', 'Admin\Designations::delete/$1');
    });
    
    // Students CRUD
    $routes->group('students', function($routes) {
        $routes->get('/', 'Admin\Students::index');
        $routes->get('getData', 'Admin\Students::getData');
        $routes->get('getBatches', 'Admin\Students::getBatches');
        $routes->get('getDepartments', 'Admin\Students::getDepartments');
        $routes->get('getPrograms', 'Admin\Students::getPrograms');
        $routes->get('downloadTemplate', 'Admin\Students::downloadTemplate');
        $routes->post('create', 'Admin\Students::create');
        $routes->post('uploadCsv', 'Admin\Students::uploadCsv');
        $routes->get('edit/(:num)', 'Admin\Students::edit/$1');
        $routes->post('update/(:num)', 'Admin\Students::update/$1');
        $routes->post('delete/(:num)', 'Admin\Students::delete/$1');
    });
    
    // Student Course Enrollments
    $routes->group('student-course-enrollments', function($routes) {
        $routes->get('/', 'Admin\StudentCourseEnrollments::index');
        $routes->get('getStudents', 'Admin\StudentCourseEnrollments::getStudents');
        $routes->get('getEnrollments/(:num)', 'Admin\StudentCourseEnrollments::getEnrollments/$1');
        $routes->get('getAvailableCourses/(:num)', 'Admin\StudentCourseEnrollments::getAvailableCourses/$1');
        $routes->get('getEnrollmentStats/(:num)', 'Admin\StudentCourseEnrollments::getEnrollmentStats/$1');
        $routes->post('enroll', 'Admin\StudentCourseEnrollments::enroll');
        $routes->post('bulkEnroll', 'Admin\StudentCourseEnrollments::bulkEnroll');
        $routes->post('unenroll/(:num)', 'Admin\StudentCourseEnrollments::unenroll/$1');
    });
    
    // Enrolled Course List
    $routes->group('enrolled-course-list', function($routes) {
        $routes->get('/', 'Admin\EnrolledCourseList::index');
        $routes->get('getStudents', 'Admin\EnrolledCourseList::getStudents');
        $routes->get('getAvailableCourses/(:num)', 'Admin\EnrolledCourseList::getAvailableCourses/$1');
    });
    
    // Teachers CRUD
    $routes->group('teachers', function($routes) {
        $routes->get('/', 'Admin\Teachers::index');
        $routes->get('getData', 'Admin\Teachers::getData');
        $routes->get('getDepartments', 'Admin\Teachers::getDepartments');
        $routes->get('getDesignations', 'Admin\Teachers::getDesignations');
        $routes->post('create', 'Admin\Teachers::create');
        $routes->get('edit/(:num)', 'Admin\Teachers::edit/$1');
        $routes->post('update/(:num)', 'Admin\Teachers::update/$1');
        $routes->post('delete/(:num)', 'Admin\Teachers::delete/$1');
    });
    
    // Admin Profile
    $routes->group('profile', function($routes) {
        $routes->get('getAdminInfo', 'Admin\Profile::getAdminInfo');
        $routes->post('updatePassword', 'Admin\Profile::updatePassword');
    });
    
    // Users CRUD
    $routes->group('users', function($routes) {
        $routes->get('/', 'Admin\Users::index');
        $routes->get('getData', 'Admin\Users::getData');
        $routes->get('getRoles', 'Admin\Users::getRoles');
        $routes->get('getRolePermissions/(:num)', 'Admin\Users::getRolePermissions/$1');
        $routes->post('create', 'Admin\Users::create');
        $routes->get('edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('update/(:num)', 'Admin\Users::update/$1');
        $routes->post('delete/(:num)', 'Admin\Users::delete/$1');
    });
    
    // Fees Management
    $routes->group('fees', function($routes) {
        $routes->get('/', 'Admin\Fees::index');
        $routes->get('getData', 'Admin\Fees::getData');
        $routes->get('getPendingFees', 'Admin\Fees::getPendingFees');
        $routes->post('authorizePayment/(:num)', 'Admin\Fees::authorizePayment/$1');
        $routes->post('rejectPayment/(:num)', 'Admin\Fees::rejectPayment/$1');
    });
    
    // Reports
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Admin\Reports::index');
        $routes->get('department-list/(:any)', 'Admin\Reports::departmentList/$1');
        $routes->get('department-list', 'Admin\Reports::departmentList');
        $routes->get('teacher-list/(:any)', 'Admin\Reports::teacherList/$1');
        $routes->get('teacher-list', 'Admin\Reports::teacherList');
        $routes->get('student-payment-status/(:any)', 'Admin\Reports::studentPaymentStatus/$1');
        $routes->get('student-payment-status', 'Admin\Reports::studentPaymentStatus');
        $routes->get('student-payment-report/(:any)', 'Admin\Reports::studentPaymentReport/$1');
        $routes->get('student-payment-report', 'Admin\Reports::studentPaymentReport');
    });
});

// Student Panel Routes
$routes->group('student', function($routes) {
    $routes->get('/', 'Auth::studentLogin'); // Redirect to login
    $routes->get('login', 'Auth::studentLogin');
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('profile', 'Student::profile');
    $routes->post('updatePassword', 'Student::updatePassword');
    
    // Student Fees
    $routes->group('fees', function($routes) {
        $routes->get('/', 'Student\Fees::index');
        $routes->post('makePayment', 'Student\Fees::makePayment');
        $routes->post('createFeeRequest', 'Student\Fees::createFeeRequest');
    });
    
    // Student Reports
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Student\Reports::index');
        $routes->get('department-list/(:any)', 'Student\Reports::departmentList/$1');
        $routes->get('department-list', 'Student\Reports::departmentList');
        $routes->get('teacher-list/(:any)', 'Student\Reports::teacherList/$1');
        $routes->get('teacher-list', 'Student\Reports::teacherList');
        $routes->get('student-payment-status/(:any)', 'Student\Reports::studentPaymentStatus/$1');
        $routes->get('student-payment-status', 'Student\Reports::studentPaymentStatus');
        $routes->get('student-payment-report/(:any)', 'Student\Reports::studentPaymentReport/$1');
        $routes->get('student-payment-report', 'Student\Reports::studentPaymentReport');
    });
});
