-- Create admin user for PahunaGhar
USE PahunaGhar;

-- Insert admin user (password: admin123)
INSERT INTO user_register_form (Name, Email, password, user_type) VALUES 
('Admin User', 'admin@pahunaghar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Note: The password hash above is for 'admin123'
-- You can also create a new admin user with a different password using PHP's password_hash() function 