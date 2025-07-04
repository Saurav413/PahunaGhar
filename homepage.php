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
                <div id="searchSuggestions" class="search-suggestions"></div>
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
        let allHotels = [];

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
                        displayHotels(allHotels);
                    } else {
                        document.getElementById('hotel-list').innerHTML = '<div class="no-hotels">No hotels found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading hotels:', error);
                    document.getElementById('hotel-list').innerHTML = '<div class="no-hotels">Error loading hotels</div>';
                });
        }

        function filterHotels(searchTerm) {
            searchTerm = searchTerm.trim().toLowerCase();
            if (!searchTerm) {
                displayHotels(allHotels);
                return;
            }
            const filtered = allHotels.filter(hotel =>
                (hotel.name && hotel.name.toLowerCase().includes(searchTerm)) ||
                (hotel.location && hotel.location.toLowerCase().includes(searchTerm)) ||
                (hotel.price && hotel.price.toLowerCase().includes(searchTerm))
            );
            displayHotels(filtered);
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
        });
    </script>
</body>
</html> 