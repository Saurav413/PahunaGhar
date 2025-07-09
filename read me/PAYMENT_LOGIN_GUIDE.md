# Payment Gateway Login Guide

## Problem: Cannot Login to eSewa and Khalti Accounts

You're experiencing login issues because you need to follow the **complete authentication flow**. The payment gateways require a two-step login process.

## Root Cause Analysis

The debug output shows:
- ✅ Database tables exist (`esewa_users`, `khalti_users`)
- ✅ Test data is available in the database
- ❌ **Session variables are not set** (`esewa_id`, `khalti_id`)
- ❌ **User is not logged in to the main system**

## Complete Solution

### Step 1: Login to Main System
**You must first login to the main PahunaGhar system:**

1. Go to: `http://localhost/PahunaGhar-main/login.php`
2. Login with your main account credentials
3. Verify you can access the homepage and user dashboard

### Step 2: Create or Access a Booking
**You need an active booking to test payments:**

1. Navigate to a hotel and make a booking, OR
2. Go to your existing bookings: `http://localhost/PahunaGhar-main/user_bookings.php`
3. Find a booking with "Pay" button

### Step 3: Choose Payment Method
**Select your preferred payment gateway:**

1. Click "Pay" on a booking
2. Choose either "eSewa" or "Khalti" payment method
3. You'll be redirected to the payment gateway login page

### Step 4: Login to Payment Gateway
**Use the test credentials provided:**

#### For eSewa:
- **URL**: `http://localhost/PahunaGhar-main/esewa_login.php?booking_id=YOUR_BOOKING_ID`
- **eSewa ID**: `9824004077`
- **MPIN**: `5470`

#### For Khalti:
- **URL**: `http://localhost/PahunaGhar-main/khalti_login.php?booking_id=YOUR_BOOKING_ID`
- **Khalti ID**: `9824004077`
- **MPIN**: `2020`

### Step 5: Complete Payment
**After successful payment gateway login:**

1. You'll see the booking details and payment amount
2. Click "Pay Via eSewa" or "Pay Via Khalti"
3. Enter the same MPIN again for payment confirmation
4. Payment will be processed and booking status updated

## Test the System

### Quick Test Script
I've created a test script to help you verify the system:

**URL**: `http://localhost/PahunaGhar-main/test_payment_login.php`

This script will:
- Check your login status
- Test payment gateway logins
- Verify MPIN functionality
- Show current session variables

### Manual Testing Steps

1. **Test Main Login**:
   ```
   http://localhost/PahunaGhar-main/login.php
   ```

2. **Test eSewa Login**:
   ```
   http://localhost/PahunaGhar-main/esewa_login.php?booking_id=1
   ```

3. **Test Khalti Login**:
   ```
   http://localhost/PahunaGhar-main/khalti_login.php?booking_id=1
   ```

## Common Issues and Solutions

### Issue 1: "User not logged in"
**Solution**: Login to the main system first at `login.php`

### Issue 2: "Invalid eSewa ID or MPIN"
**Solution**: Use the correct test credentials:
- eSewa: ID=`9824004077`, MPIN=`5470`
- Khalti: ID=`9824004077`, MPIN=`2020`

### Issue 3: "Booking not found"
**Solution**: Make sure you have an active booking and use the correct booking ID

### Issue 4: Session variables not set
**Solution**: Complete the payment gateway login process first

## Database Verification

The system has these test accounts in the database:

### eSewa Users Table
```sql
SELECT * FROM esewa_users;
```
**Result**: ID=`9824004077`, MPIN=`5470`

### Khalti Users Table
```sql
SELECT * FROM khalti_users;
```
**Result**: ID=`9824004077`, MPIN=`2020`

## Debug Tools Available

1. **`debug_mpin_issue.php`** - Check database tables and session status
2. **`debug_mpin_live.php`** - Real-time debugging of MPIN verification
3. **`test_payment_login.php`** - Comprehensive payment system test
4. **`check_esewa_mpin.php`** - eSewa MPIN verification endpoint
5. **`check_khalti_mpin.php`** - Khalti MPIN verification endpoint

## Expected Flow

```
1. Main Login (login.php) → Sets $_SESSION['logged_in'] = true
2. Create/Access Booking → Navigate to booking with payment option
3. Choose Payment Method → Redirect to esewa_login.php or khalti_login.php
4. Payment Gateway Login → Sets $_SESSION['esewa_id'] or $_SESSION['khalti_id']
5. Payment Confirmation → Enter MPIN for final payment
6. Payment Success → Booking status updated to "paid"
```

## Security Notes

- The system uses prepared statements to prevent SQL injection
- MPINs are stored securely in the database
- Session variables are properly managed
- Error logging is enabled for debugging

## Next Steps

1. **Follow the complete flow** as outlined above
2. **Use the test credentials** provided
3. **Check the debug tools** if you encounter issues
4. **Verify each step** before proceeding to the next

If you still have issues after following this guide, run the test script and share the output for further assistance. 