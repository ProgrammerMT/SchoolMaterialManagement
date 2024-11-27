<?php
session_start();
include 'config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $request_id = $_POST['request_id'];
    $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();
}

$result = $conn->query("SELECT requests.id, users.username, requests.request_date, requests.description, requests.status FROM requests JOIN users ON requests.user_id = users.id");
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Управление на заявки</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Управление на заявки</h2>
        <table>
            <tr>
                <th>Потребител</th>
                <th>Дата</th>
                <th>Описание</th>
                <th>Статус</th>
                <th>Действие</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['request_date']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                        <select name="status">
                            <option value="pending" <?php if($row['status'] == 'pending') echo 'selected'; ?>>Изчакваща</option>
                            <option value="approved" <?php if($row['status'] == 'approved') echo 'selected'; ?>>Одобрена</option>
                            <option value="rejected" <?php if($row['status'] == 'rejected') echo 'selected'; ?>>Отхвърлена</option>
                        </select>
                        <button type="submit" name="update_status">Промени</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
