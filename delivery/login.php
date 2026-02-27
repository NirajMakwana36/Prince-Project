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
    <title>Delivery Partner Login - GroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff9f43 0%, #ff7b54 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo {
            font-size: 40px;
            color: #ff9f43;
            margin-bottom: 10px;
        }

        .login-header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #ff9f43;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: #ff8830;
            box-shadow: 0 4px 12px rgba(255, 159, 67, 0.3);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .info-box {
            background-color: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
        }

        .info-box p {
            margin-bottom: 5px;
            color: #0c5460;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-motorcycle"></i>
            </div>
            <h1>Delivery Partner</h1>
            <p>GroCart Delivery Platform</p>
        </div>

        <?php if ($error): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required />
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required />
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <div class="info-box">
            <p><strong>Note:</strong> This is the delivery partner login portal.</p>
            <p>Contact admin to register as a delivery partner.</p>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="<?php echo BASE_URL; ?>" style="color: #ff9f43; text-decoration: none; font-size: 14px;">
                <i class="fas fa-arrow-left"></i>
                Back to Store
            </a>
        </div>
    </div>
</body>
</html>
