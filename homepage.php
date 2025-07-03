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
    <link rel="stylesheet" href="styles.css?v=1.1">
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo">Pahuna<span style="color:#2563eb;">Ghar</span></a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <?php if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin'): ?>
                    <a href="PahunaGhar/user_bookings.php" class="nav-link">My Bookings</a>
                <?php endif; ?>
                <a href="lets_chat.php" class="nav-link">Let's Chat</a>
            <?php endif; ?>
        </div>
        <div class="navbar-center">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search destinations, hotels, or prices...">
                <button id="searchBtn">Search</button>
            </div>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <span style="color: #2563eb; font-weight: 500; margin-right: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                    <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
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
        <div id="hotel-list" class="hotel-list">
            <!-- Hotel listings will be loaded here -->
        </div>
    </div>
    <footer style="width:100%;background:#e6e7eb;padding:28px 0 18px 0;text-align:center;font-size:1.08em;color:#222;letter-spacing:0.5px;margin-top:48px;box-shadow:0 -2px 8px rgba(44,62,80,0.04);font-family:'Montserrat',Arial,sans-serif;">
        © 2025 <span style="font-weight:700;color:#2563eb;">PahunaGhar</span>. All rights reserved.
    </footer>

    <script>
        // Function to create hotel card HTML
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

        // Function to display hotels
        function displayHotels(hotels) {
            const hotelList = document.getElementById('hotel-list');
            hotelList.innerHTML = hotels.map(hotel => createHotelCard(hotel)).join('');
        }

        // Fetch hotels from the database on page load
        function loadHotelsFromDB() {
            fetch('public_api.php?action=get_hotels')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.hotels.length > 0) {
                        displayHotels(data.hotels);
                    } else {
                        document.getElementById('hotel-list').innerHTML = '<div class="no-hotels">No hotels found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading hotels:', error);
                    document.getElementById('hotel-list').innerHTML = '<div class="no-hotels">Error loading hotels</div>';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadHotelsFromDB();
        });
    </script>
</body>
</html> 