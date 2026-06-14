<?php
session_start();
include '../db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Debugging password verify
        if (password_verify($password, $user['password'])) {
            if (in_array($user['role'], ['admin', 'editor'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Access denied. You do not have permission to access the admin panel.";
            }
        } else {
            $error = "Invalid password. (Verify failed)";
        }
    } else {
        $error = "Admin not found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - News Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Admin Login</h3>
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="password123">
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div class="text-center mt-3">
            <small class="text-muted">Use: admin@example.com / password123</small>
        </div>
    </form>
</div>

</body>
</html>
