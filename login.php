<?php
$page_title = 'Welcome Back';
include_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) redirect(BASE_URL . 'admin/index.php');
    if (isDeliveryPartner()) redirect(BASE_URL . 'delivery/index.php');
    redirect(BASE_URL . 'customer/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $user = getUserByEmail($conn, $email);
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') redirect(BASE_URL . 'admin/index.php');
        if ($user['role'] === 'delivery') redirect(BASE_URL . 'delivery/index.php');
        redirect(BASE_URL . 'customer/dashboard.php');
    } else {
        $error = 'Invalid email or password provided.';
    }
}
?>

<div class="container animate-fade">
    <div style="max-width: 450px; margin: 6rem auto; background: white; border-radius: 2rem; padding: 4rem; border: 1px solid var(--border); box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 3rem;">
            <div style="width: 70px; height: 70px; background: var(--primary-light); color: var(--primary); border-radius: 1.5rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                <i class="fas fa-lock"></i>
            </div>
            <h1 style="font-family: 'Outfit'; font-size: 2rem;">Login <span style="color: var(--primary);">CoGro</span>Cart</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Access your premium grocery experience.</p>
        </div>

        <?php if($error): ?>
            <div style="background: #fee2e2; color: #ef4444; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; font-size: 0.85rem; font-weight: 600; text-align: center;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--secondary);">Email Address</label>
                <input type="email" name="email" required class="form-control" placeholder="name@example.com" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1px solid var(--border); font-family: inherit;">
            </div>

            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="font-weight: 700; font-size: 0.85rem; color: var(--secondary);">Password</label>
                    <a href="#" style="font-size: 0.75rem; color: var(--primary); text-decoration: none; font-weight: 700;">Forgot?</a>
                </div>
                <input type="password" name="password" required class="form-control" placeholder="••••••••" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1px solid var(--border); font-family: inherit;">
            </div>

            <button type="submit" name="login" class="btn btn-primary" style="width: 100%; height: 60px; justify-content: center; font-size: 1.1rem; border-radius: 1rem;">
                Sign In <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
            <p style="color: var(--text-muted); font-size: 0.9rem;">New to CoGroCart? <a href="<?php echo BASE_URL; ?>customer/register.php" style="color: var(--primary); font-weight: 800; text-decoration: none;">Join Now</a></p>
        </div>
    </div>
</div>

<style>
    .form-control:focus { border-color: var(--primary) !important; outline: none; box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1); }
</style>

<?php include_once 'includes/footer.php'; ?>
