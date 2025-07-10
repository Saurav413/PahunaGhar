# Gmail SMTP Setup - Step by Step Guide

## Current Issue
Email sending is failing because Gmail credentials are not configured properly.

## Step 1: Enable 2-Factor Authentication

1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Click on **"Security"** in the left sidebar
3. Under **"Signing in to Google"**, click on **"2-Step Verification"**
4. Follow the steps to enable 2-factor authentication
5. You'll need your phone to complete this step

## Step 2: Generate an App Password

1. Go back to [Google Account Settings](https://myaccount.google.com/)
2. Click on **"Security"** in the left sidebar
3. Under **"Signing in to Google"**, click on **"App passwords"**
4. You may need to sign in again
5. Select **"Mail"** as the app
6. Select **"Other"** as the device (or choose your device)
7. Click **"Generate"**
8. **Copy the 16-character password** that appears (it will look like: `abcd efgh ijkl mnop`)

## Step 3: Update Email Configuration

1. Open the file `email_config.php` in your project
2. Replace the placeholder values with your actual Gmail credentials:

```php
<?php
// Email Configuration for Gmail SMTP
// Replace these values with your actual Gmail credentials

// Gmail SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);

// Your Gmail credentials
define('SMTP_USERNAME', 'your_actual_email@gmail.com'); // Replace with your Gmail address
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // Replace with your 16-char app password
define('FROM_EMAIL', 'your_actual_email@gmail.com'); // Replace with your Gmail address
define('FROM_NAME', 'PahunaGhar');
?>
```

**Example:**
```php
define('SMTP_USERNAME', 'myhotel@gmail.com');
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // Your actual app password
define('FROM_EMAIL', 'myhotel@gmail.com');
```

## Step 4: Test the Configuration

1. Visit: `http://localhost/PahunaGhar-main/email_test.php`
2. This will show your current configuration status
3. If credentials are correct, you'll see "✓ Configured" for username and password
4. To test email sending, add `?test_email=your_email@gmail.com` to the URL

## Step 5: Test Password Reset

1. Go to `http://localhost/PahunaGhar-main/login.php`
2. Click "Forgot Password?"
3. Enter a valid email address from your database
4. Submit the form
5. Check the email address for the reset link

## Troubleshooting

### Issue: "SMTP Error: Could not authenticate"
**Solution:**
- Make sure you're using the app password, not your regular Gmail password
- Verify that 2-factor authentication is enabled
- Regenerate the app password if needed

### Issue: "Connection refused"
**Solution:**
- Try using SSL instead of TLS:
  ```php
  define('SMTP_PORT', 465);
  define('SMTP_SECURE', 'ssl');
  ```

### Issue: "Less secure app access"
**Solution:**
- Enable 2-factor authentication (recommended)
- Or enable "Less secure app access" in Google Account settings (not recommended)

### Issue: Emails not received
**Solution:**
- Check spam/junk folder
- Verify the email address exists in your database
- Check the error logs for specific error messages

## Alternative: Use SSL Instead of TLS

If TLS (port 587) doesn't work, try SSL (port 465):

```php
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');
```

## Security Notes

1. **Never commit your email credentials to version control**
2. **Use environment variables in production**
3. **Regularly rotate your app passwords**
4. **Consider using a dedicated email service like SendGrid for production**

## Quick Test Commands

Test basic functionality:
```bash
curl http://localhost/PahunaGhar-main/email_test.php
```

Test with specific email:
```bash
curl "http://localhost/PahunaGhar-main/email_test.php?test_email=your_email@gmail.com"
```

## Expected Results

### Success:
- Email configuration shows "✓ Configured"
- SMTP connection successful
- Test email sent successfully
- Password reset emails are received

### Failure:
- Authentication errors
- Connection refused
- Configuration shows "❌ Not configured"

Once configured correctly, the password reset emails should be sent to Gmail addresses successfully! 