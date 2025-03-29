<?php
session_start();
include 'conf.php';

if (!isset($_SESSION['SESSION_EMAIL'])) {
    header('Location: index.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_SESSION['SESSION_EMAIL']]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Welcome to your website, " . htmlspecialchars($user['email']) . " <a href='logout.php'>Logout</a>";
    } else {
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Database query error: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>