<?php
/**
 * Email Configuration
 * 
 * Instructions:
 * 1. For Gmail: Enable "Less secure app access" or use App Password
 * 2. For other providers: Update SMTP settings accordingly
 * 3. Make sure to replace the placeholder values below
 */

return [
    // SMTP Configuration
    'host' => 'smtp.gmail.com',           // SMTP server
    'port' => 587,                        // SMTP port (587 for TLS, 465 for SSL)
    'username' => 'khildatulinayah1988@gmail.com', // Your email address
    'password' => 'cwve bcpu ufsn psfz',    // Your password or app password
    'encryption' => 'tls',                // Encryption: 'tls', 'ssl', or null
    
    // Email From Settings
    'from' => [
        'address' => 'khildatulinayah1988@gmail.com', // Same as username or different
        'name' => 'Aplikasi Data Siswa'       // Display name
    ],
    
    // Additional Settings
    'timeout' => 30,                      // Connection timeout in seconds
    'debug' => false,                     // Enable debug mode (true/false)
];

/*
=== SETUP INSTRUCTIONS ===

1. GMAIL SETUP:
   - Login ke Gmail account Anda
   - Go to: https://myaccount.google.com/security
   - Enable "2-Step Verification" (if not already enabled)
   - Go to: https://myaccount.google.com/apppasswords
   - Generate new app password for "Mail" on "Windows Computer"
   - Copy the 16-character password and use it in 'password' field above

2. OTHER EMAIL PROVIDERS:
   - Outlook/Hotmail: smtp-mail.outlook.com:587
   - Yahoo: smtp.mail.yahoo.com:587
   - Custom domain: contact your hosting provider

3. TESTING:
   - Set 'debug' => true to see detailed error messages
   - Check your spam/junk folder if emails don't arrive
   - Make sure firewall isn't blocking SMTP connections

4. SECURITY NOTES:
   - Never commit your real password to version control
   - Consider using environment variables for production
   - Use app passwords instead of main account passwords
*/
