<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle request cancellation
if (isset($_POST['cancel_request'])) {
    $request_id = $_POST['request_id'];
    
    // Update request status to 'rejected' only if it's still pending
    $stmt = $conn->prepare("UPDATE requests SET status = 'rejected' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $request_id, $user_id);
    $stmt->execute();
}

// Fetch requests and their corresponding items for the logged-in user
$stmt = $conn->prepare("
    SELECT r.id, r.request_date, r.status, ri.item_name, ri.quantity, ri.price
    FROM requests r
    LEFT JOIN request_items ri ON r.id = ri.request_id
    WHERE r.user_id = ?
    ORDER BY r.request_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Моите заявки</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Моите заявки</h2>
        <table>
            <tr>
                <th>Дата</th>
                <th>Описание</th>
                <th>Статус</th>
                <th>Действие</th>
            </tr>
            <?php
            $current_request_id = null;
            $items_html = '';

            while ($row = $result->fetch_assoc()) {
                if ($current_request_id != $row['id']) {
                    if ($current_request_id != null) {
                        echo "<tr>
                                <td>{$request_date}</td>
                                <td>{$items_html}</td>
                                <td>" . ucfirst($current_status) . "</td>
                                <td>" . ($current_status == 'pending' ? 
                                            "<form method='POST'>
                                                <input type='hidden' name='request_id' value='{$current_request_id}'>
                                                <button type='submit' name='cancel_request' onclick=\"return confirm('Сигурни ли сте, че искате да откажете тази заявка?');\">Откажи</button>
                                            </form>" : "<span>Не може да бъде отказана</span>") . 
                            "</td>
                            </tr>";
                    }

                    $request_date = htmlspecialchars($row['request_date']);
                    $items_html = "<strong>{$row['item_name']}</strong><br>Бройка: {$row['quantity']}<br>Цена: " . number_format($row['price'], 2) . " лв";
                    $current_request_id = $row['id'];
                    $current_status = $row['status'];
                } else {
                    $items_html .= "<br><br><strong>{$row['item_name']}</strong><br>Бройка: {$row['quantity']}<br>Цена: " . number_format($row['price'], 2) . " лв";
                }
            }

            if ($current_request_id != null) {
                echo "<tr>
                        <td>{$request_date}</td>
                        <td>{$items_html}</td>
                        <td>" . ucfirst($current_status) . "</td>
                        <td>" . ($current_status == 'pending' ? 
                                    "<form method='POST'>
                                        <input type='hidden' name='request_id' value='{$current_request_id}'>
                                        <button type='submit' name='cancel_request' onclick=\"return confirm('Сигурни ли сте, че искате да откажете тази заявка?');\">Откажи</button>
                                    </form>" : "<span>Не може да бъде отказана</span>") . 
                        "</td>
                    </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
