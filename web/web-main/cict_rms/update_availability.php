<?php
require 'db_connect.php';
$query = "SELECT * FROM inventory WHERE item_availability = 'unavailable'";
$result = mysqli_query($conn, $query);
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
?>
<!-- update_availability.php -->
<div id="UpdateAvailability" class="tabcontent">
    <h2>Update Item Availability</h2>
    <div class="form-container">
        <form method="POST">
            <select name="item_id" required>
                <option value="">Select Item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="new_availability" required>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
            <button type="submit" name="update_availability">Update Availability</button>
        </form>
    </div>
</div>