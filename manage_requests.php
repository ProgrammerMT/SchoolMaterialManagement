<?php
session_start();
include 'config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Handle status updates
if (isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $request_id = $_POST['request_id'];
    $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();
}

// Fetch requests with their associated items
$result = $conn->query("
    SELECT r.id AS request_id, u.username, r.request_date, r.description, r.status,
           ri.item_name, ri.quantity, ri.price
    FROM requests r
    JOIN users u ON r.user_id = u.id
    JOIN request_items ri ON r.id = ri.request_id
    ORDER BY r.id, ri.id
");

// Group items by request_id
$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[$row['request_id']]['details'] = [
        'username' => $row['username'],
        'request_date' => $row['request_date'],
        'description' => $row['description'],
        'status' => $row['status']
    ];
    $requests[$row['request_id']]['items'][] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Управление на заявки</title>
    <style>
        .request-row { background-color: #f0f0f0; }
        .item-row { background-color: #ffffff; }
        .item-cell { padding-left: 30px; }
    </style>
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
                <th>Консумативи</th>
            </tr>
            <?php foreach ($requests as $request_id => $request): ?>
            <tr class="request-row">
                <td><?php echo htmlspecialchars($request['details']['username']); ?></td>
                <td><?php echo htmlspecialchars($request['details']['request_date']); ?></td>
                <td><?php echo htmlspecialchars($request['details']['description']); ?></td>
                <td><?php echo ucfirst($request['details']['status']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                        <select name="status">
                            <option value="pending" <?php if($request['details']['status'] == 'pending') echo 'selected'; ?>>Изчакваща</option>
                            <option value="approved" <?php if($request['details']['status'] == 'approved') echo 'selected'; ?>>Одобрена</option>
                            <option value="rejected" <?php if($request['details']['status'] == 'rejected') echo 'selected'; ?>>Отхвърлена</option>
                        </select>
                        <button type="submit" name="update_status">Промени</button>
                    </form>
                </td>
                <td>
                    <?php foreach ($request['items'] as $item): ?>
                        <strong><?php echo htmlspecialchars($item['item_name']); ?></strong><br>
                        Бройка: <?php echo $item['quantity']; ?><br>
                        Цена: <?php echo number_format($item['price'], 2); ?> лв<br><br>
                    <?php endforeach; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
