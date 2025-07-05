# Admin System Documentation

This document outlines the new admin system that uses a separate `admin_register_form` table for admin authentication and management.

## Overview

The system now has two separate authentication systems:
1. **User System** - Uses `user_register_form` table for regular customers
2. **Admin System** - Uses `admin_register_form` table for administrators

## Admin Table Structure

```sql
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
```

## Admin Roles

- **super_admin** - Full system access, can manage other admins
- **admin** - Standard admin access
- **moderator** - Limited admin access

## Files Created

### New Files:
1. `create_admin_register_form_table.sql` - Creates the admin table
2. `admin_login.php` - Admin login page
3. `admin_register.php` - Admin registration page (admin-only access)
4. `admin_manage_admins.php` - Admin management interface
5. `ADMIN_SYSTEM_README.md` - This documentation

## Admin Authentication Flow

### 1. Admin Login (`admin_login.php`)
- Separate login page for administrators
- Checks credentials against `admin_register_form` table
- Validates account status (active/inactive)
- Updates last login timestamp
- Sets admin-specific session variables

### 2. Admin Registration (`admin_register.php`)
- Only accessible to existing admins
- Creates new admin accounts
- Prevents duplicate emails across both user and admin tables
- Supports different admin roles

### 3. Admin Management (`admin_manage_admins.php`)
- View all admin accounts
- Activate/deactivate admin accounts
- Delete admin accounts
- View last login times
- Role-based access control

## Session Variables for Admins

When an admin logs in, the following session variables are set:

```php
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_name'] = $admin['name'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_role'] = $admin['admin_role'];
$_SESSION['user_id'] = $admin['id']; // For compatibility
$_SESSION['user_name'] = $admin['name']; // For compatibility
$_SESSION['user_email'] = $admin['email']; // For compatibility
$_SESSION['user_type'] = 'admin'; // For compatibility
$_SESSION['logged_in'] = true;
```

## Security Features

1. **Account Status** - Admins can be deactivated without deletion
2. **Self-Protection** - Admins cannot deactivate/delete their own accounts
3. **Role-Based Access** - Different admin roles with varying permissions
4. **Last Login Tracking** - Monitor admin activity
5. **Email Uniqueness** - Prevents email conflicts between user and admin tables

## Setup Instructions

### Step 1: Create Admin Table
```sql
-- Execute create_admin_register_form_table.sql
```

### Step 2: Create Initial Admin
The default admin account is created automatically:
- Email: admin@pahunaghar.com
- Password: admin123
- Role: super_admin

### Step 3: Access Admin System
1. Navigate to `admin_login.php`
2. Login with admin credentials
3. Access admin dashboard

## Admin Dashboard Features

### User Management
- View all regular users
- Manage user accounts
- View user statistics

### Admin Management
- Create new admin accounts
- Manage existing admin accounts
- Activate/deactivate admin accounts
- View admin activity

### System Management
- Hotel management
- Booking management
- Review management
- Contact form management

## Navigation Structure

```
Admin Dashboard
├── Dashboard (Overview)
├── Users (Regular user management)
├── Admins (Admin account management)
├── Bookings (Booking management)
├── Reviews (Review management)
├── Hotels (Hotel management)
└── Let's Chat (Contact form management)
```

## API Endpoints

The admin system uses the existing `admin_api.php` for AJAX operations:
- User management
- Hotel management
- Booking management
- Review management

## Migration from Old System

If migrating from the old system where admins were stored in `user_register_form`:

1. Create the new `admin_register_form` table
2. Migrate existing admin accounts:
   ```sql
   INSERT INTO admin_register_form (name, email, password, admin_role, created_at)
   SELECT name, email, password, 'admin', created_at 
   FROM user_register_form 
   WHERE user_type = 'admin';
   ```
3. Update admin accounts to use the new login system

## Best Practices

1. **Regular Password Changes** - Encourage admins to change default passwords
2. **Role Assignment** - Assign appropriate roles based on responsibilities
3. **Account Monitoring** - Regularly review admin accounts and activity
4. **Backup** - Keep regular backups of admin accounts
5. **Access Control** - Limit admin creation to super admins only

## Troubleshooting

### Common Issues:

1. **Admin cannot login**
   - Check if account is active
   - Verify email and password
   - Check database connection

2. **Admin registration fails**
   - Ensure only existing admins can access
   - Check for email conflicts
   - Verify database permissions

3. **Session issues**
   - Check session configuration
   - Verify session variables are set correctly
   - Clear browser cache if needed

## Support

For technical support or questions about the admin system, contact the development team or refer to the main system documentation. 