<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $items = $_POST['items'];

    // Create a dynamic description based on the first item name
    $description = "";
    if (!empty($items[0]['name'])) {
        $description = $items[0]['name'];  // Using the name of the first item for the description
    }

    // Insert the main request into the 'requests' table
    $stmt = $conn->prepare("INSERT INTO requests (user_id, description) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $description);
    $stmt->execute();
    $request_id = $conn->insert_id; // Get the ID of the inserted request

    // Insert each item into the 'request_items' table
    $stmtItem = $conn->prepare("INSERT INTO request_items (request_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmtItem->bind_param("isid", $request_id, $item['name'], $item['quantity'], $item['price']);
        $stmtItem->execute();
    }

    // Redirect the user to the user management page
    header("Location: user_management.php");
    exit();
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
        <form method="POST" action="create_request.php" id="requestForm">
            <div id="itemsContainer">
                <div class="item">
                    <input type="text" name="items[0][name]" placeholder="Име на консуматив" required>
                    <input type="number" name="items[0][quantity]" placeholder="Бройка" min="1" required>
                    <input type="number" name="items[0][price]" placeholder="Цена" step="0.01" required>
                </div>
            </div>
            <button type="button" onclick="addItem()">Добави консуматив</button>
            <button type="submit">Подай заявка</button>
        </form>
    </div>

    <script>
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const itemCount = container.getElementsByClassName('item').length;
            const newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.innerHTML = `
                <input type="text" name="items[${itemCount}][name]" placeholder="Име на консуматив" required>
                <input type="number" name="items[${itemCount}][quantity]" placeholder="Бройка" min="1" required>
                <input type="number" name="items[${itemCount}][price]" placeholder="Цена" step="0.01" required>
            `;
            container.appendChild(newItem);
        }
    </script>
</body>
</html>
