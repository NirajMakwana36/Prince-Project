<?php
ob_start();
session_start();
$page_title = 'Create Account';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/db_config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

if (isLoggedIn()) redirect(BASE_URL . 'customer/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $phone = sanitize($_POST['phone']);
    
    if (getUserByEmail($conn, $email)) {
        $error = 'Email already registered';
    } else {
        $hashed = hashPassword($password);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->bind_param("ssss", $name, $email, $hashed, $phone);
        if ($stmt->execute()) {
            redirect(BASE_URL . 'customer/login.php', 'Registration successful! Please login.', 'success');
        } else {
            $error = 'Registration failed. Try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2ecc71; --secondary: #1e293b; --bg: #f8fafc; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; }
        
        .register-wrap { width: 100%; max-width: 500px; background: white; border-radius: 2.5rem; padding: 4rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1); }
        
        h1 { font-family: 'Outfit', sans-serif; font-size: 2.25rem; margin-bottom: 0.5rem; color: var(--secondary); text-align: center; }
        p.subtitle { text-align: center; color: #64748b; margin-bottom: 2.5rem; }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.85rem; color: #64748b; }
        .form-control { width: 100%; padding: 0.85rem 1.25rem; border: 1.5px solid #e2e8f0; border-radius: 1rem; transition: all 0.3s; font-family: inherit; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1); }
        
        .btn { width: 100%; padding: 1rem; background: var(--primary); color: white; border: none; border-radius: 1rem; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.3s; margin-top: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn:hover { background: #27ae60; transform: translateY(-2px); }

        .alert { background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; font-weight: 500; font-size: 0.9rem; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>
    <div class="register-wrap">
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="<?php echo BASE_URL; ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--primary); font-weight: 800; font-family: 'Outfit'; font-size: 1.5rem;">
                <i class="fas fa-shopping-basket"></i> CoGroCart
            </a>
        </div>
        <h1>Create Account</h1>
        <p class="subtitle">Join our community for the best grocery experience.</p>
        
        <?php if($error): ?>
            <div class="alert"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="+91 98765 43210" required>
            </div>
            <div class="form-group">
                <label>Create Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn">Started Shopping <i class="fas fa-rocket"></i></button>
        </form>

        <p style="margin-top: 2rem; text-align: center; color: #64748b; font-size: 0.9rem;">
            Already have an account? <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 700;">Login here</a>
        </p>
    </div>
</body>
</html>
