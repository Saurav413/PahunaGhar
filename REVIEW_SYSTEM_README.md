# Hotel Review System - PahunaGhar

## Overview
This document describes the new review system added to the PahunaGhar hotel booking platform. Users can now write reviews for hotels they have booked, and view reviews from other customers.

## Features Added

### 1. Review Submission (`submit_review.php`)
- **Star Rating System**: 1-5 star rating with interactive star selection
- **Review Comments**: Text area for detailed feedback (minimum 10 characters)
- **Update Existing Reviews**: Users can update their previous reviews
- **Validation**: Ensures proper rating and comment length
- **User Authentication**: Only logged-in users can submit reviews

### 2. Enhanced User Bookings (`user_bookings.php`)
- **Review Status Column**: Shows if user has reviewed each hotel
- **Review Buttons**: "Write Review" for unreviewed hotels, "Update Review" for reviewed hotels
- **Review Badge**: Displays user's rating for hotels they've reviewed
- **Visual Indicators**: Different button styles for new vs. existing reviews

### 3. Hotel Reviews Page (`hotel_reviews.php`)
- **All Reviews Display**: Shows all reviews for a specific hotel
- **Review Statistics**: Average rating and total review count
- **Reviewer Information**: Shows reviewer name and review date
- **Write Review Button**: Direct link to submit a review (for logged-in users)

### 4. Enhanced Homepage (`homepage.php`)
- **Updated Hotel Cards**: Shows average rating from reviews and review count
- **Action Buttons**: Separate "Book Now" and "View Reviews" buttons
- **Real-time Data**: Fetches review statistics from database

### 5. Database Integration (`public_api.php`)
- **Review Statistics**: API now includes review count and average rating
- **Performance Optimized**: Uses JOIN queries for efficient data retrieval

## Database Schema

### Reviews Table
```sql
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES register_form(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_hotel_review (user_id, hotel_id)
);
```

## Setup Instructions

### 1. Create Reviews Table
Run the setup script to create the reviews table:
```
http://your-domain/setup_reviews.php
```

Or manually execute the SQL in `create_reviews_table.sql`

### 2. File Structure
- `submit_review.php` - Review submission form
- `hotel_reviews.php` - Display all reviews for a hotel
- `user_bookings.php` - Updated with review functionality
- `homepage.php` - Updated with review statistics
- `public_api.php` - Enhanced with review data
- `create_reviews_table.sql` - Database setup script
- `setup_reviews.php` - Automated setup script

## User Flow

### Writing a Review
1. User logs in and goes to "My Bookings"
2. Clicks "Write Review" for a hotel they've booked
3. Fills out rating (1-5 stars) and comment
4. Submits review
5. Review appears on hotel's review page

### Viewing Reviews
1. User visits homepage
2. Clicks "View Reviews" on any hotel card
3. Sees all reviews for that hotel
4. Can write their own review if logged in

### Updating Reviews
1. User goes to "My Bookings"
2. Clicks "Update Review" for a hotel they've already reviewed
3. Modifies rating and/or comment
4. Submits updated review

## Security Features
- User authentication required for review submission
- Input validation for ratings and comments
- SQL injection prevention with prepared statements
- XSS prevention with htmlspecialchars()
- Unique constraint prevents multiple reviews per user per hotel

## Styling
- Consistent with existing PahunaGhar design
- Responsive design for mobile devices
- Interactive star rating system
- Hover effects and smooth transitions
- Color-coded buttons and status indicators

## Future Enhancements
- Review helpfulness voting
- Review filtering and sorting
- Review moderation system
- Review response from hotel owners
- Review photos upload
- Review analytics dashboard 