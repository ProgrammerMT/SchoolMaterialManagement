<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<link rel="stylesheet" href="style.css">
<nav class="navbar">
    <div class="logo">
        <a href="index.php">School Supplies</a>
    </div>
    <ul class="nav-links">
        <?php if ($user_role == 'admin'): ?>
            <li><a href="admin_dashboard.php">Админ Панел</a></li>
            <li><a href="manage_requests.php">Заявки</a></li>
        <?php elseif ($user_role == 'user'): ?>
            <li><a href="user_management.php">Моята Дашборд</a></li>
            <li><a href="create_request.php">Създай Заявка</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="logout.php" class="btn logout">Изход</a></li>
        <?php else: ?>
            <li><a href="index.php">Вход</a></li>
        <?php endif; ?>
    </ul>
</nav>
