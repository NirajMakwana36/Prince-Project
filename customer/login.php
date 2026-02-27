<?php
ob_start();
$page_title = 'Welcome Back';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/db_config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

session_start();

if (isLoggedIn()) {
    if (isAdmin()) redirect(BASE_URL . 'admin/index.php');
    redirect(BASE_URL . 'customer/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $user = getUserByEmail($conn, $email);
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        
        if ($user['role'] === 'admin') redirect(BASE_URL . 'admin/index.php');
        if ($user['role'] === 'delivery') redirect(BASE_URL . 'delivery/index.php');
        redirect(BASE_URL . 'customer/dashboard.php');
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2ecc71; --secondary: #1e293b; --bg: #f8fafc; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; }
        
        .login-wrap { display: grid; grid-template-columns: 1.2fr 1fr; width: 1000px; height: 650px; background: white; border-radius: 2.5rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); }
        
        .side-visual { background: linear-gradient(135deg, var(--primary) 0%, #27ae60 100%); color: white; padding: 4rem; display: flex; flex-direction: column; justify-content: space-between; position: relative; }
        .side-visual::after { content: ''; position: absolute; bottom: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; }

        .login-form { padding: 4rem; display: flex; flex-direction: column; justify-content: center; }
        .logo { font-family: 'Outfit', sans-serif; font-size: 1.75rem; font-weight: 800; color: white; display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        
        h1 { font-family: 'Outfit', sans-serif; font-size: 2.5rem; margin-bottom: 2rem; color: var(--secondary); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #64748b; }
        .form-control { width: 100%; padding: 1rem 1.25rem; border: 1.5px solid #e2e8f0; border-radius: 1rem; transition: all 0.3s; font-family: inherit; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1); }
        
        .btn { width: 100%; padding: 1rem; background: var(--primary); color: white; border: none; border-radius: 1rem; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.3s; margin-top: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn:hover { background: #27ae60; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(46, 204, 113, 0.3); }

        .alert { background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; font-weight: 500; font-size: 0.9rem; border-left: 4px solid #ef4444; }

        @media (max-width: 1000px) {
            .login-wrap { width: 95%; grid-template-columns: 1fr; height: auto; }
            .side-visual { display: none; }
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="side-visual">
            <a href="<?php echo BASE_URL; ?>" class="logo"><i class="fas fa-shopping-basket"></i> CoGroCart</a>
            <div>
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.5rem; line-height: 1.2; margin-bottom: 1.5rem;">Freshness Is Just <br>A Click Away.</h2>
                <p style="opacity: 0.9; line-height: 1.6;">Join thousands of happy families getting their daily essentials delivered fresh every single day.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 1rem; flex: 1; text-align: center;">
                    <div style="font-weight: 800; font-size: 1.25rem;">50k+</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">Downloads</div>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 1rem; flex: 1; text-align: center;">
                    <div style="font-weight: 800; font-size: 1.25rem;">4.8/5</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">Rating</div>
                </div>
            </div>
        </div>
        <div class="login-form">
            <h1>Welcome Back</h1>
            
            <?php if($error): ?>
                <div class="alert"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="margin-bottom: 0;">Password</label>
                        <a href="#" style="font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600;">Forgot?</a>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn">Login to Account <i class="fas fa-arrow-right"></i></button>
            </form>

            <p style="margin-top: 2.5rem; text-align: center; color: #64748b; font-size: 0.9rem;">
                Don't have an account? <a href="register.php" style="color: var(--primary); text-decoration: none; font-weight: 700;">Create One</a>
            </p>
            
            <div style="margin-top: 3rem; padding: 1rem; background: #f8fafc; border-radius: 1rem; text-align: center; border: 1px dashed #e2e8f0;">
                <p style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.5rem; font-weight: 600;">DEMO ADMIN</p>
                <code style="font-size: 0.85rem; color: var(--secondary);">admin@grocart.com / admin123</code>
            </div>
        </div>
    </div>
</body>
</html>
