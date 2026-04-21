<?php
/**
 * seed_admin.php
 * ---------------------------------------------------------------
 * Run this ONCE in the browser:  http://localhost/your-folder/seed_admin.php
 * It will create the admin account, then delete itself.
 *
 * Admin credentials:
 *   Email   : admin@valiente.com
 *   Password: Admin@2025
 * ---------------------------------------------------------------
 */
require_once 'connection.php';

$username = 'admin';
$email    = 'admin@valiente.com';
$password = 'Admin@2025';
$hash     = password_hash($password, PASSWORD_BCRYPT);

// Check if already exists
$chk = $connection->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
$chk->bind_param('ss', $username, $email);
$chk->execute();
$chk->store_result();

if ($chk->num_rows > 0) {
    echo '<p style="font-family:monospace;padding:2rem;">⚠️ Admin account already exists. Delete this file manually.</p>';
} else {
    $ins = $connection->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    $ins->bind_param('sss', $username, $email, $hash);

    if ($ins->execute()) {
        // Self-delete
        @unlink(__FILE__);
        echo '<p style="font-family:monospace;padding:2rem;">✅ Admin account created!<br><br>
              <strong>Email:</strong> admin@valiente.com<br>
              <strong>Password:</strong> Admin@2025<br><br>
              This file has been deleted. <a href="login.php">Go to login →</a></p>';
    } else {
        echo '<p style="font-family:monospace;padding:2rem;">❌ Error: ' . htmlspecialchars($connection->error) . '</p>';
    }
}
