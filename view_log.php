<?php
/**
 * View API Debug Log
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Debug Log</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        h1 { color: #4ec9b0; }
        .log-entry {
            padding: 10px;
            margin: 5px 0;
            background: #252526;
            border-left: 3px solid #007acc;
            border-radius: 3px;
        }
        .success { border-left-color: #4ec9b0; }
        .error { border-left-color: #f48771; }
        .info { border-left-color: #dcdcaa; }
        button {
            padding: 10px 20px;
            margin: 10px 5px;
            background: #007acc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: monospace;
        }
        button:hover { background: #005a9e; }
        .clear-btn { background: #f48771; }
        .clear-btn:hover { background: #d16969; }
        pre {
            background: #1e1e1e;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .stats {
            background: #252526;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>📋 API Debug Log</h1>
    
    <?php
    $log_file = sys_get_temp_dir() . '/recipe_api_debug.log';
    $trace_log = sys_get_temp_dir() . '/recipe_debug_trace.log';
    
    if (isset($_GET['clear'])) {
        if (file_exists($log_file)) {
            unlink($log_file);
        }
        if (file_exists($trace_log)) {
            unlink($trace_log);
        }
        echo '<p style="color: #4ec9b0;">✅ Logs cleared!</p>';
    }
    
    if (isset($_GET['test'])) {
        // Make a test request
        file_put_contents($log_file, date('Y-m-d H:i:s') . " [TEST] Testing logging functionality\n", FILE_APPEND);
        echo '<p style="color: #4ec9b0;">✅ Test entry added to log!</p>';
    }
    ?>
    
    <div>
        <button onclick="location.reload()">🔄 Refresh</button>
        <button onclick="location.href='?test=1'">🧪 Add Test Entry</button>
        <button class="clear-btn" onclick="if(confirm('Clear log?')) location.href='?clear=1'">🗑️ Clear Log</button>
        <button onclick="window.open('test_save_api.html')">🚀 Open Test Page</button>
    </div>
    
    <div class="stats">
        <strong>Log File:</strong> <code><?= htmlspecialchars($log_file) ?></code><br>
        <strong>Exists:</strong> <?= file_exists($log_file) ? '✅ Yes' : '❌ No' ?><br>
        <?php if (file_exists($log_file)): ?>
            <strong>Size:</strong> <?= number_format(filesize($log_file)) ?> bytes<br>
            <strong>Modified:</strong> <?= date('Y-m-d H:i:s', filemtime($log_file)) ?>
        <?php endif; ?>
    </div>
    
    <h2>📝 Log Entries (last 100 lines)</h2>
    
    <?php
    if (file_exists($log_file)) {
        $lines = file($log_file);
        $lines = array_slice($lines, -100); // Last 100 lines
        $lines = array_reverse($lines); // Most recent first
        
        if (empty($lines)) {
            echo '<p style="color: #dcdcaa;">No log entries yet. Make an API request to see logs here.</p>';
        } else {
            echo '<div style="font-size: 12px;">';
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                $class = 'log-entry';
                if (strpos($line, 'ERROR') !== false || strpos($line, '[5') !== false) {
                    $class .= ' error';
                } elseif (strpos($line, 'success') !== false || strpos($line, '[200]') !== false) {
                    $class .= ' success';
                } elseif (strpos($line, 'OUTPUT:') !== false) {
                    $class .= ' info';
                }
                
                echo '<div class="' . $class . '">' . htmlspecialchars($line) . '</div>';
            }
            echo '</div>';
        }
    } else {
        echo '<p style="color: #dcdcaa;">📭 Log file does not exist yet. Make an API request first.</p>';
        echo '<p>The log will be created at: <code>' . htmlspecialchars($log_file) . '</code></p>';
    }
    ?>
    
    <hr style="margin: 30px 0; border: 1px solid #3e3e42;">
    
    <h2>🧪 Quick Test</h2>
    <p>Click the button below to test the API directly from this page:</p>
    
    <button onclick="testAPI()">🚀 Test Save Recipe API</button>
    <div id="test-output" style="margin-top: 10px;"></div>
    
    <script>
        async function testAPI() {
            const output = document.getElementById('test-output');
            output.innerHTML = '<p style="color: #dcdcaa;">⏳ Testing...</p>';
            
            try {
                const response = await fetch('api/save_ai_recipe.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        title: 'Test Recipe ' + Date.now(),
                        cuisine: 'italian',
                        ingredients: '1 cup flour\n2 eggs',
                        steps: 'Step 1\nStep 2'
                    })
                });
                
                const text = await response.text();
                console.log('Response:', text);
                
                output.innerHTML = '<p style="color: #4ec9b0;">✅ Response received! Check log above.</p>' +
                    '<pre>' + text + '</pre>' +
                    '<p><button onclick="location.reload()">🔄 Refresh to see log</button></p>';
                    
            } catch (error) {
                output.innerHTML = '<p style="color: #f48771;">❌ Error: ' + error.message + '</p>';
            }
        }
    </script>
</body>
</html>

