<?php
require 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle CRUD operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_item'])) {
        // Add new item
        $item_name = $_POST['item_name'];
        $item_quantity = $_POST['item_quantity'];
        $item_availability = ($item_quantity > 0) ? 'Available' : 'Unavailable';

        $sql = "INSERT INTO inventory (item_name, item_quantity, item_availability) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $item_name, $item_quantity, $item_availability);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_item'])) {
        // Edit existing item
        $id = $_POST['id'];
        $item_name = $_POST['item_name'];
        $item_quantity = $_POST['item_quantity'];
        $item_availability = ($item_quantity > 0) ? 'Available' : 'Unavailable';

        $sql = "UPDATE inventory SET item_name = ?, item_quantity = ?, item_availability = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $item_name, $item_quantity, $item_availability, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_item'])) {
        // Delete item
        $id = $_POST['id'];

        $sql = "DELETE FROM inventory WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Pagination
$limit = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of items
$sql = "SELECT COUNT(*) AS total FROM inventory";
$result = $conn->query($sql);
$totalItems = $result->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $limit);

// Fetch items for the current page
$sql = "SELECT * FROM inventory LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container input {
            margin: 5px;
            padding: 8px;
            width: 200px;
        }
        .form-container button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Inventory</h1>
        <!-- Add Item Form -->
        <div class="form-container">
            <form method="POST">
                <input type="text" name="item_name" placeholder="Item Name" required>
                <input type="number" name="item_quantity" placeholder="Quantity" required>
                <button type="submit" name="add_item">Add Item</button>
            </form>
        </div>

        <!-- Inventory Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['item_name']; ?></td>
                        <td><?php echo $item['item_quantity']; ?></td>
                        <td><?php echo $item['item_availability']; ?></td>
                        <td>
                            <!-- Edit Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="text" name="item_name" value="<?php echo $item['item_name']; ?>" required>
                                <input type="number" name="item_quantity" value="<?php echo $item['item_quantity']; ?>" required>
                                <button type="submit" name="edit_item">Edit</button>
                            </form>
                            <!-- Delete Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_item">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>