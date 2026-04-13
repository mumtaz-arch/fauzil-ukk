<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>System Setup</h2>";

$conn = getConnection();

// 1. Update Schema
echo "Updating table schema...<br>";
$sql_alter = "ALTER TABLE user MODIFY password VARCHAR(255) NOT NULL";
if (mysqli_query($conn, $sql_alter)) {
    echo "✅ Table 'user' updated successfully.<br>";
} else {
    echo "❌ Error updating table: " . mysqli_error($conn) . "<br>";
}

// 2. Create Admin Account
$admin_user = 'admin';
$admin_pass = 'admin123';
$hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

echo "Setting up admin account...<br>";

// Check if admin exists
$check_query = "SELECT * FROM user WHERE nama_user = '$admin_user'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Update existing
    $update_query = "UPDATE user SET password = '$hashed_pass' WHERE nama_user = '$admin_user'";
    if (mysqli_query($conn, $update_query)) {
        echo "✅ Admin account '$admin_user' updated with new hashed password.<br>";
    } else {
        echo "❌ Error updating admin: " . mysqli_error($conn) . "<br>";
    }
} else {
    // Create new
    $insert_query = "INSERT INTO user (nama_user, password) VALUES ('$admin_user', '$hashed_pass')";
    if (mysqli_query($conn, $insert_query)) {
        echo "✅ Admin account '$admin_user' created successfully.<br>";
    } else {
        echo "❌ Error creating admin: " . mysqli_error($conn) . "<br>";
    }
}

echo "<br><strong>Setup Complete!</strong><br>";
echo "Please delete this file (admin_setup.php) before deploying to production.<br>";
echo "<a href='auth/login.php'>Go to Login</a>";
?>
