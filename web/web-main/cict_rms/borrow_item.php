<!-- borrow_item.php -->
 <?php
 require 'db_connect.php';
 // Fetch only available items
$query = "SELECT * FROM inventory WHERE item_availability = 'available'";
$result = mysqli_query($conn, $query);
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!-- borrow_item.php -->
<div id="BorrowItem" class="tabcontent">
    <h2>Borrow Item</h2>
    <div class="form-container">
        <form method="POST">
            <select name="item_id" required>
                <option value="">Select Item</option>
                <?php foreach ($inventories as $inventory): ?>
                    <option value="<?php echo $inventory['id']; ?>"><?php echo $inventory['item_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="student_id" placeholder="Student ID" required>
            <input type="text" name="student_name" placeholder="Student Name" required>
            <button type="submit" name="borrow_item">Borrow Item</button>
                </form>
    </div>
</div>