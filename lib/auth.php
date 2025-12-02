<?php
/**
 * Authentication helper functions
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

/**
 * Check if user is authenticated
 */
function is_authenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require authentication (redirect to login if not authenticated)
 */
function require_auth() {
    if (!is_authenticated()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('index.php?action=login');
    }
}

/**
 * Get current authenticated user ID
 */
function auth_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current authenticated user data
 */
function auth_user() {
    if (!is_authenticated()) {
        return null;
    }
    
    $user_id = auth_user_id();
    $pdo = db_connect();
    
    if (!$pdo) return null;
    
    $stmt = $pdo->prepare('SELECT * FROM user WHERE id = :id');
    $stmt->execute(['id' => $user_id]);
    return $stmt->fetch();
}

/**
 * Register a new user
 */
function register_user($name, $email, $password) {
    $pdo = db_connect();
    
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM user WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Email already registered'];
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $pdo->prepare('
        INSERT INTO user (name, email, password_hash, created_at) 
        VALUES (:name, :email, :password_hash, NOW())
    ');
    
    try {
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $password_hash
        ]);
        
        $user_id = $pdo->lastInsertId();
        return ['success' => true, 'user_id' => $user_id];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Registration failed'];
    }
}

/**
 * Authenticate user with email and password
 */
function authenticate_user($email, $password) {
    $pdo = db_connect();
    
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    // Get user by email
    $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'error' => 'Invalid email or password'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'error' => 'Invalid email or password'];
    }
    
    return ['success' => true, 'user' => $user];
}

/**
 * Login user (set session)
 */
function login_user($user_id, $remember = false) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['logged_in_at'] = time();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Set remember me cookie if requested (30 days)
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['remember_token'] = $token;
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
    }
}

/**
 * Logout user
 */
function logout_user() {
    // Clear session
    $_SESSION = array();
    
    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Validate registration input
 */
function validate_registration($data) {
    $errors = [];
    $clean = [];
    
    // Validate name
    $name = trim($data['name'] ?? '');
    if (empty($name)) {
        $errors[] = 'Name is required';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters';
    } else {
        $clean['name'] = $name;
    }
    
    // Validate email
    $email = trim($data['email'] ?? '');
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    } else {
        $clean['email'] = $email;
    }
    
    // Validate password
    $password = $data['password'] ?? '';
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    } else {
        $clean['password'] = $password;
    }
    
    // Validate password confirmation
    $password_confirm = $data['password_confirm'] ?? '';
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match';
    }
    
    // Validate terms acceptance
    if (!isset($data['terms']) || $data['terms'] != '1') {
        $errors[] = 'You must agree to the terms and privacy policy';
    }
    
    return [$errors, $clean];
}

/**
 * Validate login input
 */
function validate_login($data) {
    $errors = [];
    $clean = [];
    
    // Validate email
    $email = trim($data['email'] ?? '');
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    } else {
        $clean['email'] = $email;
    }
    
    // Validate password
    $password = $data['password'] ?? '';
    if (empty($password)) {
        $errors[] = 'Password is required';
    } else {
        $clean['password'] = $password;
    }
    
    return [$errors, $clean];
}

