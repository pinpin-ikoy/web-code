<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <div class="container">
        <h2>Register</h2>
        <form action="register_process.php" method="POST" onsubmit="return validateForm()">
            <input type="text" name="first_name" placeholder="First Name" required><br>
            <input type="text" name="last_name" placeholder="Last Name" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>

            <!-- Phone Number Input -->
            <div class="phone-container">
                <span>+63</span>
                <input type="text" name="contact_number" id="contact_number" placeholder="912345678" maxlength="10" required>
            </div>

            <input type="password" name="password" id="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter Password" required><br>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="officer">Officer</option>
            </select><br>
            <div class="btn">
                <button type="submit">Register</button>
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>

</body>
</html>
