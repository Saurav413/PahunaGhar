<?php
session_start();
require_once 'user_config.php';

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$isAdmin = true; // Now properly authenticated
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hotels - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_hotels.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_users.php">Users</a>
                <a href="admin_bookings.php">Bookings</a>
                <a href="admin_reviews.php">Reviews</a>
                <a href="admin_hotels.php" class="active">Hotels</a>
                <a href="admin_contacts.php">Contacts</a>
            </div>
            <div>
                <a href="homepage.php">View Site</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1>Manage Hotels</h1>
                <p>Add, edit, and manage hotel listings</p>
            </div>
            <button class="btn btn-primary" onclick="openAddModal()">Add New Hotel</button>
        </div>

        <div class="hotels-table">
            <div class="table-header">
                <div class="table-title">All Hotels</div>
                <div>
                    <input type="text" id="searchHotels" placeholder="Search hotels..." class="search-hotels-input">
                    <button class="btn btn-primary" onclick="loadHotels()">Refresh</button>
                </div>
            </div>
            <div class="hotels-list" id="hotelsList">
                <div class="loading">Loading hotels...</div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Hotel Modal -->
    <div id="hotelModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Hotel</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="hotelForm">
                <input type="hidden" id="hotelId" name="id">
                
                <div class="form-group">
                    <label for="hotelName">Hotel Name *</label>
                    <input type="text" id="hotelName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="hotelLocation">Location *</label>
                    <input type="text" id="hotelLocation" name="location" required>
                </div>
                
                <div class="form-group">
                    <label for="hotelDescription">Description</label>
                    <textarea id="hotelDescription" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="hotelPrice">Price *</label>
                    <input type="text" id="hotelPrice" name="price" placeholder="e.g., $150/night" required>
                </div>
                
                <div class="form-group">
                    <label for="hotelRating">Rating</label>
                    <select id="hotelRating" name="rating">
                        <option value="0">Select Rating</option>
                        <option value="1.0">1.0</option>
                        <option value="1.5">1.5</option>
                        <option value="2.0">2.0</option>
                        <option value="2.5">2.5</option>
                        <option value="3.0">3.0</option>
                        <option value="3.5">3.5</option>
                        <option value="4.0">4.0</option>
                        <option value="4.5">4.5</option>
                        <option value="5.0">5.0</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="hotelImage">Image URL</label>
                    <input type="url" id="hotelImage" name="image_url" placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Hotel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let hotels = [];
        let editingHotel = null;

        // Load hotels on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadHotels();
            
            // Search functionality
            document.getElementById('searchHotels').addEventListener('input', function() {
                filterHotels(this.value);
            });
        });

        function loadHotels() {
            fetch('admin_api.php?action=get_hotels')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hotels = data.hotels;
                        displayHotels(hotels);
                    } else {
                        document.getElementById('hotelsList').innerHTML = '<div class="no-hotels">Error loading hotels: ' + data.error + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('hotelsList').innerHTML = '<div class="no-hotels">Error loading hotels</div>';
                });
        }

        function displayHotels(hotelsToShow) {
            const container = document.getElementById('hotelsList');
            
            if (hotelsToShow.length === 0) {
                container.innerHTML = '<div class="no-hotels">No hotels found</div>';
                return;
            }
            
            container.innerHTML = `
                <div class="hotel-row hotel-row-header">
                    <div>Image</div>
                    <div>Hotel Info</div>
                    <div>Location</div>
                    <div>Price</div>
                    <div>Rating</div>
                    <div>Actions</div>
                </div>
            ` + hotelsToShow.map(hotel => `
                <div class="hotel-row">
                    <div>
                        <img src="${hotel.image_url || 'https://via.placeholder.com/60x60?text=No+Image'}" 
                             alt="${hotel.name}" 
                             class="hotel-image"
                             onerror="this.src='https://via.placeholder.com/60x60?text=Error'">
                    </div>
                    <div class="hotel-info">
                        <h3>${hotel.name}</h3>
                        <p>${hotel.description || 'No description available'}</p>
                    </div>
                    <div class="hotel-location">${hotel.location}</div>
                    <div class="hotel-price">${hotel.price}</div>
                    <div class="hotel-rating">${hotel.rating ? '★ ' + hotel.rating : 'No rating'}</div>
                    <div class="hotel-actions">
                        <button class="btn btn-warning btn-small" onclick="editHotel(${hotel.id})">Edit</button>
                        <button class="btn btn-danger btn-small" onclick="deleteHotel(${hotel.id})">Delete</button>
                    </div>
                </div>
            `).join('');
        }

        function filterHotels(searchTerm) {
            const filtered = hotels.filter(hotel => 
                hotel.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                hotel.location.toLowerCase().includes(searchTerm.toLowerCase()) ||
                hotel.description.toLowerCase().includes(searchTerm.toLowerCase())
            );
            displayHotels(filtered);
        }

        function openAddModal() {
            editingHotel = null;
            document.getElementById('modalTitle').textContent = 'Add New Hotel';
            document.getElementById('hotelForm').reset();
            document.getElementById('hotelId').value = '';
            document.getElementById('hotelModal').style.display = 'block';
        }

        function editHotel(hotelId) {
            const hotel = hotels.find(h => h.id == hotelId);
            if (hotel) {
                editingHotel = hotel;
                document.getElementById('modalTitle').textContent = 'Edit Hotel';
                document.getElementById('hotelId').value = hotel.id;
                document.getElementById('hotelName').value = hotel.name;
                document.getElementById('hotelLocation').value = hotel.location;
                document.getElementById('hotelDescription').value = hotel.description || '';
                document.getElementById('hotelPrice').value = hotel.price;
                document.getElementById('hotelRating').value = hotel.rating || 0;
                document.getElementById('hotelImage').value = hotel.image_url || '';
                document.getElementById('hotelModal').style.display = 'block';
            }
        }

        function closeModal() {
            document.getElementById('hotelModal').style.display = 'none';
            editingHotel = null;
        }

        function deleteHotel(hotelId) {
            if (confirm('Are you sure you want to delete this hotel?')) {
                fetch('admin_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_hotel&id=${hotelId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Hotel deleted successfully!');
                        loadHotels();
                    } else {
                        alert('Error deleting hotel: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting hotel');
                });
            }
        }

        // Handle form submission
        document.getElementById('hotelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = editingHotel ? 'update_hotel' : 'add_hotel';
            formData.append('action', action);
            
            fetch('admin_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(editingHotel ? 'Hotel updated successfully!' : 'Hotel added successfully!');
                    closeModal();
                    loadHotels();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving hotel');
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('hotelModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html> 