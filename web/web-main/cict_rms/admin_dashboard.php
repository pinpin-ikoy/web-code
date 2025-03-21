<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_dashboard.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h1>Welcome Admin, <?php echo $_SESSION['name']; ?></h1>
        <div class="tabs">
            <button class="tablink" onclick="openTab(event, 'ManageInventory')">Manage Inventory</button>
            <button class="tablink" onclick="openTab(event, 'Transactions')">Transactions</button>
            <button class="tablink" onclick="openTab(event, 'Graphs')">Graphs</button>
            <button class="tablink" onclick="openTab(event, 'ManageUsers')">Manage Users</button>
        </div>

        <!-- Tab Content -->
        <div id="ManageInventory" class="tabcontent">
            <?php include 'manage_inventory.php'; ?>
        </div>

        <div id="Transactions" class="tabcontent">
            <h2>Transactions</h2>
            <?php include 'transaction_history.php'; ?>
        </div>

        <div id="Graphs" class="tabcontent">
            <h2>Graphs</h2>
            <p>This is where you view graphs and analytics.</p>
            <!-- Add graphs and charts here -->
        </div>

        <div id="ManageUsers" class="tabcontent">
            <?php include 'manage_users.php'; ?>
        </div>
            
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

           