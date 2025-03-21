<?php
require 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle status change
if (isset($_GET['change_status'])) {
    $userId = $_GET['user_id'];
    $newStatus = $_GET['new_status'];

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);
    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating status.');</script>";
    }
    $stmt->close();
}

// Fetch user data for the "Manage Users" tab
$users = [];
$sql = "SELECT id, username, role, first_name, last_name, email, contact_number, status FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <h2>Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td><?php echo $user['first_name']; ?></td>
                            <td><?php echo $user['last_name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['contact_number']; ?></td>
                            <td><?php echo $user['status']; ?></td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <a href="?change_status=true&user_id=<?php echo $user['id']; ?>&new_status=inactive" class="status-btn inactive">Deactivate</a>
                                <?php else: ?>
                                    <a href="?change_status=true&user_id=<?php echo $user['id']; ?>&new_status=active" class="status-btn active">Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
</html>