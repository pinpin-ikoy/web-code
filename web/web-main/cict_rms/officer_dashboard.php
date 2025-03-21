<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'officer') {
    header("Location: login.php");
    exit();
}

// Handle updating item availability
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_availability'])) {
    $item_id = $_POST['item_id'];
    $new_availability = $_POST['new_availability'];

    $sql = "UPDATE inventory SET item_availability = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_availability, $item_id);
    $stmt->execute();
    $stmt->close();
}

// Handle borrowing items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_item'])) {
    $item_id = $_POST['item_id'];
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $verified_by = $_SESSION['username'];

    // Insert the borrow transaction
    $sql = "INSERT INTO transactions (item_id, student_id, student_name, transaction_type, verified_by, status) VALUES (?, ?, ?, 'borrowed', ?, 'borrowed')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $item_id, $student_id, $student_name, $verified_by);
    $stmt->execute();
    $stmt->close();

    // Update item availability
    $sql = "UPDATE inventory SET item_quantity = item_quantity - 1, item_availability = IF(item_quantity - 1 <= 0, 'Unavailable', 'Available') WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();
}

// Handle returning items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_item'])) {
    $item_id = $_POST['item_id'];
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $verified_by = $_SESSION['username'];

    // Find the corresponding borrowed transaction
    $sql = "SELECT id FROM transactions WHERE item_id = ? AND student_id = ? AND status = 'borrowed' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $item_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $borrowed_transaction = $result->fetch_assoc();
    $stmt->close();

    if ($borrowed_transaction) {
        // Update the borrowed transaction to 'returned'
        $sql = "UPDATE transactions SET status = 'returned' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $borrowed_transaction['id']);
        $stmt->execute();
        $stmt->close();

        // Update item availability
        $sql = "UPDATE inventory SET item_quantity = item_quantity + 1, item_availability = 'Available' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<script>alert('No matching borrowed transaction found.');</script>";
    }
}

// Fetch all items
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
$items = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all transactions
$sql = "SELECT * FROM transactions ORDER BY transaction_date DESC";
$result = $conn->query($sql);
$transactions = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard</title>
    <link rel="stylesheet" href="officer_dashboard.css">
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h1 style="color: aliceblue;">Welcome Officer, <?php echo $_SESSION['username']; ?></h1>


        <!-- Tabs -->
        <div class="tabs">
            <button class="tablink" onclick="openTab(event, 'BorrowItem')">Borrow Item</button>
            <button class="tablink" onclick="openTab(event, 'ReturnItem')">Return Item</button>
            <button class="tablink" onclick="openTab(event, 'UpdateAvailability')">Update Availability</button>
            <button class="tablink" onclick="openTab(event, 'TransactionHistory')">Transaction History</button>
        </div>


        <!-- Include Tab Content -->
        <?php include 'borrow_item.php'; ?>
        <?php include 'return_item.php'; ?>
        <?php include 'update_availability.php'; ?>
        <?php include 'transaction_history.php'; ?>
    </div>
    

    <script>
        function openTab(event, tabName) {
            // Hide all tab content
            const tabcontent = document.querySelectorAll(".tabcontent");
            tabcontent.forEach(tab => tab.style.display = "none");

            // Remove "active" class from all tab links
            const tablinks = document.querySelectorAll(".tablink");
            tablinks.forEach(tab => tab.classList.remove("active"));

            // Show the current tab content and mark the button as active
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.classList.add("active");
        }

        // Open the first tab by default
        document.querySelector(".tablink").click();
    </script>
</body>
</html>