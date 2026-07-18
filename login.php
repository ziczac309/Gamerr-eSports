<?php
// login.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Check Admin
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin/dashboard.php");
        exit;
    }

    // 2. Check Staff
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ?");
    $stmt->execute([$username]);
    $staff = $stmt->fetch();
    if ($staff && password_verify($password, $staff['password'])) {
        $_SESSION['staff_id'] = $staff['id'];
        header("Location: staff/dashboard.php");
        exit;
    }

    // 3. Check User
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    }
    
    $error = "Invalid credentials!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GAMER ESPORTS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { user-select: none; -webkit-user-select: none; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4" oncontextmenu="return false;">
    <div class="w-full max-w-md bg-gray-800 rounded-[25px] shadow-lg p-8">
        <h1 class="text-3xl font-bold text-center mb-6 text-purple-400">GAMER ESPORTS</h1>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-6">
                <label class="block text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <button type="submit" name="login" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 py-3 rounded-xl font-bold text-lg shadow-lg">Login</button>
        </form>
    </div>
</body>
</html>
