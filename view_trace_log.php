<?php
$log_file = sys_get_temp_dir() . '/recipe_debug_trace.log';

if (isset($_GET['clear']) && file_exists($log_file)) {
    unlink($log_file);
    header('Location: view_trace_log.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trace Log</title>
    <meta http-equiv="refresh" content="3">
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        h1 { color: #4ec9b0; }
        button { padding: 10px 20px; margin: 5px; background: #007acc; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a9e; }
        .clear-btn { background: #f48771; }
        .clear-btn:hover { background: #d16969; }
        pre { background: #252526; padding: 15px; border-radius: 4px; overflow-x: auto; line-height: 1.6; }
        .line { padding: 2px 0; }
        .start { color: #4ec9b0; font-weight: bold; }
        .error { color: #f48771; }
        .success { color: #4ec9b0; }
        .info { color: #dcdcaa; }
    </style>
</head>
<body>
    <h1>📋 Trace Log (Auto-refresh: 3s)</h1>
    
    <div>
        <button onclick="location.reload()">🔄 Refresh Now</button>
        <button class="clear-btn" onclick="location.href='?clear=1'">🗑️ Clear Log</button>
        <button onclick="window.close()">✖️ Close</button>
    </div>
    
    <p><strong>Log File:</strong> <code><?= htmlspecialchars($log_file) ?></code></p>
    <p><strong>Exists:</strong> <?= file_exists($log_file) ? '✅ Yes' : '❌ No' ?></p>
    
    <?php if (file_exists($log_file)): ?>
        <p><strong>Size:</strong> <?= filesize($log_file) ?> bytes</p>
        <p><strong>Modified:</strong> <?= date('Y-m-d H:i:s', filemtime($log_file)) ?></p>
        
        <h2>📝 Log Content</h2>
        <pre><?php
        $content = file_get_contents($log_file);
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $class = 'line';
            if (strpos($line, '===') !== false) $class .= ' start';
            elseif (strpos($line, '❌') !== false || strpos($line, 'ERROR') !== false) $class .= ' error';
            elseif (strpos($line, '✅') !== false) $class .= ' success';
            else $class .= ' info';
            
            echo '<div class="' . $class . '">' . htmlspecialchars($line) . '</div>';
        }
        ?></pre>
    <?php else: ?>
        <p style="color: #dcdcaa;">📭 No log file yet. Run the debug test first.</p>
    <?php endif; ?>
</body>
</html>

