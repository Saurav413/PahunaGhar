-- Additional tables for PahunaGhar admin dashboard
USE PahunaGhar;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guests INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_register_form(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_register_form(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

-- Sample booking data
INSERT INTO bookings (user_id, hotel_id, check_in_date, check_out_date, guests, total_amount, status) VALUES
(1, 1, '2025-02-15', '2025-02-17', 2, 600.00, 'confirmed'),
(1, 2, '2025-03-01', '2025-03-03', 1, 206.00, 'pending'),
(2, 3, '2025-02-20', '2025-02-22', 3, 109.59, 'confirmed');

-- Sample review data
INSERT INTO reviews (user_id, hotel_id, rating, comment) VALUES
(1, 1, 4.8, 'Excellent service and beautiful rooms!'),
(1, 2, 4.5, 'Great location and friendly staff.'),
(2, 3, 4.7, 'Amazing mountain views and cozy atmosphere.'); 