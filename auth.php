<?php
session_start();
require_once 'config.php';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// User login function
function login($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, password, role_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password, $role_id);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $role_id;
            return true;
        }
    }
    return false;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
}

// Check if user is admin
function is_admin() {
    return (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1);
}

// Create new user (admin only)
function create_user($username, $password, $email, $role_id) {
    global $conn;
    if (!is_admin()) {
        return false;
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $hashed_password, $email, $role_id);
    return $stmt->execute();
}

// Verify CSRF token
function verify_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>
