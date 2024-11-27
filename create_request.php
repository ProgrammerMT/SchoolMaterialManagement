<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO requests (user_id, request_date, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $date, $description);
    $stmt->execute();

    header('Location: dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Създаване на заявка</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Създай нова заявка</h2>
        <form method="POST">
            <textarea name="description" placeholder="Описание на консумативите" required></textarea>
            <button type="submit">Изпрати заявка</button>
        </form>
    </div>
</body>
</html>
