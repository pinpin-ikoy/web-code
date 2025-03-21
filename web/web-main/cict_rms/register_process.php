<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // 'admin' or 'officer'

    // Ensure contact number is exactly 9 digits
    if (!preg_match("/^[0-9]{9}$/", $contact_number)) {
        die("Invalid contact number. Enter 9 digits only (e.g., 912345678).");
    }

    // Format contact number to include +63
    $formatted_contact = "+63" . $contact_number;

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO users (first_name, last_name, username, email, contact_number, password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $first_name, $last_name, $username, $email, $formatted_contact, $hashed_password, $role);
    
    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Redirecting...</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    background: #6A11CB; /*background or whatever you prefer */
                    overflow: hidden; 
                    transition: background 0.5s ease;
                }
                .transition-container {
                    position: fixed;
                    width: 100vw;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: rgba(255, 255, 255, 0.8); /* semi-transparent overlay */
                    z-index: 999; /* make sure it is on top */
                    animation: fade-out 0.8s ease forwards;
                }
                @keyframes fade-out {
                    0% {
                        opacity: 1;
                    }
                    100% {
                        opacity: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class='transition-container'>
                <div>Loading...</div> <!-- You could replace this with a spinner if desired -->
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = 'login.php'; // Redirect after animation
                }, 800);
            </script>
        </body>
        </html>";
        exit();
    } else {
        echo "<script>alert('Error: Could not register.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
