<?php
session_start();
require 'conf.php';

$msg = "";


if (isset($_GET['code'])) {
    $code = $_GET['code'];


    $stmt = $pdo->prepare("SELECT * FROM users WHERE code = ?");
    $stmt->execute([$code]);
    $user = $stmt->fetch();

    if (!$user) {

        die("Invalid or expired reset code. Please request a new password reset.");
    }
} else {

    die("No reset code provided. Please request a new password reset.");
}

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {

        $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
        ❌Passwords do not match
         </div>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ?, code = NULL WHERE email = ?");
        $stmt->execute([$hashedPassword, $user['email']]);

        
        $msg = "<div class='w-full p-3 text-sm text-center text-green-400 bg-green-900/40 border border-green-500 rounded-lg shadow-md'>
        ✅ Your password has been successfully reset
    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-gray-700">
    <div class="w-full max-w-md p-8 space-y-4 bg-gray-800 bg-opacity-90 backdrop-blur-md rounded-2xl shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-100 text-center">Reset Password</h2>
        <?php echo $msg; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-300">New Password</label>
                <input name="password" type="password" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-gray-300">Confirm Password</label>
                <input name="confirm_password" type="password" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button name="submit" type="submit" class="w-full py-2 text-lg font-semibold text-gray-100 bg-blue-600 rounded-lg hover:bg-blue-700">Reset Password</button>
        </form>
        <p class="text-center text-gray-300 mt-2">
            <a href="index.php" class="text-blue-400 hover:underline">Back to login</a>
        </p>
    </div>
</body>
</html>
