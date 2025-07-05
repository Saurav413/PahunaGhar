<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPIN Visibility Demo</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap">
    <style>
        body {
            background: #23272f;
            min-height: 100vh;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            color: #f3f4f6;
            padding: 20px;
        }
        .demo-container {
            max-width: 600px;
            margin: 0 auto;
            background: #2d323c;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            padding: 40px;
        }
        .demo-title {
            color: #fff;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .demo-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #353945;
            border-radius: 12px;
        }
        .demo-section h3 {
            color: #10b981;
            margin-bottom: 15px;
        }
        .mpin-input-container {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }
        .mpin-input {
            width: 100%;
            padding: 12px 16px;
            padding-right: 50px;
            border-radius: 8px;
            border: 1.5px solid #353945;
            background: #2d323c;
            color: #f3f4f6;
            font-size: 1.1rem;
            outline: none;
            box-sizing: border-box;
        }
        .toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #b0b3b8;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 4px;
            transition: color 0.2s;
        }
        .toggle-btn:hover {
            color: #10b981;
        }
        .feature-list {
            background: #1f2937;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .feature-list h4 {
            color: #10b981;
            margin-bottom: 10px;
        }
        .feature-list ul {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 5px 0;
            color: #d1d5db;
        }
        .feature-list li:before {
            content: "✅ ";
            color: #10b981;
        }
        .test-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .test-btn {
            background: #10b981;
            color: #23272f;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .test-btn:hover {
            background: #059669;
        }
        .test-btn.secondary {
            background: #7c3aed;
        }
        .test-btn.secondary:hover {
            background: #5b21b6;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="demo-title">👁️ MPIN Visibility Demo</div>
        
        <div class="demo-section">
            <h3>eSewa Style MPIN Input</h3>
            <div class="mpin-input-container">
                <input type="password" id="esewaDemoInput" class="mpin-input" placeholder="Enter MPIN" maxlength="10">
                <button type="button" id="esewaToggleBtn" class="toggle-btn" onclick="toggleMpinVisibility('esewaDemoInput', 'esewaToggleBtn')" title="Show MPIN">👁️</button>
            </div>
            <small style="color: #b0b3b8;">Try clicking the eye icon to show/hide the MPIN</small>
        </div>
        
        <div class="demo-section">
            <h3>Khalti Style MPIN Input</h3>
            <div class="mpin-input-container">
                <input type="password" id="khaltiDemoInput" class="mpin-input" placeholder="Enter MPIN" maxlength="10">
                <button type="button" id="khaltiToggleBtn" class="toggle-btn" onclick="toggleMpinVisibility('khaltiDemoInput', 'khaltiToggleBtn')" title="Show MPIN">👁️</button>
            </div>
            <small style="color: #b0b3b8;">Try clicking the eye icon to show/hide the MPIN</small>
        </div>
        
        <div class="feature-list">
            <h4>✨ New Features Added:</h4>
            <ul>
                <li>Show/Hide MPIN toggle button (👁️/🙈)</li>
                <li>Visual feedback with eye icons</li>
                <li>Hover effects for better UX</li>
                <li>Tooltip showing current state</li>
                <li>Consistent styling across all payment pages</li>
                <li>Mobile-friendly responsive design</li>
            </ul>
        </div>
        
        <div class="test-buttons">
            <a href="esewa.php" class="test-btn">Test eSewa Payment</a>
            <a href="khalti.php" class="test-btn secondary">Test Khalti Payment</a>
            <a href="test_complete_flow.php" class="test-btn">Test Complete Flow</a>
        </div>
    </div>

    <script>
        function toggleMpinVisibility(inputId, buttonId) {
            var mpinInput = document.getElementById(inputId);
            var toggleBtn = document.getElementById(buttonId);
            
            if (mpinInput.type === 'password') {
                mpinInput.type = 'text';
                toggleBtn.textContent = '👁️';
                toggleBtn.title = 'Hide MPIN';
            } else {
                mpinInput.type = 'password';
                toggleBtn.textContent = '👁️';
                toggleBtn.title = 'Show MPIN';
            }
        }
        
        // Add some demo functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-fill demo MPINs after 2 seconds
            setTimeout(function() {
                document.getElementById('esewaDemoInput').value = '5470';
                document.getElementById('khaltiDemoInput').value = '2020';
            }, 2000);
        });
    </script>
</body>
</html> 