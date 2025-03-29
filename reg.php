<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
require 'conf.php'; // Ensure this contains your PDO connection

$msg = "";

if (isset($_POST["submit"])) {
    // Sanitize and validate email input
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
                ❌ Invalid email format.
            </div>";
    } elseif ($password !== $confirm_password) {
        $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
                ❌ Passwords do not match.
            </div>";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists > 0) {
            $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
                    ❌ Email already exists.
                </div>";
        } else {
            // Hash password and generate a verification code
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $code = bin2hex(random_bytes(16));

            // Insert user into the database
            $sql = "INSERT INTO users (email, password, code) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$email, $hashedPassword, $code]);

            if ($result) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'e7ifubbb@gmail.com';  // Change this to your actual email
                    $mail->Password   = 'lgxo sznv qydl izuc'; // Change to your actual email password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('e7ifubbb@gmail.com', 'YourAppName');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Account Verification';
                    $mail->Body    = 'Click the link to verify your account: 
                                      <a href="http://localhost/register/verify.php?code=' . $code . '">Verify Now</a>';

                    $mail->send();
                    $msg = "<div class='w-full p-3 text-sm text-center text-green-400 bg-green-900/40 border border-green-500 rounded-lg shadow-md'>
                            ✅ We\'ve sent a verification email.
                        </div>";
                } catch (Exception $e) {
                    $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
                            ❌ Email could not be sent. Error: {$mail->ErrorInfo}
                        </div>";
                }
            } else {
                $msg = "<div class='w-full p-3 text-sm text-center text-red-400 bg-red-900/40 border border-red-500 rounded-lg shadow-md'>
                        ❌ Something went wrong. Please try again.
                    </div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-gray-700">
    <div class="w-full max-w-md p-8 space-y-4 bg-gray-800 bg-opacity-90 backdrop-blur-md rounded-2xl shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-100 text-center">Register</h2>
        <?php echo $msg; ?>
        <form class="space-y-4" action="" method="POST">
            <div>
                <label class="block text-gray-300">Email</label>
                <input name="email" type="email" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-gray-300">Password</label>
                <input name="password" type="password" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-gray-300">Confirm Password</label>
                <input name="confirm_password" type="password" class="w-full px-4 py-2 mt-1 bg-gray-700 text-gray-300 border-none rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button name="submit" type="submit" class="w-full py-2 text-lg font-semibold text-gray-100 bg-blue-600 rounded-lg hover:bg-blue-700">Register</button>
        </form>
        <p class="text-center text-gray-300">
            <a href="index.php" class="text-blue-400 hover:underline">Already have an account? Login</a>
        </p>
    </div>
</body>
</html>
