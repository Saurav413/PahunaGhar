<?php
session_start();
require_once 'config.php';

// Test credentials
$test_esewa_id = "9745869500";
$test_esewa_mpin = "5470";
$test_khalti_id = "9824004077";
$test_khalti_mpin = "2020";

// Simulate login
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['esewa_id'] = $test_esewa_id;
$_SESSION['khalti_id'] = $test_khalti_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPIN Flow Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #0056b3; }
        input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 5px;
        }
        .result {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <h1>MPIN Verification Flow Test</h1>
    
    <div class="test-section">
        <h2>Session Status</h2>
        <div class="result">
            <strong>Session Variables:</strong><br>
            logged_in: <?php echo $_SESSION['logged_in'] ? 'true' : 'false'; ?><br>
            user_id: <?php echo $_SESSION['user_id'] ?? 'NOT SET'; ?><br>
            esewa_id: <?php echo $_SESSION['esewa_id'] ?? 'NOT SET'; ?><br>
            khalti_id: <?php echo $_SESSION['khalti_id'] ?? 'NOT SET'; ?><br>
        </div>
    </div>

    <div class="test-section">
        <h2>eSewa MPIN Test</h2>
        <p>Test eSewa ID: <?php echo $test_esewa_id; ?></p>
        <p>Test MPIN: <?php echo $test_esewa_mpin; ?></p>
        
        <input type="password" id="esewaMpinInput" placeholder="Enter eSewa MPIN" value="<?php echo $test_esewa_mpin; ?>">
        <button onclick="testEsewaMpin()">Test eSewa MPIN</button>
        <div id="esewaResult" class="result" style="display:none;"></div>
    </div>

    <div class="test-section">
        <h2>Khalti MPIN Test</h2>
        <p>Test Khalti ID: <?php echo $test_khalti_id; ?></p>
        <p>Test MPIN: <?php echo $test_khalti_mpin; ?></p>
        
        <input type="password" id="khaltiMpinInput" placeholder="Enter Khalti MPIN" value="<?php echo $test_khalti_mpin; ?>">
        <button onclick="testKhaltiMpin()">Test Khalti MPIN</button>
        <div id="khaltiResult" class="result" style="display:none;"></div>
    </div>

    <div class="test-section">
        <h2>Direct PHP Test</h2>
        <button onclick="testDirectPhp()">Test Direct PHP Verification</button>
        <div id="directResult" class="result" style="display:none;"></div>
    </div>

    <div class="test-section">
        <h2>Database Connection Test</h2>
        <button onclick="testDatabase()">Test Database Connection</button>
        <div id="dbResult" class="result" style="display:none;"></div>
    </div>

    <script>
        function testEsewaMpin() {
            const mpin = document.getElementById('esewaMpinInput').value;
            const resultDiv = document.getElementById('esewaResult');
            
            resultDiv.innerHTML = 'Testing...';
            resultDiv.style.display = 'block';
            resultDiv.className = 'result info';
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_esewa_mpin.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log('eSewa Response:', xhr.responseText);
                    console.log('eSewa Status:', xhr.status);
                    
                    if (xhr.status === 200) {
                        if (xhr.responseText.trim() === 'success') {
                            resultDiv.innerHTML = '<span class="success">✅ eSewa MPIN verification: SUCCESS</span>';
                            resultDiv.className = 'result success';
                        } else {
                            resultDiv.innerHTML = '<span class="error">❌ eSewa MPIN verification: FAILED</span><br>Response: ' + xhr.responseText;
                            resultDiv.className = 'result error';
                        }
                    } else {
                        resultDiv.innerHTML = '<span class="error">❌ HTTP Error: ' + xhr.status + '</span><br>Response: ' + xhr.responseText;
                        resultDiv.className = 'result error';
                    }
                }
            };
            
            xhr.send('mpin=' + encodeURIComponent(mpin));
        }

        function testKhaltiMpin() {
            const mpin = document.getElementById('khaltiMpinInput').value;
            const resultDiv = document.getElementById('khaltiResult');
            
            resultDiv.innerHTML = 'Testing...';
            resultDiv.style.display = 'block';
            resultDiv.className = 'result info';
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_khalti_mpin.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log('Khalti Response:', xhr.responseText);
                    console.log('Khalti Status:', xhr.status);
                    
                    if (xhr.status === 200) {
                        if (xhr.responseText.trim() === 'success') {
                            resultDiv.innerHTML = '<span class="success">✅ Khalti MPIN verification: SUCCESS</span>';
                            resultDiv.className = 'result success';
                        } else {
                            resultDiv.innerHTML = '<span class="error">❌ Khalti MPIN verification: FAILED</span><br>Response: ' + xhr.responseText;
                            resultDiv.className = 'result error';
                        }
                    } else {
                        resultDiv.innerHTML = '<span class="error">❌ HTTP Error: ' + xhr.status + '</span><br>Response: ' + xhr.responseText;
                        resultDiv.className = 'result error';
                    }
                }
            };
            
            xhr.send('mpin=' + encodeURIComponent(mpin));
        }

        function testDirectPhp() {
            const resultDiv = document.getElementById('directResult');
            resultDiv.innerHTML = 'Testing direct PHP verification...';
            resultDiv.style.display = 'block';
            resultDiv.className = 'result info';
            
            // Make a request to our debug page
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'debug_mpin_live.php', true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resultDiv.innerHTML = '<span class="success">✅ Direct PHP test completed</span><br><br>Check the debug page for detailed information.';
                        resultDiv.className = 'result success';
                    } else {
                        resultDiv.innerHTML = '<span class="error">❌ Direct PHP test failed</span>';
                        resultDiv.className = 'result error';
                    }
                }
            };
            
            xhr.send();
        }

        function testDatabase() {
            const resultDiv = document.getElementById('dbResult');
            resultDiv.innerHTML = 'Testing database connection...';
            resultDiv.style.display = 'block';
            resultDiv.className = 'result info';
            
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'debug_mpin_issue.php', true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resultDiv.innerHTML = '<span class="success">✅ Database test completed</span><br><br>Check the debug page for database information.';
                        resultDiv.className = 'result success';
                    } else {
                        resultDiv.innerHTML = '<span class="error">❌ Database test failed</span>';
                        resultDiv.className = 'result error';
                    }
                }
            };
            
            xhr.send();
        }
    </script>
</body>
</html> 