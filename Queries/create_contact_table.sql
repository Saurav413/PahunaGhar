-- Create contact table for storing contact form submissions
USE pahunaghar;

CREATE TABLE IF NOT EXISTS contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 