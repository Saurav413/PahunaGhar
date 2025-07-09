# Super Admin System - PahunaGhar

## Overview

The Super Admin System provides elevated privileges for managing the entire PahunaGhar platform, including admin account management and all administrative functions.

## Features

### Super Admin Capabilities
- ✅ **Full System Access**: Access to all admin functions
- ✅ **Admin Management**: Create, edit, delete, and manage admin accounts
- ✅ **Role Management**: Promote/demote admins to/from super admin
- ✅ **User Management**: Manage all user accounts
- ✅ **Hotel Management**: Add, edit, and manage hotel listings
- ✅ **Booking Oversight**: Monitor and manage all bookings
- ✅ **Review Moderation**: Manage and moderate user reviews
- ✅ **System Configuration**: Complete system control

### Admin Capabilities (Regular)
- ✅ **Limited System Access**: Access to most admin functions
- ✅ **User Management**: View and manage user accounts
- ✅ **Hotel Management**: Add, edit, and manage hotel listings
- ✅ **Booking Management**: Monitor and manage bookings
- ✅ **Review Management**: View and moderate reviews
- ✅ **Chat Management**: Handle customer support

## File Structure

### Super Admin Files
- `super_admin_login.php` - Super admin login page
- `super_admin_dashboard.php` - Super admin dashboard
- `super_admin_manage_admins.php` - Admin account management
- `super_admin_register.php` - Create new admin accounts
- `setup_super_admin.php` - Initial super admin setup

### Updated Files
- `admin_login.php` - Updated to support regular admin login only
- `admin_register.php` - Updated to support role selection
- `admin_manage_admins.php` - Updated to show role badges

## Setup Instructions

### 1. Initial Super Admin Setup

1. **Access the setup page**:
   ```
   http://your-domain/setup_super_admin.php
   ```

2. **Create the first super admin**:
   - Enter super admin name
   - Enter email address
   - Set a strong password (minimum 8 characters)
   - Confirm password

3. **Complete setup**:
   - Click "Create Super Admin Account"
   - You'll be redirected to super admin login

### 2. Super Admin Login

1. **Access super admin login**:
   ```
   http://your-domain/super_admin_login.php
   ```

2. **Login with credentials**:
   - Enter super admin email
   - Enter password
   - Click "Super Admin Login"

### 3. Managing Admin Accounts

1. **From Super Admin Dashboard**:
   - Click "Manage Admins" in the navigation
   - View all admin accounts with their roles

2. **Create New Admin**:
   - Click "Create New Admin" button
   - Fill in admin details
   - Select role (Admin or Super Admin)
   - Set password

3. **Manage Existing Admins**:
   - **Change Role**: Use dropdown to promote/demote
   - **Activate/Deactivate**: Toggle account status
   - **Delete**: Remove admin account (cannot delete self)

## Security Features

### Access Control
- **Super Admin Only**: Certain functions restricted to super admins
- **Role-based Access**: Different permissions for different roles
- **Session Management**: Secure session handling
- **Password Security**: Strong password requirements

### Data Protection
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: HTML escaping
- **CSRF Protection**: Form validation
- **Input Validation**: Comprehensive input checking

## User Roles

### Super Admin
- **Role**: `super_admin`
- **Access**: Full system access
- **Can**: Manage all admins, users, hotels, bookings, reviews
- **Cannot**: Delete own account or change own role

### Regular Admin
- **Role**: `admin`
- **Access**: Limited admin functions
- **Can**: Manage users, hotels, bookings, reviews
- **Cannot**: Manage other admin accounts

## Database Schema

### Admin Table (`admin_register_form`)
```sql
- id (Primary Key)
- name (Admin Name)
- email (Email Address)
- password (Hashed Password)
- admin_role (admin/super_admin)
- is_active (1/0)
- created_at (Timestamp)
- last_login (Timestamp)
```

## Navigation Structure

### Super Admin Navigation
- **Dashboard**: System overview and statistics
- **Manage Admins**: Admin account management
- **Users**: User account management
- **Bookings**: Booking management
- **Reviews**: Review moderation
- **Hotels**: Hotel management
- **Let's Chat**: Customer support

### Regular Admin Navigation
- **Dashboard**: Limited system overview
- **Users**: User account management
- **Bookings**: Booking management
- **Reviews**: Review moderation
- **Hotels**: Hotel management
- **Let's Chat**: Customer support

## Error Handling

### Common Error Messages
- **"Super admin account already exists"**: Setup script can only run once
- **"You cannot deactivate your own account"**: Self-protection mechanism
- **"You cannot change your own role"**: Self-protection mechanism
- **"Email already registered"**: Duplicate email prevention

### Troubleshooting
1. **Setup Issues**: Ensure database connection is working
2. **Login Issues**: Check email and password
3. **Permission Issues**: Verify user role and account status
4. **Database Issues**: Check table structure and permissions

## Best Practices

### Security
- Use strong passwords for all admin accounts
- Regularly update admin passwords
- Monitor admin account activity
- Limit super admin accounts to trusted personnel

### Management
- Document all admin account changes
- Regular audit of admin permissions
- Backup admin data regularly
- Test admin functions in development environment

## Support

For technical support or questions about the Super Admin System:
- Check the error logs for detailed information
- Verify database connectivity
- Ensure all required files are present
- Contact system administrator for assistance

---

**Note**: This system provides powerful administrative capabilities. Use responsibly and ensure proper access controls are in place. 