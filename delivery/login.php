<?php
$page_title = 'Delivery Partner Login';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/db_config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'delivery') {
    header("Location: " . BASE_URL . "delivery/index.php");
    exit;
}

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required!';
    } else {
        $user = getUserByEmail($conn, $email);
        
        if ($user && $user['role'] === 'delivery' && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            header("Location: " . BASE_URL . "delivery/index.php");
            exit;
        } else {
            $error = 'Invalid credentials or not a delivery partner!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Gateway - CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; margin: 0; }
        
        .bg-blob { position: absolute; width: 600px; height: 600px; background: #fbbf24; filter: blur(180px); opacity: 0.1; border-radius: 50%; z-index: 1; }
        
        .login-card { 
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(25px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
            padding: 4.5rem; 
            border-radius: 3.5rem; 
            width: 100%; 
            max-width: 480px; 
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.6); 
            position: relative; 
            z-index: 10; 
        }

        .partner-badge { 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            background: rgba(251, 191, 36, 0.1); 
            color: #fbbf24; 
            padding: 0.75rem 1.5rem; 
            border-radius: 2rem; 
            font-size: 0.85rem; 
            font-weight: 700; 
            margin-bottom: 2.5rem; 
            border: 1px solid rgba(251, 191, 36, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logo { font-family: 'Outfit'; font-size: 2.5rem; margin-bottom: 1rem; color: white; }
        .logo i { color: #fbbf24; margin-right: 0.5rem; }
        
        h2 { color: white; font-size: 2rem; margin-bottom: 3rem; font-family: 'Outfit'; }
        
        .form-group { margin-bottom: 2rem; }
        .form-group label { display: block; color: #94a3b8; font-weight: 600; margin-bottom: 0.75rem; font-size: 0.9rem; }
        
        .form-control { 
            background: rgba(255,255,255,0.05) !important; 
            border: 1px solid rgba(255,255,255,0.1) !important; 
            color: white !important; 
            height: 60px; 
            border-radius: 1.25rem !important; 
            padding: 0 1.5rem !important;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
        }
        
        .form-control:focus { 
            border-color: #fbbf24 !important; 
            background: rgba(255,255,255,0.08) !important; 
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1) !important; 
        }

        .btn-partner { 
            background: #fbbf24; 
            color: #111; 
            height: 65px; 
            border-radius: 1.5rem; 
            font-weight: 800; 
            font-size: 1.1rem; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 0.75rem; 
            transition: all 0.3s; 
            border: none;
            width: 100%;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.4);
        }
        
        .btn-partner:hover { 
            transform: translateY(-3px); 
            background: #f59e0b; 
            box-shadow: 0 15px 35px -8px rgba(251, 191, 36, 0.5); 
        }

        .alert-error { 
            background: rgba(239, 68, 68, 0.1); 
            border: 1px solid rgba(239, 68, 68, 0.2); 
            color: #fca5a5; 
            padding: 1.25rem; 
            border-radius: 1.25rem; 
            margin-bottom: 2rem; 
            font-size: 0.9rem; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }

        .back-link { 
            display: block; 
            text-align: center; 
            margin-top: 2.5rem; 
            color: #64748b; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem; 
            transition: 0.3s;
        }
        .back-link:hover { color: white; }
    </style>
</head>
<body>
    <div class="bg-blob" style="top: -200px; left: -200px;"></div>
    <div class="bg-blob" style="bottom: -200px; right: -200px; background: #3b82f6; opacity: 0.05;"></div>

    <div class="login-card">
        <div class="partner-badge">
            <i class="fas fa-shipping-fast"></i>
            Logistic Network
        </div>
        
        <div class="logo"><i class="fas fa-shopping-basket"></i> CoGroCart</div>
        <h2>Partner Login</h2>

        <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Credentials</label>
                <input type="email" name="email" class="form-control" placeholder="partner@cogrocart.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <label>Security Key</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-partner">
                Access Dashboard <i class="fas fa-lock-open"></i>
            </button>
        </form>

        <a href="<?php echo BASE_URL; ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Return to Site
        </a>
    </div>
</body>
</html>
