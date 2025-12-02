<?php
/**
 * Utility functions
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 3
 */

/**
 * Render a view with layout
 * @param string $view
 * @param array $data
 */
function render($view, $data = []) {
    extract($data);
    require __DIR__ . '/../views/layout.header.php';
    require __DIR__ . '/../views/' . $view . '.php';
    require __DIR__ . '/../views/layout.footer.php';
    exit;
}

/**
 * Output JSON response
 * @param mixed $data
 * @param int $code
 */
function json_out($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

/**
 * Redirect to URL
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Require POST method
 */
function require_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo "Method not allowed";
        exit;
    }
}

/**
 * Escape HTML output
 * @param string $text
 * @return string
 */
function h($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF token alias
 * @return string
 */
function csrf() {
    return csrf_token();
}
