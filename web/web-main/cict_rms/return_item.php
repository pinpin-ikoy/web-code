<!-- return_item.php -->
<div id="ReturnItem" class="tabcontent">
    <h2>Return Item</h2>
    <div class="form-container">
        <form method="POST">
            <select name="item_id" required>
                <option value="">Select Item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="student_id" placeholder="Student ID" required>
            <input type="text" name="student_name" placeholder="Student Name" required>
            <button type="submit" name="return_item">Return Item</button>
        </form>
    </div>
</div>