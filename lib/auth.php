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
    // For local development without database - auto-login as demo user
    if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
        if (!is_authenticated()) {
            $_SESSION['user_id'] = 1; // Demo user
            $_SESSION['user_name'] = 'Demo User';
            $_SESSION['logged_in_at'] = time();
        }
        return;
    }
    
    // Normal authentication check
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
    
    // For local development without database
    if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
        return [
            'id' => 1,
            'name' => $_SESSION['user_name'] ?? 'Demo User',
            'email' => 'demo@example.com',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
    
    $pdo = db_connect();
    
    if (!$pdo) return null;
    
    $stmt = $pdo->prepare('SELECT * FROM app_user WHERE id = :id');
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
    $stmt = $pdo->prepare('SELECT id FROM app_user WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Email already registered'];
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user - check if name column exists
    try {
        // Try with name column first (for databases that have it)
        $stmt = $pdo->prepare('
            INSERT INTO app_user (name, email, password_hash, created_at) 
            VALUES (:name, :email, :password_hash, NOW())
        ');
        
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $password_hash
        ]);
        
        $user_id = $pdo->lastInsertId();
        return ['success' => true, 'user_id' => $user_id];
    } catch (PDOException $e) {
        error_log("Registration failed for $email: " . $e->getMessage());
        
        // Check if it's a sequence sync issue (duplicate key on primary key)
        if (strpos($e->getMessage(), 'duplicate key') !== false && 
            strpos($e->getMessage(), 'app_user_pkey') !== false) {
            // Sequence is out of sync - fix it and retry
            try {
                // Get max ID from table
                $stmt = $pdo->query('SELECT COALESCE(MAX(id), 0) as max_id FROM app_user');
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $max_id = (int)$result['max_id'];
                
                // Reset sequence to max_id (next insert will use max_id + 1)
                $pdo->exec("SELECT setval('app_user_id_seq', $max_id, true)");
                
                error_log("Fixed user sequence: reset to $max_id");
                
                // Retry the insert
                $stmt = $pdo->prepare('
                    INSERT INTO app_user (name, email, password_hash, created_at) 
                    VALUES (:name, :email, :password_hash, NOW())
                ');
                
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password_hash' => $password_hash
                ]);
                
                $user_id = $pdo->lastInsertId();
                return ['success' => true, 'user_id' => $user_id];
            } catch (PDOException $e3) {
                error_log("Registration failed after sequence fix: " . $e3->getMessage());
                return ['success' => false, 'error' => 'Registration failed: ' . $e3->getMessage()];
            }
        }
        
        // Check if it's a duplicate email error (unique constraint on email)
        if ((strpos($e->getMessage(), 'duplicate key') !== false && 
             strpos($e->getMessage(), 'app_user_email_key') !== false) ||
            (strpos($e->getMessage(), 'already exists') !== false && 
             strpos($e->getMessage(), 'email') !== false)) {
            return ['success' => false, 'error' => 'Email already registered'];
        }
        
        // Check if name column doesn't exist - fallback to insert without it
        if (strpos($e->getMessage(), 'column "name"') !== false) {
            try {
                $stmt = $pdo->prepare('
                    INSERT INTO app_user (email, password_hash, created_at) 
                    VALUES (:email, :password_hash, NOW())
                ');
                
                $stmt->execute([
                    'email' => $email,
                    'password_hash' => $password_hash
                ]);
                
                $user_id = $pdo->lastInsertId();
                return ['success' => true, 'user_id' => $user_id];
            } catch (PDOException $e2) {
                // Also check for sequence issue in fallback
                if (strpos($e2->getMessage(), 'duplicate key') !== false && 
                    strpos($e2->getMessage(), 'app_user_pkey') !== false) {
                    try {
                        // Fix sequence and retry
                        $stmt = $pdo->query('SELECT COALESCE(MAX(id), 0) as max_id FROM app_user');
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $max_id = (int)$result['max_id'];
                        $pdo->exec("SELECT setval('app_user_id_seq', $max_id, true)");
                        
                        // Retry insert
                        $stmt = $pdo->prepare('
                            INSERT INTO app_user (email, password_hash, created_at) 
                            VALUES (:email, :password_hash, NOW())
                        ');
                        
                        $stmt->execute([
                            'email' => $email,
                            'password_hash' => $password_hash
                        ]);
                        
                        $user_id = $pdo->lastInsertId();
                        return ['success' => true, 'user_id' => $user_id];
                    } catch (PDOException $e4) {
                        return ['success' => false, 'error' => 'Registration failed: ' . $e4->getMessage()];
                    }
                }
                
                return ['success' => false, 'error' => 'Registration failed: ' . $e2->getMessage()];
            }
        }
        
        return ['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()];
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
    $stmt = $pdo->prepare('SELECT * FROM app_user WHERE email = :email');
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

/**
 * ========================================
 * GOOGLE OAUTH 2.0 FUNCTIONS
 * ========================================
 */

/**
 * Generate Google OAuth authorization URL
 */
function google_auth_url() {
    // Ensure environment variables are loaded
    load_env();
    
    $client_id = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
    $redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');
    
    // Generate and store state for CSRF protection
    $state = bin2hex(random_bytes(16));
    $_SESSION['google_oauth_state'] = $state;
    
    $params = [
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'access_type' => 'online',
        'prompt' => 'select_account'
    ];
    
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

/**
 * Exchange authorization code for tokens
 */
function google_exchange_code($code) {
    // Ensure environment variables are loaded
    load_env();
    
    $client_id = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
    $client_secret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
    $redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');
    
    $token_url = 'https://oauth2.googleapis.com/token';
    
    $post_data = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        error_log("Google token exchange failed: HTTP $http_code - $response");
        return ['success' => false, 'error' => 'Failed to exchange authorization code'];
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['id_token'])) {
        error_log("Google token response missing id_token: " . print_r($data, true));
        return ['success' => false, 'error' => 'Invalid token response'];
    }
    
    return ['success' => true, 'tokens' => $data];
}

/**
 * Decode and verify Google ID token (JWT)
 * Simplified version - in production, should verify signature with Google's public keys
 */
function google_decode_id_token($id_token) {
    // Split JWT into parts
    $parts = explode('.', $id_token);
    
    if (count($parts) !== 3) {
        return ['success' => false, 'error' => 'Invalid ID token format'];
    }
    
    // Decode payload (middle part)
    $payload = base64_decode(strtr($parts[1], '-_', '+/'));
    $user_info = json_decode($payload, true);
    
    if (!$user_info) {
        return ['success' => false, 'error' => 'Failed to decode ID token'];
    }
    
    // Basic validation
    load_env();
    $client_id = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
    
    if (!isset($user_info['aud']) || $user_info['aud'] !== $client_id) {
        return ['success' => false, 'error' => 'Invalid token audience'];
    }
    
    if (!isset($user_info['exp']) || $user_info['exp'] < time()) {
        return ['success' => false, 'error' => 'Token expired'];
    }
    
    return ['success' => true, 'user_info' => $user_info];
}

/**
 * Get user info from Google
 */
function google_get_user_info($id_token) {
    $result = google_decode_id_token($id_token);
    
    if (!$result['success']) {
        return $result;
    }
    
    $user_info = $result['user_info'];
    
    // Extract relevant fields
    return [
        'success' => true,
        'user' => [
            'google_id' => $user_info['sub'],
            'email' => $user_info['email'] ?? '',
            'name' => $user_info['name'] ?? '',
            'picture' => $user_info['picture'] ?? '',
            'email_verified' => $user_info['email_verified'] ?? false
        ]
    ];
}

/**
 * Find or create user from Google OAuth data
 */
function find_or_create_google_user($google_user) {
    $pdo = db_connect();
    
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $email = $google_user['email'];
    $name = $google_user['name'];
    
    // Check if user exists by email
    $stmt = $pdo->prepare('SELECT * FROM app_user WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // User exists - return existing user
        return ['success' => true, 'user' => $user, 'is_new' => false];
    }
    
    // Create new user - try with name first, fallback without if column doesn't exist
    try {
        $stmt = $pdo->prepare('
            INSERT INTO app_user (name, email, password_hash, created_at) 
            VALUES (:name, :email, NULL, NOW())
            RETURNING id, name, email, created_at
        ');
        
        $stmt->execute([
            'name' => $name,
            'email' => $email
        ]);
        
        $user = $stmt->fetch();
        
        if (!$user) {
            // Fallback for databases that don't support RETURNING
            $user_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT * FROM app_user WHERE id = :id');
            $stmt->execute(['id' => $user_id]);
            $user = $stmt->fetch();
        }
        
        return ['success' => true, 'user' => $user, 'is_new' => true];
    } catch (PDOException $e) {
        error_log("Failed to create Google user: " . $e->getMessage());
        
        // Check if it's a duplicate key error (sequence out of sync)
        if (strpos($e->getMessage(), 'duplicate key') !== false && 
            strpos($e->getMessage(), 'app_user_pkey') !== false) {
            // Sequence is out of sync - fix it and retry
            try {
                // Get max ID from table
                $stmt = $pdo->query('SELECT COALESCE(MAX(id), 0) as max_id FROM app_user');
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $max_id = (int)$result['max_id'];
                
                // Reset sequence to max_id (next insert will use max_id + 1)
                $pdo->exec("SELECT setval('app_user_id_seq', $max_id, true)");
                
                error_log("Fixed user sequence: reset to $max_id");
                
                // Retry the insert
                $stmt = $pdo->prepare('
                    INSERT INTO app_user (name, email, password_hash, created_at) 
                    VALUES (:name, :email, NULL, NOW())
                    RETURNING id, name, email, created_at
                ');
                
                $stmt->execute([
                    'name' => $name,
                    'email' => $email
                ]);
                
                $user = $stmt->fetch();
                
                if (!$user) {
                    $user_id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare('SELECT * FROM app_user WHERE id = :id');
                    $stmt->execute(['id' => $user_id]);
                    $user = $stmt->fetch();
                }
                
                return ['success' => true, 'user' => $user, 'is_new' => true];
            } catch (PDOException $e3) {
                error_log("Failed to create Google user after sequence fix: " . $e3->getMessage());
                return ['success' => false, 'error' => 'Failed to create user account: ' . $e3->getMessage()];
            }
        }
        
        // Check if name column doesn't exist - fallback to insert without it
        if (strpos($e->getMessage(), 'column "name"') !== false) {
            try {
                $stmt = $pdo->prepare('
                    INSERT INTO app_user (email, password_hash, created_at) 
                    VALUES (:email, NULL, NOW())
                    RETURNING id, email, created_at
                ');
                
                $stmt->execute(['email' => $email]);
                
                $user = $stmt->fetch();
                
                if (!$user) {
                    $user_id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare('SELECT * FROM app_user WHERE id = :id');
                    $stmt->execute(['id' => $user_id]);
                    $user = $stmt->fetch();
                }
                
                return ['success' => true, 'user' => $user, 'is_new' => true];
            } catch (PDOException $e2) {
                error_log("Failed to create Google user (fallback): " . $e2->getMessage());
                
                // Also check for sequence issue in fallback
                if (strpos($e2->getMessage(), 'duplicate key') !== false && 
                    strpos($e2->getMessage(), 'app_user_pkey') !== false) {
                    try {
                        // Fix sequence and retry
                        $stmt = $pdo->query('SELECT COALESCE(MAX(id), 0) as max_id FROM app_user');
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $max_id = (int)$result['max_id'];
                        $pdo->exec("SELECT setval('app_user_id_seq', $max_id, true)");
                        
                        // Retry insert
                        $stmt = $pdo->prepare('
                            INSERT INTO app_user (email, password_hash, created_at) 
                            VALUES (:email, NULL, NOW())
                            RETURNING id, email, created_at
                        ');
                        
                        $stmt->execute(['email' => $email]);
                        $user = $stmt->fetch();
                        
                        if (!$user) {
                            $user_id = $pdo->lastInsertId();
                            $stmt = $pdo->prepare('SELECT * FROM app_user WHERE id = :id');
                            $stmt->execute(['id' => $user_id]);
                            $user = $stmt->fetch();
                        }
                        
                        return ['success' => true, 'user' => $user, 'is_new' => true];
                    } catch (PDOException $e4) {
                        return ['success' => false, 'error' => 'Failed to create user account: ' . $e4->getMessage()];
                    }
                }
                
                return ['success' => false, 'error' => 'Failed to create user account: ' . $e2->getMessage()];
            }
        }
        
        return ['success' => false, 'error' => 'Failed to create user account: ' . $e->getMessage()];
    }
}

/**
 * Handle complete Google OAuth flow
 */
function authenticate_google($code, $state) {
    // Verify state to prevent CSRF
    if (!isset($_SESSION['google_oauth_state']) || $_SESSION['google_oauth_state'] !== $state) {
        return ['success' => false, 'error' => 'Invalid state parameter'];
    }
    
    // Clear state
    unset($_SESSION['google_oauth_state']);
    
    // Exchange code for tokens
    $token_result = google_exchange_code($code);
    
    if (!$token_result['success']) {
        return $token_result;
    }
    
    // Get user info from ID token
    $user_info_result = google_get_user_info($token_result['tokens']['id_token']);
    
    if (!$user_info_result['success']) {
        return $user_info_result;
    }
    
    // Find or create user
    $user_result = find_or_create_google_user($user_info_result['user']);
    
    if (!$user_result['success']) {
        return $user_result;
    }
    
    // Return user with flag indicating if they're new
    return [
        'success' => true,
        'user' => $user_result['user'],
        'is_new_user' => $user_result['is_new']
    ];
}

