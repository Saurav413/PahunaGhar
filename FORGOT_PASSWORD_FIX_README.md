# Forgot Password Functionality - Issues and Fixes

## Issues Found

### 1. **Missing Database Table**
- The `password_resets` table was missing from the database
- This table is essential for storing password reset tokens

### 2. **Wrong Database Connection**
- `forgot_password.php` was using `config.php` which connects to `pahunaghar` database
- But the user data is in `PahunaGhar` database (note the capital letters)
- Should use `user_config.php` which connects to the correct database

### 3. **SMTP Configuration Issues**
- SMTP settings were placeholders (`smtp.example.com`, `your_email@example.com`)
- No real email server configured
- This would prevent password reset emails from being sent

### 4. **Error Handling**
- No proper error reporting enabled
- Database errors were not being caught and displayed
- No distinction between different types of messages (success, error, info)

## Fixes Applied

### 1. **Created Missing Database Table**
```sql
-- File: Queries/create_password_resets_table.sql
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
);
```

### 2. **Fixed Database Connection**
- Changed from `require 'config.php'` to `require 'user_config.php'`
- Updated all database queries to use `$user_pdo` instead of `$conn`

### 3. **Improved Error Handling**
- Added error reporting for debugging
- Added try-catch blocks for database operations
- Added message types (success, error, info) with different colors

### 4. **Temporary Email Bypass for Testing**
- Instead of sending emails (which requires SMTP configuration), the system now displays the reset link directly
- This allows testing the functionality without email setup
- Added TODO comments for production email configuration

### 5. **Enhanced User Experience**
- Better error messages
- Color-coded messages (red for errors, green for success, blue for info)
- Clear instructions for testing

## How to Test

### 1. **Create the Database Table**
Run the SQL query in `Queries/create_password_resets_table.sql` in your MySQL database.

### 2. **Test the Forgot Password Flow**
1. Go to `login.php`
2. Click "Forgot Password?"
3. Enter a valid email address that exists in your `user_register_form` table
4. Submit the form
5. You should see a reset link displayed (instead of an email being sent)
6. Click the reset link
7. Enter a new password (minimum 6 characters)
8. Submit to reset the password

### 3. **Test with Invalid Email**
- Enter an email that doesn't exist in the database
- Should show the same message (for security - doesn't reveal if email exists)

## For Production Use

### 1. **Configure SMTP Settings**
Uncomment the email sending code in `forgot_password.php` and configure:

```php
$mail->Host = 'smtp.gmail.com'; // Your SMTP server
$mail->Username = 'your_email@gmail.com'; // Your email
$mail->Password = 'your_app_password'; // Your app password
```

### 2. **Remove Debug Information**
- Remove or comment out the reset link display
- Keep only the "If your email is registered, a reset link has been sent." message

### 3. **Security Considerations**
- The current implementation is secure with:
  - Random token generation
  - 1-hour expiration
  - One-time use tokens
  - Proper password hashing
  - SQL injection protection

## Files Modified

1. **forgot_password.php** - Fixed database connection, added error handling, temporary email bypass
2. **reset_password.php** - Fixed database connection, added error handling
3. **Queries/create_password_resets_table.sql** - Created missing database table
4. **test_db.php** - Created diagnostic script (can be deleted after testing)

## Database Requirements

Make sure you have:
- `PahunaGhar` database (with capital letters)
- `user_register_form` table with user data
- `password_resets` table (created by the new SQL file)

The forgot password functionality should now work correctly for testing purposes! 