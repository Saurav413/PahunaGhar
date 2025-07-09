# Migration from register_form to user_register_form

This document outlines the migration process from the `register_form` table to the new `user_register_form` table in the PahunaGhar system.

## Overview

The system has been updated to use `user_register_form` instead of `register_form` for storing user registration data. This change provides better naming consistency and organization.

## Files Created/Modified

### New Files Created:
1. `create_user_register_form_table.sql` - SQL script to create the new table
2. `migrate_to_user_register_form.php` - Migration script to copy existing data
3. `MIGRATION_README.md` - This documentation file

### Files Modified:
1. `register.php` - Updated to use `user_register_form`
2. `login.php` - Updated to use `user_register_form`
3. `admin_users.php` - Updated to use `user_register_form`
4. `admin_dashboard.php` - Updated to use `user_register_form`
5. `admin_bookings.php` - Updated to use `user_register_form`
6. `admin_api.php` - Updated to use `user_register_form`
7. `hotel_reviews.php` - Updated to use `user_register_form`
8. `admin_reviews.php` - Updated to use `user_register_form`
9. `admin_tables.sql` - Updated foreign key references
10. `create_reviews_table.sql` - Updated foreign key references
11. `create_admin_user.sql` - Updated to use `user_register_form`
12. `REVIEW_SYSTEM_README.md` - Updated foreign key references

## Migration Steps

### Step 1: Create the New Table
Run the SQL script to create the new table:
```sql
-- Execute the contents of create_user_register_form_table.sql
```

### Step 2: Run the Migration Script
Execute the migration script to copy existing data:
```bash
php migrate_to_user_register_form.php
```

### Step 3: Verify Migration
Check that all data has been migrated correctly:
```sql
SELECT COUNT(*) FROM user_register_form;
SELECT COUNT(*) FROM register_form;
```

### Step 4: Test the System
1. Test user registration
2. Test user login
3. Test admin functionality
4. Test booking system
5. Test review system

### Step 5: Optional - Remove Old Table
Once you've verified everything works correctly, you can optionally remove the old table:
```sql
DROP TABLE register_form;
```

## Table Structure

The `user_register_form` table has the same structure as the original `register_form`:

```sql
CREATE TABLE user_register_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Foreign Key Relationships

The following tables reference `user_register_form`:
- `bookings` - user_id foreign key
- `reviews` - user_id foreign key

## Backward Compatibility

The migration script preserves all existing data and maintains the same functionality. All user IDs remain the same, ensuring that existing bookings and reviews continue to work correctly.

## Rollback Plan

If you need to rollback the changes:
1. Update all PHP files to reference `register_form` instead of `user_register_form`
2. Update all SQL files to reference `register_form` instead of `user_register_form`
3. Drop the `user_register_form` table if it was created

## Testing Checklist

- [ ] User registration works
- [ ] User login works
- [ ] Admin login works
- [ ] User bookings display correctly
- [ ] Admin dashboard shows correct user count
- [ ] Admin can manage users
- [ ] Reviews system works
- [ ] Booking system works
- [ ] All foreign key relationships work

## Notes

- The migration preserves all existing user data
- User IDs remain unchanged
- All existing functionality continues to work
- The change is transparent to end users
- Admin functionality remains the same 