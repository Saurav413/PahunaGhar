-- Create admin_register_form table for PahunaGhar Admin Accounts
USE PahunaGhar;

-- Drop table if exists to ensure clean creation
DROP TABLE IF EXISTS admin_register_form;

-- Create admin_register_form table
CREATE TABLE admin_register_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    admin_role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_register_form (name, email, password, admin_role) VALUES 
('Super Admin', 'admin@pahunaghar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Create index for better performance
CREATE INDEX idx_admin_email ON admin_register_form(email);
CREATE INDEX idx_admin_role ON admin_register_form(admin_role);
CREATE INDEX idx_admin_active ON admin_register_form(is_active); 