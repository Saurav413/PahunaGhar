# Gmail SMTP Setup Guide for Password Reset Emails

## Step 1: Enable 2-Factor Authentication on Your Gmail Account

1. Go to your Google Account settings: https://myaccount.google.com/
2. Click on "Security" in the left sidebar
3. Under "Signing in to Google," click on "2-Step Verification"
4. Follow the steps to enable 2-factor authentication

## Step 2: Generate an App Password

1. Go to your Google Account settings: https://myaccount.google.com/
2. Click on "Security" in the left sidebar
3. Under "Signing in to Google," click on "App passwords"
4. Select "Mail" as the app and "Other" as the device
5. Click "Generate"
6. Copy the 16-character app password that appears

## Step 3: Configure Email Settings

1. Open the file `email_config.php` in your project
2. Replace the placeholder values with your actual Gmail credentials:

```php
// Replace these values:
define('SMTP_USERNAME', 'your_gmail@gmail.com'); // Your actual Gmail address
define('SMTP_PASSWORD', 'your_app_password'); // The 16-character app password from Step 2
define('FROM_EMAIL', 'your_gmail@gmail.com'); // Your actual Gmail address
```

**Example:**
```php
define('SMTP_USERNAME', 'myhotel@gmail.com');
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // Your app password
define('FROM_EMAIL', 'myhotel@gmail.com');
```

## Step 4: Test the Email Functionality

1. Make sure your XAMPP server is running
2. Go to your website's login page
3. Click "Forgot Password?"
4. Enter a valid email address that exists in your database
5. Submit the form
6. Check the email address you entered for the password reset link

## Troubleshooting

### Issue: "SMTP connect() failed" Error
**Solution:** 
- Make sure you're using the app password, not your regular Gmail password
- Verify that 2-factor authentication is enabled
- Check that the SMTP settings are correct

### Issue: "Authentication failed" Error
**Solution:**
- Double-check your Gmail address and app password
- Make sure there are no extra spaces in the credentials
- Regenerate the app password if needed

### Issue: Emails not being received
**Solution:**
- Check your spam/junk folder
- Verify the email address you're testing with exists in your database
- Check the error logs in your XAMPP error log

### Issue: "Connection refused" Error
**Solution:**
- Make sure your firewall isn't blocking port 587
- Try using port 465 with SSL instead of TLS
- Check if your hosting provider allows SMTP connections

## Alternative: Using Gmail with SSL (Port 465)

If TLS doesn't work, you can try SSL instead. Update your `email_config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465); // Changed from 587
define('SMTP_SECURE', 'ssl'); // Changed from 'tls'
```

## Security Notes

1. **Never commit your email credentials to version control**
2. **Use environment variables in production**
3. **Regularly rotate your app passwords**
4. **Consider using a dedicated email service like SendGrid for production**

## Testing Checklist

- [ ] 2-factor authentication enabled on Gmail
- [ ] App password generated and copied correctly
- [ ] `email_config.php` updated with correct credentials
- [ ] Database table `password_resets` created
- [ ] Valid email address exists in `user_register_form` table
- [ ] XAMPP server running
- [ ] No firewall blocking SMTP connections

## Common Gmail SMTP Settings

| Setting | Value |
|---------|-------|
| SMTP Server | smtp.gmail.com |
| Port (TLS) | 587 |
| Port (SSL) | 465 |
| Security | TLS or SSL |
| Authentication | Required |

Once configured correctly, the password reset emails should be sent to Gmail addresses successfully! 