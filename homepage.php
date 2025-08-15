<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css?v=1.1">
    <style>
        .hotel-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .book-btn, .reviews-btn {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .book-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        .reviews-btn {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        .book-btn:hover, .reviews-btn:hover {
            transform: translateY(-2px);
        }
        
        /* Circular Hotel Display Styles */
        .circular-hotel-container {
            position: relative;
            width: 100%;
            height: 600px;
            overflow: hidden;
            margin: 40px 0;
            background: radial-gradient(circle at center, rgba(37, 99, 235, 0.05) 0%, transparent 70%);
        }
        
        /* Enhanced Background Elements */
        .circular-hotel-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
            z-index: 1;
        }
        
        /* Animated Background Shapes */
        .background-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            overflow: hidden;
        }
        
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(37, 99, 235, 0.1), rgba(236, 72, 153, 0.1));
            animation: float 6s ease-in-out infinite;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 8s;
        }
        
        .floating-shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 20%;
            right: 15%;
            animation-delay: 2s;
            animation-duration: 10s;
            background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(37, 99, 235, 0.1));
        }
        
        .floating-shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
            animation-duration: 7s;
            background: linear-gradient(45deg, rgba(236, 72, 153, 0.1), rgba(16, 185, 129, 0.1));
        }
        
        .floating-shape:nth-child(4) {
            width: 100px;
            height: 100px;
            bottom: 15%;
            right: 10%;
            animation-delay: 1s;
            animation-duration: 9s;
        }
        
        .floating-shape:nth-child(5) {
            width: 40px;
            height: 40px;
            top: 50%;
            left: 5%;
            animation-delay: 3s;
            animation-duration: 6s;
            background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(236, 72, 153, 0.1));
        }
        
        .floating-shape:nth-child(6) {
            width: 70px;
            height: 70px;
            top: 60%;
            right: 5%;
            animation-delay: 5s;
            animation-duration: 8s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.7;
            }
            25% {
                transform: translateY(-20px) rotate(90deg);
                opacity: 1;
            }
            50% {
                transform: translateY(-10px) rotate(180deg);
                opacity: 0.8;
            }
            75% {
                transform: translateY(-30px) rotate(270deg);
                opacity: 0.9;
            }
        }
        
        /* Animated Grid Pattern */
        .grid-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(rgba(37, 99, 235, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37, 99, 235, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            z-index: 1;
        }
        
        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }
        
        /* Glowing Orbs */
        .glowing-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(20px);
            animation: glow 4s ease-in-out infinite alternate;
        }
        
        .glowing-orb:nth-child(1) {
            width: 200px;
            height: 200px;
            top: -100px;
            left: -100px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.3) 0%, transparent 70%);
            animation-delay: 0s;
        }
        
        .glowing-orb:nth-child(2) {
            width: 150px;
            height: 150px;
            top: -75px;
            right: -75px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.3) 0%, transparent 70%);
            animation-delay: 2s;
        }
        
        .glowing-orb:nth-child(3) {
            width: 180px;
            height: 180px;
            bottom: -90px;
            left: -90px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            animation-delay: 1s;
        }
        
        @keyframes glow {
            0% {
                opacity: 0.5;
                transform: scale(1);
            }
            100% {
                opacity: 0.8;
                transform: scale(1.1);
            }
        }
        
        /* Particle System */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(37, 99, 235, 0.6);
            border-radius: 50%;
            animation: particleFloat 15s linear infinite;
        }
        
        .particle:nth-child(odd) {
            background: rgba(236, 72, 153, 0.6);
            animation-duration: 12s;
        }
        
        .particle:nth-child(3n) {
            background: rgba(16, 185, 129, 0.6);
            animation-duration: 18s;
        }
        
        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) translateX(100px);
                opacity: 0;
            }
        }
        
        /* Generate particles dynamically */
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 14s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 16s; }
        .particle:nth-child(10) { left: 95%; animation-delay: 18s; }
        
        .circular-hotel-display {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        .hotel-card-circular {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 
                0 8px 32px rgba(44, 62, 80, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            width: 300px;
            height: 450px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: scale(0.8) translateY(50px);
            pointer-events: none;
        }
        
        .hotel-card-circular.active {
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: all;
            z-index: 10;
            box-shadow: 
                0 20px 60px rgba(44, 62, 80, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.2),
                0 0 40px rgba(37, 99, 235, 0.1);
        }
        
        .hotel-card-circular.prev {
            opacity: 0.7;
            transform: scale(0.9) translateX(-200px) translateY(-20px);
            z-index: 5;
        }
        
        .hotel-card-circular.next {
            opacity: 0.7;
            transform: scale(0.9) translateX(200px) translateY(-20px);
            z-index: 5;
        }
        
        .hotel-card-circular.far-prev {
            opacity: 0.3;
            transform: scale(0.7) translateX(-400px) translateY(-40px);
            z-index: 1;
        }
        
        .hotel-card-circular.far-next {
            opacity: 0.3;
            transform: scale(0.7) translateX(400px) translateY(-40px);
            z-index: 1;
        }
        
        .hotel-image-circular {
            width: 100%;
            height: 180px;
            object-fit: cover;
            transition: filter 0.3s;
        }
        
        .hotel-card-circular:hover .hotel-image-circular {
            filter: brightness(0.95) saturate(1.2);
        }
        
        .hotel-content-circular {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .hotel-name-circular {
            font-size: 1.2em;
            font-weight: 700;
            color: #1a2636;
            line-height: 1.3;
            margin: 0;
        }
        
        .hotel-description-circular {
            font-size: 0.9em;
            color: #4a5a6a;
            line-height: 1.4;
            margin: 0;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .hotel-bottom-row-circular {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            gap: 10px;
        }
        
        .hotel-rating-circular {
            font-size: 1em;
            color: #f7b731;
            font-weight: 600;
            margin: 0;
        }
        
        .hotel-price-circular {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
            box-shadow: 0 2px 8px rgba(39,174,96,0.13);
        }
        
        .hotel-actions-circular {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        
        .book-btn-circular, .reviews-btn-circular {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            font-size: 0.85em;
        }
        
        .book-btn-circular {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .reviews-btn-circular {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .book-btn-circular:hover, .reviews-btn-circular:hover {
            transform: translateY(-2px);
        }
        
        /* Navigation Controls */
        .hotel-navigation {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 20;
        }
        
        .nav-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .nav-dot.active {
            background: #2563eb;
            transform: scale(1.2);
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.5);
        }
        
        .nav-dot:hover {
            background: #2563eb;
            transform: scale(1.1);
        }
        
        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            z-index: 20;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(37, 99, 235, 0.2);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.1);
        }
        
        /* Hotel Counter */
        .hotel-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            z-index: 20;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(37, 99, 235, 0.2);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.1);
        }
        
        /* Search Suggestions Styles */
        .search-bar {
            position: relative;
        }
        
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .suggestion-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
        
        .suggestion-item.selected {
            background-color: #e3f2fd;
        }
        
        .suggestion-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            font-weight: bold;
        }
        
        .suggestion-icon.hotel {
            background-color: #3498db;
        }
        
        .suggestion-icon.location {
            background-color: #27ae60;
        }
        
        .suggestion-icon.price {
            background-color: #f39c12;
        }
        
        .suggestion-text {
            flex: 1;
            font-size: 14px;
        }
        
        .no-suggestions {
            padding: 16px;
            text-align: center;
            color: #666;
            font-style: italic;
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background: #181a20 !important;
            color: #f3f4f6 !important;
        }
        body.dark-mode .navbar,
        body.dark-mode .container,
        body.dark-mode .hotel-card,
        body.dark-mode .bookings-panel {
            background: #23272f !important;
            color: #f3f4f6 !important;
            box-shadow: 0 4px 24px rgba(16, 24, 40, 0.22) !important;
        }
        body.dark-mode .nav-link,
        body.dark-mode .logo {
            color: #e0eafc !important;
        }
        body.dark-mode .logo span,
        body.dark-mode .logo {
            color: #10b981 !important;
        }
        body.dark-mode .hotel-card {
            border: 1.5px solid #353945 !important;
        }
        body.dark-mode .hotel-name,
        body.dark-mode .hotel-description,
        body.dark-mode .hotel-rating,
        body.dark-mode .hotel-price {
            color: #f3f4f6 !important;
        }
        body.dark-mode .book-btn {
            background: linear-gradient(135deg, #10b981, #2563eb) !important;
            color: #fff !important;
        }
        body.dark-mode .reviews-btn {
            background: linear-gradient(135deg, #a78bfa, #7c3aed) !important;
            color: #fff !important;
        }
        body.dark-mode .book-btn:hover, body.dark-mode .reviews-btn:hover {
            filter: brightness(1.15);
        }
        body.dark-mode .search-bar input,
        body.dark-mode .search-bar button {
            background: #23272f !important;
            color: #e0eafc !important;
            border: 1.5px solid #353945 !important;
        }
        body.dark-mode .search-suggestions {
            background: #23272f !important;
            color: #e0eafc !important;
            border: 1.5px solid #353945 !important;
        }
        body.dark-mode .suggestion-item {
            background: #23272f !important;
            color: #e0eafc !important;
        }
        body.dark-mode .suggestion-item:hover {
            background: #2563eb !important;
            color: #fff !important;
        }
        body.dark-mode .footer-highlight {
            color: #10b981 !important;
        }
        body.dark-mode .welcome-title, body.dark-mode .welcome-message {
            color: #fff !important;
        }
        
        /* Dark mode for circular display */
        body.dark-mode .hotel-card-circular {
            background: rgba(35, 39, 47, 0.95) !important;
            color: #f3f4f6 !important;
            border: 1.5px solid rgba(53, 57, 69, 0.5) !important;
            backdrop-filter: blur(20px) !important;
        }
        body.dark-mode .hotel-name-circular,
        body.dark-mode .hotel-description-circular {
            color: #f3f4f6 !important;
        }
        body.dark-mode .scroll-indicator,
        body.dark-mode .hotel-counter {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
            border: 1px solid rgba(16, 185, 129, 0.2) !important;
        }
        
        /* Dark mode background effects */
        body.dark-mode .circular-hotel-container::before {
            background: 
                radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(37, 99, 235, 0.05) 0%, transparent 50%) !important;
        }
        
        body.dark-mode .floating-shape {
            background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(124, 58, 237, 0.1)) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode .floating-shape:nth-child(2) {
            background: linear-gradient(45deg, rgba(37, 99, 235, 0.1), rgba(16, 185, 129, 0.1)) !important;
        }
        
        body.dark-mode .floating-shape:nth-child(3) {
            background: linear-gradient(45deg, rgba(124, 58, 237, 0.1), rgba(37, 99, 235, 0.1)) !important;
        }
        
        body.dark-mode .floating-shape:nth-child(5) {
            background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(124, 58, 237, 0.1)) !important;
        }
        
        body.dark-mode .grid-pattern {
            background-image: 
                linear-gradient(rgba(16, 185, 129, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16, 185, 129, 0.03) 1px, transparent 1px) !important;
        }
        
        body.dark-mode .glowing-orb:nth-child(1) {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%) !important;
        }
        
        body.dark-mode .glowing-orb:nth-child(2) {
            background: radial-gradient(circle, rgba(124, 58, 237, 0.3) 0%, transparent 70%) !important;
        }
        
        body.dark-mode .glowing-orb:nth-child(3) {
            background: radial-gradient(circle, rgba(37, 99, 235, 0.3) 0%, transparent 70%) !important;
        }
        
        body.dark-mode .particle {
            background: rgba(16, 185, 129, 0.6) !important;
        }
        
        body.dark-mode .particle:nth-child(odd) {
            background: rgba(124, 58, 237, 0.6) !important;
        }
        
        body.dark-mode .particle:nth-child(3n) {
            background: rgba(37, 99, 235, 0.6) !important;
        }
        
        body.dark-mode .nav-dot {
            background: rgba(16, 185, 129, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode .nav-dot.active {
            background: #10b981 !important;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5) !important;
        }
        
        body.dark-mode .nav-dot:hover {
            background: #10b981 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo">Pahuna<span style="color:#2563eb;">Ghar</span></a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <?php if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'super_admin'])): ?>
                    <a href="user_bookings.php" class="nav-link">My Bookings</a>
                <?php endif; ?>
                <a href="lets_chat.php" class="nav-link">Let's Chat</a>
            <?php endif; ?>
        </div>
        <div class="navbar-center">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search destinations, hotels, or prices...">
                <button id="searchBtn">Search</button>
                <div id="searchSuggestions" class="search-suggestions"></div>
            </div>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <span style="color: #2563eb; font-weight: 500; margin-right: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <?php if (isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['admin', 'super_admin'])): ?>
                    <a href="<?php echo $_SESSION['user_type'] === 'super_admin' ? 'super_admin_dashboard.php' : 'admin_dashboard.php'; ?>" class="nav-link">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
            <button id="themeToggle" style="margin-left:18px;padding:8px 16px;border-radius:6px;border:none;cursor:pointer;font-weight:600;">🌙 Dark Mode</button>
        </div>
    </nav>
    <header class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Find your next stay</h1>
            <p>Discover unique places to stay, from cozy cottages to luxury hotels.</p>
            <a href="#hotel-listings" class="browse-btn">Browse Listings</a>
        </div>
    </header>
    <div class="container">
        <h1 id="hotel-listings">Welcome to the Hotel Booking System</h1>
        <div id="searchResults" style="display: none;">
            <h2>Search Results</h2>
            <button id="showAllBtn" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">Show All Hotels</button>
        </div>
        
        <!-- Circular Hotel Display -->
        <div class="circular-hotel-container">
            <!-- Background Effects -->
            <div class="grid-pattern"></div>
            <div class="background-shapes">
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
            </div>
            <div class="glowing-orb"></div>
            <div class="glowing-orb"></div>
            <div class="glowing-orb"></div>
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            
            <div class="scroll-indicator">🖱️ Scroll to explore hotels</div>
            <div class="hotel-counter">1 / <span id="totalHotels">0</span></div>
            <div class="circular-hotel-display" id="circularHotelDisplay">
                <!-- Hotel cards will be dynamically inserted here -->
            </div>
            <div class="hotel-navigation" id="hotelNavigation">
                <!-- Navigation dots will be dynamically inserted here -->
            </div>
        </div>
        
        <!-- Fallback grid display for mobile -->
        <div id="hotel-list" class="hotel-list" style="display: none;">
            <!-- Hotel listings will be loaded here -->
        </div>
    </div>
    <footer style="width:100%;background:#e6e7eb;padding:28px 0 18px 0;text-align:center;font-size:1.08em;color:#222;letter-spacing:0.5px;margin-top:48px;box-shadow:0 -2px 8px rgba(44,62,80,0.04);font-family:'Montserrat',Arial,sans-serif;">
        © 2025 <span style="font-weight:700;color:#2563eb;">PahunaGhar</span>. All rights reserved.
    </footer>

    <script>
        let allHotels = [];
        let currentHotelIndex = 0;
        let isMobile = window.innerWidth <= 768;

        // Function to create circular hotel card HTML
        function createCircularHotelCard(hotel, index) {
            const reviewCount = hotel.review_count || 0;
            let avgRating = hotel.avg_rating;
            if (avgRating === null || avgRating === undefined || isNaN(Number(avgRating))) {
                avgRating = hotel.rating || 0;
            }
            avgRating = Number(avgRating);
            const reviewText = reviewCount > 0 ? `(${reviewCount} reviews)` : '(No reviews yet)';
            
            return `
                <div class="hotel-card-circular" data-index="${index}">
                    <img class="hotel-image-circular" src="${hotel.image_url}" alt="${hotel.name}" 
                         onerror="this.src='https://via.placeholder.com/300x180?text=Hotel+Image'">
                    <div class="hotel-content-circular">
                        <h3 class="hotel-name-circular">${hotel.name}</h3>
                        <p class="hotel-description-circular">${hotel.description}</p>
                        <div class="hotel-bottom-row-circular">
                            <div class="hotel-rating-circular">&#9733; ${avgRating.toFixed(1)}/5 ${reviewText}</div>
                            <div class="hotel-price-circular">${hotel.price}</div>
                        </div>
                        <div class="hotel-actions-circular">
                            <button onclick="window.location.href='booking.php?id=${hotel.id}'" class="book-btn-circular">Book Now</button>
                            <button onclick="window.location.href='hotel_reviews.php?id=${hotel.id}'" class="reviews-btn-circular">Reviews</button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Function to create regular hotel card HTML (for mobile fallback)
        function createHotelCard(hotel) {
            const reviewCount = hotel.review_count || 0;
            let avgRating = hotel.avg_rating;
            if (avgRating === null || avgRating === undefined || isNaN(Number(avgRating))) {
                avgRating = hotel.rating || 0;
            }
            avgRating = Number(avgRating);
            const reviewText = reviewCount > 0 ? `(${reviewCount} reviews)` : '(No reviews yet)';
            
            return `
                <div class="hotel-card">
                    <img class="hotel-image" src="${hotel.image_url}" alt="${hotel.name}" 
                         onerror="this.src='https://via.placeholder.com/250x150?text=Hotel+Image'">
                    <div class="hotel-content">
                        <div class="hotel-name">${hotel.name}</div>
                        <div class="hotel-description">${hotel.description}</div>
                        <div class="hotel-bottom-row">
                            <div class="hotel-rating">&#9733; ${avgRating.toFixed(1)}/5 ${reviewText}</div>
                            <div class="hotel-price">${hotel.price}</div>
                        </div>
                        <div class="hotel-actions">
                            <button onclick="window.location.href='booking.php?id=${hotel.id}'" class="book-btn">Book Now</button>
                            <button onclick="window.location.href='hotel_reviews.php?id=${hotel.id}'" class="reviews-btn">View Reviews</button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Function to update circular display
        function updateCircularDisplay() {
            const display = document.getElementById('circularHotelDisplay');
            const counter = document.getElementById('totalHotels');
            const navigation = document.getElementById('hotelNavigation');
            
            if (allHotels.length === 0) return;
            
            // Update counter
            counter.textContent = allHotels.length;
            
            // Clear existing content
            display.innerHTML = '';
            navigation.innerHTML = '';
            
            // Create hotel cards
            allHotels.forEach((hotel, index) => {
                const card = document.createElement('div');
                card.innerHTML = createCircularHotelCard(hotel, index);
                display.appendChild(card.firstElementChild);
                
                // Create navigation dot
                const dot = document.createElement('div');
                dot.className = 'nav-dot';
                dot.onclick = () => goToHotel(index);
                navigation.appendChild(dot);
            });
            
            // Set initial positions
            updateHotelPositions();
        }

        // Function to update hotel positions based on current index
        function updateHotelPositions() {
            const cards = document.querySelectorAll('.hotel-card-circular');
            const dots = document.querySelectorAll('.nav-dot');
            
            cards.forEach((card, index) => {
                const diff = index - currentHotelIndex;
                card.className = 'hotel-card-circular';
                
                if (diff === 0) {
                    card.classList.add('active');
                } else if (diff === -1 || (currentHotelIndex === 0 && index === cards.length - 1)) {
                    card.classList.add('prev');
                } else if (diff === 1 || (currentHotelIndex === cards.length - 1 && index === 0)) {
                    card.classList.add('next');
                } else if (diff === -2 || (currentHotelIndex === 0 && index === cards.length - 2) || (currentHotelIndex === 1 && index === cards.length - 1)) {
                    card.classList.add('far-prev');
                } else if (diff === 2 || (currentHotelIndex === cards.length - 1 && index === 1) || (currentHotelIndex === cards.length - 2 && index === 0)) {
                    card.classList.add('far-next');
                }
            });
            
            // Update navigation dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentHotelIndex);
            });
            
            // Update counter
            document.querySelector('.hotel-counter').textContent = `${currentHotelIndex + 1} / ${allHotels.length}`;
        }

        // Function to go to specific hotel
        function goToHotel(index) {
            currentHotelIndex = index;
            updateHotelPositions();
        }

        // Function to go to next hotel
        function nextHotel() {
            currentHotelIndex = (currentHotelIndex + 1) % allHotels.length;
            updateHotelPositions();
        }

        // Function to go to previous hotel
        function prevHotel() {
            currentHotelIndex = (currentHotelIndex - 1 + allHotels.length) % allHotels.length;
            updateHotelPositions();
        }

        // Function to display hotels (for mobile fallback)
        function displayHotels(hotels) {
            const hotelList = document.getElementById('hotel-list');
            hotelList.innerHTML = hotels.length > 0
                ? hotels.map(hotel => createHotelCard(hotel)).join('')
                : '<div class="no-hotels">No hotels found</div>';
        }

        // Fetch hotels from the database on page load
        function loadHotelsFromDB() {
            fetch('public_api.php?action=get_hotels')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.hotels.length > 0) {
                        allHotels = data.hotels;
                        if (isMobile) {
                            displayHotels(allHotels);
                            document.getElementById('hotel-list').style.display = 'grid';
                            document.querySelector('.circular-hotel-container').style.display = 'none';
                        } else {
                            updateCircularDisplay();
                        }
                    } else {
                        document.getElementById('hotel-list').innerHTML = '<div class="no-hotels">No hotels found</div>';
                        document.querySelector('.circular-hotel-container').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading hotels:', error);
                    document.getElementById('hotel-list').innerHTML = '<div class="no-hotels">Error loading hotels</div>';
                    document.querySelector('.circular-hotel-container').style.display = 'none';
                });
        }

        function filterHotels(searchTerm) {
            searchTerm = searchTerm.trim().toLowerCase();
            if (!searchTerm) {
                if (isMobile) {
                    displayHotels(allHotels);
                } else {
                    updateCircularDisplay();
                }
                return;
            }
            const filtered = allHotels.filter(hotel =>
                (hotel.name && hotel.name.toLowerCase().includes(searchTerm)) ||
                (hotel.location && hotel.location.toLowerCase().includes(searchTerm)) ||
                (hotel.price && hotel.price.toLowerCase().includes(searchTerm))
            );
            if (isMobile) {
                displayHotels(filtered);
            } else {
                // For circular display, we'll show filtered results in the same format
                const tempHotels = [...filtered];
                allHotels = tempHotels;
                currentHotelIndex = 0;
                updateCircularDisplay();
            }
        }

        // Search suggestions functionality
        let searchTimeout;
        let selectedSuggestionIndex = -1;
        let currentSuggestions = [];

        function getSearchSuggestions(searchTerm) {
            if (searchTerm.length < 2) {
                hideSuggestions();
                return;
            }

            fetch(`search_suggestions.php?q=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.suggestions && data.suggestions.length > 0) {
                        showSuggestions(data.suggestions);
                    } else {
                        showNoSuggestions();
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    hideSuggestions();
                });
        }

        function showSuggestions(suggestions) {
            const suggestionsContainer = document.getElementById('searchSuggestions');
            currentSuggestions = suggestions;
            selectedSuggestionIndex = -1;

            const suggestionsHTML = suggestions.map((suggestion, index) => {
                const iconText = suggestion.type === 'hotel' ? 'H' : 
                                suggestion.type === 'location' ? 'L' : 'P';
                return `
                    <div class="suggestion-item" data-index="${index}" onclick="selectSuggestion('${suggestion.suggestion}')">
                        <div class="suggestion-icon ${suggestion.type}">${iconText}</div>
                        <div class="suggestion-text">${suggestion.display_text}</div>
                    </div>
                `;
            }).join('');

            suggestionsContainer.innerHTML = suggestionsHTML;
            suggestionsContainer.style.display = 'block';
        }

        function showNoSuggestions() {
            const suggestionsContainer = document.getElementById('searchSuggestions');
            suggestionsContainer.innerHTML = '<div class="no-suggestions">No suggestions found</div>';
            suggestionsContainer.style.display = 'block';
        }

        function hideSuggestions() {
            const suggestionsContainer = document.getElementById('searchSuggestions');
            suggestionsContainer.style.display = 'none';
            currentSuggestions = [];
            selectedSuggestionIndex = -1;
        }

        function selectSuggestion(suggestion) {
            document.getElementById('searchInput').value = suggestion;
            hideSuggestions();
            filterHotels(suggestion);
        }

        function handleKeyNavigation(e) {
            const suggestionsContainer = document.getElementById('searchSuggestions');
            if (suggestionsContainer.style.display === 'none') return;

            const suggestionItems = suggestionsContainer.querySelectorAll('.suggestion-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedSuggestionIndex = Math.min(selectedSuggestionIndex + 1, suggestionItems.length - 1);
                updateSelectedSuggestion(suggestionItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedSuggestionIndex = Math.max(selectedSuggestionIndex - 1, -1);
                updateSelectedSuggestion(suggestionItems);
            } else if (e.key === 'Enter' && selectedSuggestionIndex >= 0) {
                e.preventDefault();
                const selectedSuggestion = currentSuggestions[selectedSuggestionIndex];
                if (selectedSuggestion) {
                    selectSuggestion(selectedSuggestion.suggestion);
                }
            } else if (e.key === 'Escape') {
                hideSuggestions();
            }
        }

        function updateSelectedSuggestion(suggestionItems) {
            suggestionItems.forEach((item, index) => {
                item.classList.toggle('selected', index === selectedSuggestionIndex);
            });
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            const searchBar = document.querySelector('.search-bar');
            if (!searchBar.contains(e.target)) {
                hideSuggestions();
            }
        });

        // Wheel event for circular navigation
        let wheelTimeout;
        function handleWheel(e) {
            if (isMobile) return;
            
            e.preventDefault();
            clearTimeout(wheelTimeout);
            
            wheelTimeout = setTimeout(() => {
                if (e.deltaY > 0) {
                    nextHotel();
                } else {
                    prevHotel();
                }
            }, 50);
        }

        // Keyboard navigation for circular display
        function handleKeyboard(e) {
            if (isMobile) return;
            
            if (e.key === 'ArrowRight' || e.key === ' ') {
                e.preventDefault();
                nextHotel();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                prevHotel();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadHotelsFromDB();
            
            const searchInput = document.getElementById('searchInput');
            
            // Search button click
            document.getElementById('searchBtn').addEventListener('click', function() {
                const searchTerm = searchInput.value;
                filterHotels(searchTerm);
                hideSuggestions();
            });
            
            // Input events for suggestions
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();
                
                if (searchTerm.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        getSearchSuggestions(searchTerm);
                    }, 300); // Debounce for 300ms
                } else {
                    hideSuggestions();
                }
            });
            
            // Keyboard navigation
            searchInput.addEventListener('keydown', handleKeyNavigation);
            
            // Enter key for search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && selectedSuggestionIndex === -1) {
                    e.preventDefault();
                    filterHotels(this.value);
                    hideSuggestions();
                }
            });
            
            // Add wheel and keyboard event listeners for circular display
            if (!isMobile) {
                const circularContainer = document.querySelector('.circular-hotel-container');
                circularContainer.addEventListener('wheel', handleWheel, { passive: false });
                document.addEventListener('keydown', handleKeyboard);
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                const wasMobile = isMobile;
                isMobile = window.innerWidth <= 768;
                
                if (wasMobile !== isMobile) {
                    if (isMobile) {
                        displayHotels(allHotels);
                        document.getElementById('hotel-list').style.display = 'grid';
                        document.querySelector('.circular-hotel-container').style.display = 'none';
                    } else {
                        updateCircularDisplay();
                        document.getElementById('hotel-list').style.display = 'none';
                        document.querySelector('.circular-hotel-container').style.display = 'block';
                    }
                }
            });
        });
    </script>
    <script>
    document.getElementById('themeToggle').onclick = function() {
        document.body.classList.toggle('dark-mode');
        // Save preference
        if(document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            this.textContent = '☀️ Light Mode';
        } else {
            localStorage.setItem('theme', 'light');
            this.textContent = '🌙 Dark Mode';
        }
    };
    // On page load, set theme from localStorage
    window.onload = function() {
        if(localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            document.getElementById('themeToggle').textContent = '☀️ Light Mode';
        }
    };
    </script>
</body>
</html> 