# PahunaGhar - Hotel Booking System

A modern hotel booking website with search functionality for finding hotels by name, location, and price.

## Features

- **Hotel Search**: Search hotels by name, location, description, or price
- **Responsive Design**: Modern UI that works on all devices
- **Database Integration**: MySQL database for hotel data storage
- **Fallback System**: Client-side search if database is unavailable
- **Cross-page Search**: Search from any page and get redirected to results
- **Admin Panel**: Complete administrative interface for managing hotels and users
- **User Features**: User registration and login, hotel browsing, booking system, review and rating system, contact form for inquiries
- **Admin Features**: Admin dashboard with statistics, user management, hotel management (add, edit, delete), booking management, review management, contact message management

## Setup Instructions

### 1. Database Setup

1. **Start XAMPP**:
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Create Database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database_setup.sql` file or run the SQL commands manually

3. **Database Configuration**:
   - The default configuration uses:
     - Host: localhost
     - Database: pahunaghar
     - Username: root
     - Password: (empty)
   - Modify `config.php` if you need different settings

### 2. File Structure

```
PahunaGhar/
├── config.php              # Database configuration
├── search.php              # Search API endpoint
├── homepage.php            # Main homepage with search
├── lets_chat.php           # Let's Chat page
├── login.php               # Login page
├── register.php            # Registration page
├── admin.php               # Admin dashboard
├── admin_api.php           # Admin API endpoints
├── admin_hotels.php        # Hotel management
├── admin_users.php         # User management
├── logout.php              # Logout functionality
├── styles.css              # CSS styles
├── database_setup.sql      # Database setup script
└── README.md               # This file
```

### 3. Search Functionality

The search system works in two ways:

1. **Database Search** (Primary):
   - Searches hotel name, location, description, and price
   - Uses MySQL LIKE queries for flexible matching
   - Handles price range searches

2. **Client-side Search** (Fallback):
   - Works if database is unavailable
   - Searches through predefined hotel data
   - Same search criteria as database search

### 4. Admin Panel

Access the admin panel at: `http://localhost/PahunaGhar/admin.php`

#### Admin Features:
- **Dashboard**: Overview with statistics and quick actions
- **Hotel Management**: Add, edit, delete, and search hotels
- **User Management**: View and manage user accounts
- **Real-time Updates**: AJAX-powered interface for smooth interactions

#### Admin Functions:
- **Add Hotels**: Complete form with image URLs, ratings, and descriptions
- **Edit Hotels**: Modify existing hotel information
- **Delete Hotels**: Remove hotels with confirmation
- **Search Hotels**: Filter hotels by name, location, or description
- **View Statistics**: Hotel count, user count, and system information

### 5. Usage

1. **Search from Homepage**:
   - Type in the search bar
   - Press Enter or click Search button
   - Results are displayed immediately

2. **Search from Let's Chat Page**:
   - Search redirects to homepage with results
   - URL includes search parameter

3. **Admin Management**:
   - Navigate to admin.php
   - Use the navigation menu to access different sections
   - Add new hotels with the "Add New Hotel" button
   - Edit existing hotels by clicking the "Edit" button
   - Delete hotels with confirmation

4. **Search Examples**:
   - Hotel name: "Soaltee", "Hyatt"
   - Location: "Kathmandu", "Pokhara"
   - Price: "300", "100"
   - Description keywords: "luxury", "mountain"

### 6. Database Schema

```sql
CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    price VARCHAR(50) NOT NULL,
    rating DECIMAL(3,1) DEFAULT 0.0,
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Troubleshooting

1. **Database Connection Error**:
   - Ensure XAMPP MySQL is running
   - Check database credentials in `config.php`
   - Verify database `pahunaghar` exists

2. **Search Not Working**:
   - Check browser console for JavaScript errors
   - Verify `search.php` is accessible
   - Ensure database table has data

3. **Admin Panel Not Working**:
   - Check if `admin_api.php` is accessible
   - Verify database permissions
   - Check browser console for AJAX errors

4. **Images Not Loading**:
   - Check internet connection
   - Some images are external URLs

## Security Notes

- The admin panel currently has no authentication (for development)
- In production, implement proper admin login system
- Add user roles and permissions
- Secure API endpoints with authentication

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **Fonts**: Google Fonts (Montserrat)

## License

© 2025 PahunaGhar. All rights reserved.

## Let's Chat Form Functionality

The Let's Chat form allows users to send messages to the admin. Features include:

- **Form Validation**: Validates name, email, and message fields
- **Database Storage**: Messages are stored in the `contact` table
- **Admin Management**: Admins can view and delete Let's Chat messages
- **Success/Error Messages**: User-friendly feedback for form submission

### Contact Table Structure
```sql
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Admin Let's Chat Management
- Access via Admin Dashboard → Let's Chat
- View all Let's Chat messages with sender details
- Delete unwanted messages
- Sort by submission date (newest first)

## Installation

1. Place all files in your web server directory
2. Configure database connection in `config.php`
3. Import database schema files
4. Access the application through your web browser

## File Structure

- `lets_chat.php` - Let's Chat form page
- `admin_lets_chat.php` - Admin Let's Chat management
- `config.php` - Database configuration

## Security Features

- SQL injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- Input validation and sanitization
- Session-based authentication

## Usage

### For Users
1. Navigate to the Let's Chat page
2. Fill in your name, email, and message
3. Submit the form
4. Receive confirmation message

### For Admins
1. Login to admin dashboard
2. Click on "Let's Chat" in the navigation
3. View and manage Let's Chat messages
4. Delete messages as needed
