<?php
session_start();
require 'conf.php';
$msg = "";

if (isset($_GET['verification'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE code = ?");
    $stmt->execute([$_GET['verification']]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET code = '' WHERE email = ?");
        $stmt->execute([$user['email']]);
        $msg = "<div class='w-full p-3 text-sm text-center text-green-400 bg-green-900/40 border border-green-500 rounded-lg shadow-md'>
        ✅ Your Account has been Verified
    </div>";
    } else {
        header("Location: index.php");
        exit();
    }
}

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if (empty($user['code'])) {
            $_SESSION['SESSION_EMAIL'] = $email;
            header('Location: welcome.php');
        } else {

            $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
            ❌Verify your email before logging in.
        </div>";
        }
    } else {

                    $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
            ❌Invalid email or password
             </div>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-gray-700">
    <div class="w-full max-w-md p-8 space-y-4 bg-gray-800 bg-opacity-90 backdrop-blur-md rounded-2xl shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-100 text-center">Login</h2>
        <?php
        echo $msg;
        ?>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300">Email</label>
                <input name="email" type="email" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-gray-300">Password</label>
                <input name="password" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button name="submit" type="submit" class="w-full py-2 text-lg font-semibold text-gray-100 bg-blue-600 rounded-lg hover:bg-blue-700">Login</button>
        </form>
        <p class="text-center text-gray-300 mt-2">
            <a href="forgetpassword.php" class="text-blue-400 hover:underline">Forgot password?</a>
        </p>
        <p class="text-center text-gray-300">
            <a href="reg.php" class="text-blue-400 hover:underline">Create an account</a>
        </p>
    </div>
</body>
</html>
