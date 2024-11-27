<?php
session_start();
include 'config.php'; // Ensure 'db.php' handles your MySQLi connection properly

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Check if fields are filled
    if (empty($username) || empty($password)) {
        $error = "Всички полета са задължителни.";
    } else {
        // Prepare and execute the statement securely
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: manage_requests.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Грешно потребителско име или парола.";
            }
        } else {
            $error = "Грешно потребителско име или парола.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Вход в системата</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="login-container">
        <h2>Вход</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="index.php">
            <input type="text" name="username" placeholder="Потребителско име" required>
            <input type="password" name="password" placeholder="Парола" required>
            <button type="submit">Вход</button>
        </form>
        <p>Нямате акаунт? <a href="register.php">Регистрирайте се тук</a></p>
    </div>
</body>
</html>
