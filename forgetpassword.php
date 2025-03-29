<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'conf.php';
$msg = "";

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $code = bin2hex(random_bytes(16));


    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {

        $stmt = $pdo->prepare("UPDATE users SET code = ? WHERE email = ?");
        $stmt->execute([$code, $email]);


        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'e7ifubbb@gmail.com';
            $mail->Password = 'vezs nqwg luee nvhg'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('e7ifubbb@gmail.com', 'YourAppName');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body = 'Click <a href="http://localhost:3000/chpass.php?code=' . $code . '">here</a> to reset your password.';
            $mail->send();

     
                    
        $msg = "<div class='w-full p-3 text-sm text-center text-green-400 bg-green-900/40 border border-green-500 rounded-lg shadow-md'>
        ✅ Reset link sent to your email
    </div>";
            
        } catch (Exception $e) {

            $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
            ❌Email could not be sent. Please try again.
             </div>";
        }
    } else {

        $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
        ❌Email not found
         </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-gray-700">
    <div class="w-full max-w-md p-8 space-y-4 bg-gray-800 bg-opacity-90 backdrop-blur-md rounded-2xl shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-100 text-center">Forgot Password</h2>
        <?php echo $msg; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-300">Email</label>
                <input name="email" type="email" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button name="submit" type="submit" class="w-full py-2 text-lg font-semibold text-gray-100 bg-blue-600 rounded-lg hover:bg-blue-700">Reset Password</button>
        </form>
        <p class="text-center text-gray-300 mt-2">
            <a href="index.php" class="text-blue-400 hover:underline">Back to login</a>
        </p>
    </div>
</body>
</html>
