-- Create user_register_form table for PahunaGhar
USE PahunaGhar;

-- Drop table if exists to ensure clean creation
DROP TABLE IF EXISTS user_register_form;

-- Create user_register_form table
CREATE TABLE user_register_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO user_register_form (name, email, password, user_type) VALUES 
('Admin User', 'admin@pahunaghar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 