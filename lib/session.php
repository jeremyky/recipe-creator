<?php
/**
 * Session management helpers
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 3
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get current user ID from session (or default to 1 for demo/development)
 * @return int
 */
function user_id() {
    // Use authenticated user ID if logged in
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    // For development/testing without authentication
    return 1; // Demo user
}

/**
 * Set flash message
 * @param string $type
 * @param mixed $message
 */
function flash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear flash messages
 * @return array
 */
function get_flash() {
    if (!isset($_SESSION['flash'])) {
        return [];
    }
    $messages = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Generate CSRF token
 * @return string
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

