<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sign In') ?> - Bootstrap Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating > label {
            padding-left: 15px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .form-floating.position-relative .form-control {
            padding-left: 45px;
        }
        
        .badge-login {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .badge-login.admin {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .badge-login.student {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="bi bi-shield-lock"></i> <?= esc($login_type === 'admin' ? 'Admin' : 'Student') ?> Portal</h2>
                <p>Sign in to access your dashboard</p>
            </div>
            
            <div class="login-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <span class="badge-login <?= esc($login_type) ?>">
                    <i class="bi bi-<?= esc($login_type === 'admin' ? 'shield-check' : 'person-circle') ?>"></i> 
                    <?= esc(ucfirst($login_type)) ?> Access
                </span>
                
                <form action="<?= base_url('auth/processLogin') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="login_type" value="<?= esc($login_type) ?>">
                    
                    <div class="form-floating position-relative">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="Email Address" 
                               required 
                               autofocus>
                        <label for="email">Email Address</label>
                    </div>
                    
                    <div class="form-floating position-relative">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Password" 
                               required>
                        <label for="password">Password</label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>
                
                <div class="divider">
                    <span>or</span>
                </div>
                
                <div class="text-center">
                    <?php if ($login_type === 'admin'): ?>
                        <a href="<?= base_url('auth/studentLogin') ?>" class="text-decoration-none">
                            <i class="bi bi-person-circle"></i> Sign in as Student
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('auth/adminLogin') ?>" class="text-decoration-none">
                            <i class="bi bi-shield-check"></i> Sign in as Admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="login-footer">
                <small class="text-muted">
                    <a href="#">Forgot password?</a> | 
                    <a href="<?= base_url() ?>">Back to Home</a>
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

