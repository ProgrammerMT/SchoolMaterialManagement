<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Дашборд</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Добре дошли!</h1>
        <?php if ($user_role == 'user'): ?>
            <a href="create_request.php" class="btn">Създай заявка</a>
        <?php elseif ($user_role == 'admin'): ?>
            <a href="manage_requests.php" class="btn">Управление на заявки</a>
        <?php endif; ?>
        <a href="logout.php" class="btn logout">Изход</a>
    </div>
</body>
</html>
