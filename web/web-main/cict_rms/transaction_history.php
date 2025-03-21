<?php
require 'db_connect.php';

// Pagination
$limit = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of transactions
$sql = "SELECT COUNT(*) AS total FROM transactions";
$result = $conn->query($sql);
$totalItems = $result->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $limit);

// Fetch transactions for the current page
$sql = "SELECT * FROM transactions ORDER BY transaction_date DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
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
            cursor: pointer;
        }
        th:hover {
            background-color: #0056b3;
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
        .search-container {
            margin-bottom: 20px;
        }
        #searchInput {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
    <script>
        function sortTable(columnIndex) {
            const table = document.querySelector("table");
            const tbody = table.querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));

            rows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();

                // Numeric sorting for Item ID and Date columns
                if (columnIndex === 0 || columnIndex === 6) {
                    return aValue - bValue;
                }
                // String sorting for other columns
                return aValue.localeCompare(bValue);
            });

            // Clear the table body and append sorted rows
            tbody.innerHTML = "";
            rows.forEach(row => tbody.appendChild(row));
        }

        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toUpperCase();
            const table = document.getElementById("transactionTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                const row = rows[i];
                let match = false;
                for (let j = 0; j < row.cells.length; j++) {
                    const cell = row.cells[j];
                    if (cell.textContent.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                        break;
                    }
                }
                row.style.display = match ? "" : "none";
            }
        }
    </script>
</head>
<body>
    <h2>Transaction History</h2>

    <!-- Search Form -->
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchTable()">
    </div>

    <table id="transactionTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Item ID</th>
                <th onclick="sortTable(1)">Student ID</th>
                <th onclick="sortTable(2)">Student Name</th>
                <th onclick="sortTable(3)">Transaction Type</th>
                <th onclick="sortTable(4)">Status</th>
                <th onclick="sortTable(5)">Verified By</th>
                <th onclick="sortTable(6)">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['item_id']; ?></td>
                    <td><?php echo $transaction['student_id']; ?></td>
                    <td><?php echo $transaction['student_name']; ?></td>
                    <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                    <td><?php echo ucfirst($transaction['status']); ?></td>
                    <td><?php echo $transaction['verified_by']; ?></td>
                    <td><?php echo $transaction['transaction_date']; ?></td>
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
</body>
</html>