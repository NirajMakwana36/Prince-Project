<?php
$page_title = 'Admin Access';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/db_config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

session_start();

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header("Location: " . BASE_URL . "admin/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $user = getUserByEmail($conn, $email);
    if ($user && $user['role'] === 'admin' && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        header("Location: " . BASE_URL . "admin/index.php");
        exit;
    } else {
        $error = 'Invalid credentials or access denied.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; }
        .login-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); padding: 4rem; border-radius: 3rem; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); position: relative; z-index: 10; }
        .bg-blob { position: absolute; width: 500px; height: 500px; background: var(--primary); filter: blur(150px); opacity: 0.15; border-radius: 50%; z-index: 1; }
        .logo { font-family: 'Outfit'; font-size: 2.5rem; text-align: center; margin-bottom: 3rem; color: white; display: flex; align-items: center; justify-content: center; gap: 1rem; }
        .logo i { color: var(--primary); }
        .form-control { background: rgba(255,255,255,0.05) !important; border-color: rgba(255,255,255,0.1) !important; color: white !important; }
        .form-control:focus { border-color: var(--primary) !important; background: rgba(255,255,255,0.08) !important; }
        label { color: #94a3b8; font-weight: 600; margin-bottom: 0.75rem; display: block; }
    </style>
</head>
<body>
    <div class="bg-blob" style="top: -200px; left: -200px;"></div>
    <div class="bg-blob" style="bottom: -200px; right: -200px; background: #3b82f6;"></div>

    <div class="login-card">
        <div class="logo"><i class="fas fa-shopping-basket"></i> CoGroCart</div>
        
        <?php if($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #fca5a5; padding: 1.25rem; border-radius: 1rem; margin-bottom: 2rem; font-weight: 600;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Administrator Email</label>
                <input type="email" name="email" class="form-control form-control-lg" placeholder="admin@cogrocart.com" required>
            </div>
            <div class="form-group" style="margin-bottom: 3rem;">
                <label>Secret Password</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block" style="height: 60px; font-size: 1.1rem; justify-content: center;">
                Unlock Dashboard <i class="fas fa-key" style="margin-left: 0.75rem;"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 2.5rem;">
            <a href="<?php echo BASE_URL; ?>" style="color: #94a3b8; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Return to Site
            </a>
        </div>
    </div>
</body>
</html>
