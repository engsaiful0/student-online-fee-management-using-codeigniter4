-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 03:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `daud_ci4`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `phone`, `role_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'admin@example.com', '$2y$10$CQF6YyfXNQqlXah/9oGp5OiHK0gs0FGcSBW.Hc6qC79HvRrwfXeyS', '+1234567890', 2, 'active', '2025-11-04 16:10:19', '2025-11-05 00:23:50', NULL),
(2, 'John Admin', 'john.admin@example.com', '$2y$10$waqllqSr31fkRwQv/7QxlOd6svNbAG0doY5OF/UPbNsebbg4bBcZm', '+1234567891', NULL, 'active', '2025-11-04 16:10:19', '2025-11-04 16:10:19', NULL),
(3, 'Jane Manager', 'jane.manager@example.com', '$2y$10$mBdwSokYHo.yfxh5K2ddC.fjaFAxA4k//q7b.yjZFuWD/10Ocp3Uy', '+1234567892', NULL, 'active', '2025-11-04 16:10:20', '2025-11-04 16:10:20', NULL),
(4, 'xxyz', 'xxyz@gmail.com', '$2y$10$DZ7sytKuK4sUHs9.ZZZeweb0UI8pRIUEzNgLEFDuRcdzyFBD1JoZy', '01818650864', 2, 'active', '2025-11-05 00:07:46', '2025-11-05 00:07:46', NULL),
(5, 'Saiful Islam', 'saifuldev2011@gmail.com', '$2y$10$WX1IO7a286nBTieASoJ7wOJOwafD5jhZULbvvUIr.h6qq8fXmcgVW', '01818650864', 1, 'active', '2025-11-05 00:09:43', '2025-11-05 00:09:43', NULL),
(6, 'Saiful Islam', 'saifuldev201211@gmail.com', '$2y$10$olW3VlujmLak8GkJ8TyZaO1mRsW0h2/f9HK0jx9DEa7SgGyM8RDbi', '01818650864', 1, 'active', '2025-11-05 00:13:42', '2025-11-05 00:13:42', NULL),
(7, 'far', 'far@gmail.com', '$2y$10$W6hrqQF8.YjPgP4FmyjkLuAWdLi/UenZH/dboreWoag74.8Fpgvju', '01818650864', 2, 'active', '2025-11-05 00:23:36', '2025-11-05 00:23:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `code`, `name`, `start_year`, `end_year`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '101', 'Batch A', '2025', '2026', '', 'active', '2025-11-04 22:22:08', '2025-11-04 22:22:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `batch_semesters`
--

CREATE TABLE `batch_semesters` (
  `id` int(11) UNSIGNED NOT NULL,
  `batch_id` int(11) UNSIGNED NOT NULL,
  `semester_id` int(11) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive','completed') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch_semesters`
--

INSERT INTO `batch_semesters` (`id`, `batch_id`, `semester_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, '2025-11-01', '2025-11-30', 'active', '2025-11-04 22:26:40', '2025-11-04 22:26:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) UNSIGNED NOT NULL,
  `department_id` int(11) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `credit_hours` int(2) NOT NULL DEFAULT 3,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `department_id`, `code`, `name`, `description`, `credit_hours`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'CSE-2323', 'Digital Logic Design', 'Digital Logic Design', 3, 'active', '2025-11-04 22:30:57', '2025-11-04 22:31:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_offerings`
--

CREATE TABLE `course_offerings` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `batch_semester_id` int(11) UNSIGNED NOT NULL,
  `capacity` int(5) NOT NULL DEFAULT 30,
  `enrolled_count` int(5) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','full','completed') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_offerings`
--

INSERT INTO `course_offerings` (`id`, `course_id`, `batch_semester_id`, `capacity`, `enrolled_count`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 30, 11, 'active', '2025-11-04 22:35:56', '2025-11-04 23:25:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `code`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '12', 'CSE', 'fdfd', 'active', '2025-11-04 16:29:40', '2025-11-04 16:32:47', '2025-11-04 16:32:47'),
(2, '11', 'CSE', 'CSE', 'active', '2025-11-04 16:32:59', '2025-11-04 16:32:59', NULL),
(3, '13', 'Civil', 'CE', 'active', '2025-11-04 16:33:32', '2025-11-04 16:33:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` int(3) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `code`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Prof', 'Proffessor', '', 1, 'active', '2025-11-04 22:49:35', '2025-11-04 22:49:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) UNSIGNED NOT NULL,
  `student_id` int(11) UNSIGNED NOT NULL,
  `fee_type` enum('course_fee','tuition_fee','registration_fee','examination_fee','other') NOT NULL DEFAULT 'course_fee',
  `course_offering_id` int(11) UNSIGNED DEFAULT NULL,
  `fee_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `status` enum('pending','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `authorized_by` int(11) UNSIGNED DEFAULT NULL,
  `authorized_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `fee_type`, `course_offering_id`, `fee_title`, `description`, `amount`, `paid_amount`, `due_date`, `payment_date`, `payment_method`, `transaction_id`, `receipt_number`, `status`, `authorized_by`, `authorized_at`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'tuition_fee', NULL, 'Course Fee', 'Course Fee', 15000.00, 15000.00, '2025-11-05', '2025-11-05 00:47:53', 'cash', '5713655722', 'RCP-20251105-000001', 'paid', 1, '2025-11-05 00:48:18', '', '2025-11-05 00:46:30', '2025-11-05 00:48:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-11-04-160726', 'App\\Database\\Migrations\\CreateAdminsTable', 'default', 'App', 1762272594, 1),
(2, '2025-11-04-160738', 'App\\Database\\Migrations\\CreateStudentsTable', 'default', 'App', 1762272595, 1),
(3, '2025-11-04-161524', 'App\\Database\\Migrations\\CreateDepartmentsTable', 'default', 'App', 1762273249, 2),
(4, '2025-11-04-161530', 'App\\Database\\Migrations\\CreateProgramsTable', 'default', 'App', 1762273250, 2),
(5, '2025-11-04-161535', 'App\\Database\\Migrations\\CreateSemestersTable', 'default', 'App', 1762273250, 2),
(6, '2025-11-04-161600', 'App\\Database\\Migrations\\CreateBatchesTable', 'default', 'App', 1762294731, 3),
(7, '2025-11-04-161700', 'App\\Database\\Migrations\\CreateBatchSemestersTable', 'default', 'App', 1762295168, 4),
(8, '2025-11-04-161800', 'App\\Database\\Migrations\\CreateCoursesTable', 'default', 'App', 1762295414, 5),
(9, '2025-11-04-161900', 'App\\Database\\Migrations\\CreateCourseOfferingsTable', 'default', 'App', 1762295710, 6),
(10, '2025-11-04-162000', 'App\\Database\\Migrations\\CreateRolesTable', 'default', 'App', 1762295998, 7),
(11, '2025-11-04-162100', 'App\\Database\\Migrations\\CreateDesignationsTable', 'default', 'App', 1762296430, 8),
(12, '2025-11-04-162200', 'App\\Database\\Migrations\\AddFieldsToStudentsTable', 'default', 'App', 1762297003, 9),
(13, '2025-11-04-162300', 'App\\Database\\Migrations\\CreateStudentCourseEnrollmentsTable', 'default', 'App', 1762298115, 10),
(14, '2025-11-04-162400', 'App\\Database\\Migrations\\CreateTeachersTable', 'default', 'App', 1762299560, 11),
(15, '2025-11-04-162500', 'App\\Database\\Migrations\\AddRoleIdToAdminsTable', 'default', 'App', 1762301049, 12),
(16, '2025-11-04-162600', 'App\\Database\\Migrations\\CreateFeesTable', 'default', 'App', 1762303505, 13);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) UNSIGNED NOT NULL,
  `department_id` int(11) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_years` int(2) NOT NULL DEFAULT 4,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `department_id`, `code`, `name`, `description`, `duration_years`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, '111', 'Basic', 'fdfd', 4, 'active', '2025-11-04 16:59:33', '2025-11-04 16:59:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `permissions`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Adminstrator', 'adminstrator', 'Adminstrator', '[\"create\",\"view\",\"delete\"]', 'active', '2025-11-04 22:44:23', '2025-11-04 22:44:23', NULL),
(2, 'Shahriar Mahmud Arman', 'teacher', '', '[\"create\",\"view\",\"delete\"]', 'active', '2025-11-05 00:06:40', '2025-11-05 00:06:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) UNSIGNED NOT NULL,
  `program_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive','completed') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `program_id`, `name`, `code`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'First', '101', '2025-11-01', '2025-11-30', 'active', '2025-11-04 17:06:57', '2025-11-04 17:07:00', '2025-11-04 17:07:00'),
(2, 1, 'First', '101', '2025-11-01', '2025-11-30', 'active', '2025-11-04 17:07:20', '2025-11-04 17:07:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `batch_id` int(11) UNSIGNED DEFAULT NULL,
  `session` varchar(50) DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `program_id` int(11) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `student_id`, `batch_id`, `session`, `department_id`, `program_id`, `phone`, `address`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Alice Johnson', 'alice.student@example.com', '$2y$10$tIl34nWEScPco9Cv9clcVujQlHzrOxgHXL06CutMvFcO9TUeOd0/W', 'STU001', NULL, NULL, NULL, NULL, '+1987654321', '123 Main Street, City, State 12345', 'active', '2025-11-04 16:10:20', '2025-11-05 02:40:15', NULL),
(2, 'Bob Smith', 'bob.smith@example.com', '$2y$10$iyvSzo.wJKXGgVjg6y5HAOZ.tQZ4zxOCqzItO.r2sTAESZkSfNLXW', 'STU002', NULL, NULL, NULL, NULL, '+1987654322', '456 Oak Avenue, City, State 12345', 'active', '2025-11-04 16:10:20', '2025-11-04 16:10:20', NULL),
(3, 'Charlie Brown', 'charlie.brown@example.com', '$2y$10$MsXQHR8ovhHE7koOUQzHreUGSHTx5HFfRhPWH4SebK47i9ubnbIX2', 'STU003', NULL, NULL, NULL, NULL, '+1987654323', '789 Pine Road, City, State 12345', 'active', '2025-11-04 16:10:20', '2025-11-04 16:10:20', NULL),
(4, 'Diana Prince', 'diana.prince@example.com', '$2y$10$n1nTtTijoGaNYOmSKi7t2.C0nMxLExT2JcPMT6tOIlSAMw73QXu7K', 'STU004', NULL, NULL, NULL, NULL, '+1987654324', '321 Elm Street, City, State 12345', 'active', '2025-11-04 16:10:20', '2025-11-04 16:10:20', NULL),
(5, 'Edward Wilson', 'edward.wilson@example.com', '$2y$10$crfzM/z5kpW7q2FByVBNgukWtcxYtWGBK9SUzdTDuZFMMkxVdCDg6', 'STU005', NULL, NULL, NULL, NULL, '+1987654325', '654 Maple Drive, City, State 12345', 'active', '2025-11-04 16:10:20', '2025-11-04 16:10:20', NULL),
(7, 'Student 1 Name', 'stu006@student.example.com', '$2y$10$5yyIrMshfMFXg/t7WfaiCuV1oJvCcwOEWNYPfu.gOtHSA3Y49Cvxa', 'STU006', 1, '2024-2025', 1, 1, NULL, NULL, 'active', '2025-11-04 23:10:45', '2025-11-04 23:10:45', NULL),
(8, 'Saiful islam', 'stu007@student.example.com', '$2y$10$zncuB8XZay9Qxq5qt3hC.erIutwwx7yZShkvWLC4F0sK.62/OyOm.', 'STU007', 1, '2024-2025', 1, 1, NULL, NULL, 'active', '2025-11-04 23:10:45', '2025-11-04 23:10:45', NULL),
(9, 'ALif', 'alif@gmail.com', '$2y$10$i9oQ5T2wRoMn5Hkqm5vGAOVCsQ2QhZ/E4dog1rzZtXxF1InqGHbzO', 'STU008', 1, '2024-2025', 2, 1, '01818650864', 'Haji Mension,Hamjerbug\r\nMuradpur', 'active', '2025-11-04 23:11:40', '2025-11-04 23:11:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_course_enrollments`
--

CREATE TABLE `student_course_enrollments` (
  `id` int(11) UNSIGNED NOT NULL,
  `student_id` int(11) UNSIGNED NOT NULL,
  `course_offering_id` int(11) UNSIGNED NOT NULL,
  `teacher_id` int(11) UNSIGNED DEFAULT NULL,
  `status` enum('enrolled','dropped','completed') NOT NULL DEFAULT 'enrolled',
  `enrollment_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_course_enrollments`
--

INSERT INTO `student_course_enrollments` (`id`, `student_id`, `course_offering_id`, `teacher_id`, `status`, `enrollment_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, 1, NULL, 'enrolled', '2025-11-04', '2025-11-04 23:25:34', '2025-11-04 23:25:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) UNSIGNED NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `designation_id` int(11) UNSIGNED DEFAULT NULL,
  `qualification` varchar(200) DEFAULT NULL,
  `specialization` text DEFAULT NULL,
  `experience_years` int(3) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `employee_id`, `name`, `email`, `phone`, `department_id`, `designation_id`, `qualification`, `specialization`, `experience_years`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'xyz', 'xyz@gmail.com', '01818650864', 3, 1, 'PhD', 'PhD', 10, 'active', '2025-11-04 23:42:55', '2025-11-04 23:42:55', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `batch_semesters`
--
ALTER TABLE `batch_semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `batch_semester_id` (`batch_semester_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_authorized_by_foreign` (`authorized_by`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_offering_id` (`course_offering_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `student_course_enrollments`
--
ALTER TABLE `student_course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_offering_id` (`course_offering_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `designation_id` (`designation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `batch_semesters`
--
ALTER TABLE `batch_semesters`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_offerings`
--
ALTER TABLE `course_offerings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_course_enrollments`
--
ALTER TABLE `student_course_enrollments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batch_semesters`
--
ALTER TABLE `batch_semesters`
  ADD CONSTRAINT `batch_semesters_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `batch_semesters_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD CONSTRAINT `course_offerings_batch_semester_id_foreign` FOREIGN KEY (`batch_semester_id`) REFERENCES `batch_semesters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_offerings_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_authorized_by_foreign` FOREIGN KEY (`authorized_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `fees_course_offering_id_foreign` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_course_enrollments`
--
ALTER TABLE `student_course_enrollments`
  ADD CONSTRAINT `student_course_enrollments_course_offering_id_foreign` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_course_enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `teachers_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
