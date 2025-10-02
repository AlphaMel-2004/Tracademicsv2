# Gmail Configuration for TracAdemics Password Reset

## Option 1: Using Gmail SMTP (Recommended for Production)

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings
2. Enable 2-Factor Authentication if not already enabled

### Step 2: Generate App Password
1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Click on "Security" in the left sidebar
3. Scroll down to "How you sign in to Google" section
4. Click on "2-Step Verification" (must be enabled first)
5. Scroll down and click on "App passwords" at the bottom
6. In the "Select app" dropdown, choose "Mail"
7. In the "Select device" dropdown, choose "Other (Custom name)"
8. Type "TracAdemics" as the app name
9. Click "Generate"
10. Copy the generated 16-character password (it will be shown in yellow boxes)

### Step 3: Update .env File
Replace these values in your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_actual_gmail@gmail.com
MAIL_PASSWORD=your_16_character_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@brokenshire.edu.ph"
MAIL_FROM_NAME="TracAdemics"
```

## Option 2: Using Mailtrap (For Testing)

### Step 1: Create Mailtrap Account
1. Go to [mailtrap.io](https://mailtrap.io)
2. Sign up for a free account
3. Create a new inbox

### Step 2: Get SMTP Credentials
1. Click on your inbox
2. Go to "SMTP Settings"
3. Copy the credentials

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@brokenshire.edu.ph"
MAIL_FROM_NAME="TracAdemics"
```

## Option 3: Log Driver (For Development Only)
If you just want to see the email content without sending:
```env
MAIL_MAILER=log
```
Check `storage/logs/laravel.log` for email content.

## After Configuration:
1. Run: `php artisan config:cache`
2. Test the forgot password functionality
3. Check your email (Gmail) or Mailtrap inbox

## Troubleshooting:
- **Google Workspace Account**: If you get "2-Step Verification is disabled for your account. Contact your admin", your organization controls security settings. Use Mailtrap instead.
- Make sure 2FA is enabled on Gmail (personal accounts only)
- Use App Password, not your regular Gmail password
- Check spam/junk folder
- Verify email address exists in your user database
- **Quick Test**: Use `MAIL_MAILER=log` to see emails in `storage/logs/laravel.log`