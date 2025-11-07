<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .landing-container {
            max-width: 1200px;
            width: 100%;
        }
        
        .landing-header {
            text-align: center;
            color: white;
            margin-bottom: 60px;
        }
        
        .landing-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .landing-header p {
            font-size: 1.3rem;
            opacity: 0.95;
        }
        
        .login-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        
        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }
        
        .login-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 3rem;
        }
        
        .login-card.admin .login-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .login-card.student .login-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .login-card h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .login-card p {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .btn-login {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-admin:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-student {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn-student:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(240, 147, 251, 0.4);
            color: white;
        }
        
        .features {
            margin-top: 60px;
            text-align: center;
            color: white;
        }
        
        .features h3 {
            font-size: 2rem;
            margin-bottom: 40px;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .feature-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .feature-item i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .feature-item h4 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .landing-header h1 {
                font-size: 2.5rem;
            }
            
            .login-cards {
                grid-template-columns: 1fr;
            }
            
            .login-card {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <!-- Header -->
        <div class="landing-header">
            <h1><i class="bi bi-mortarboard"></i> Student Management System</h1>
            <p>Comprehensive Academic Management Solution</p>
        </div>
        
        <!-- Login Cards -->
        <div class="login-cards">
            <!-- Admin Login Card -->
            <div class="login-card admin">
                <div class="login-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h2>Admin Login</h2>
                <p>Access the administrative panel to manage students, courses, teachers, and system settings.</p>
                <a href="<?= base_url('auth/admin') ?>" class="btn btn-login btn-admin">
                    <i class="bi bi-box-arrow-in-right"></i> Admin Login
                </a>
            </div>
            
            <!-- Student Login Card -->
            <div class="login-card student">
                <div class="login-icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h2>Student Login</h2>
                <p>Access your student portal to view courses, assignments, grades, and academic information.</p>
                <a href="<?= base_url('auth/student') ?>" class="btn btn-login btn-student">
                    <i class="bi bi-box-arrow-in-right"></i> Student Login
                </a>
            </div>
        </div>
        
        <!-- Features -->
        <div class="features">
            <h3>System Features</h3>
            <div class="feature-grid">
                <div class="feature-item">
                    <i class="bi bi-people"></i>
                    <h4>Student Management</h4>
                    <p>Manage student records, enrollments, and academic progress</p>
                </div>
                <div class="feature-item">
                    <i class="bi bi-book-half"></i>
                    <h4>Course Management</h4>
                    <p>Create and manage courses, offerings, and schedules</p>
                </div>
                <div class="feature-item">
                    <i class="bi bi-person-badge"></i>
                    <h4>Teacher Portal</h4>
                    <p>Manage faculty information and assignments</p>
                </div>
                <div class="feature-item">
                    <i class="bi bi-graph-up"></i>
                    <h4>Academic Tracking</h4>
                    <p>Track academic performance and generate reports</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

