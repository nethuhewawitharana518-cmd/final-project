<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #121212; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #F9FAFB;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #121212; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #1E1E1E; border: 1px solid #374151; border-radius: 16px; padding: 40px;">
                    <!-- Logo Header -->
                    <tr>
                        <td align="center" style="padding-bottom: 30px;">
                            <span style="font-size: 32px;">🥗</span>
                            <h2 style="margin: 10px 0 0 0; color: #ffffff; font-weight: 800; letter-spacing: -0.5px;">Food<span style="color: #FF6B00;">Rescue</span></h2>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td>
                            <h3 style="color: #ffffff; font-size: 20px; font-weight: 700; margin-top: 0;">Password Reset Request</h3>
                            <p style="color: #9CA3AF; font-size: 16px; line-height: 1.6; margin-bottom: 24px;">
                                We received a request to reset the password for your FoodRescue account. Click the button below to choose a new password. This link will expire shortly.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- CTA Button -->
                    <tr>
                        <td align="center" style="padding: 10px 0 30px 0;">
                            <a href="{{ $resetUrl }}" target="_blank" style="background-color: #FF6B00; color: #ffffff; padding: 14px 30px; text-decoration: none; font-size: 16px; font-weight: 700; border-radius: 9999px; display: inline-block; box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);">
                                Reset Password
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Fallback Link -->
                    <tr>
                        <td>
                            <p style="color: #9CA3AF; font-size: 14px; line-height: 1.6; border-top: 1px solid #374151; padding-top: 20px;">
                                If you're having trouble clicking the button, copy and paste the URL below into your web browser:
                            </p>
                            <p style="color: #FF6B00; font-size: 14px; word-break: break-all; margin-bottom: 24px;">
                                <a href="{{ $resetUrl }}" style="color: #FF6B00; text-decoration: none;">{{ $resetUrl }}</a>
                            </p>
                            <p style="color: #9CA3AF; font-size: 14px; line-height: 1.6; margin-bottom: 0;">
                                If you did not request a password reset, no further action is required.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top: 40px; border-top: 1px solid #374151; margin-top: 20px;">
                            <p style="color: #6B7280; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} FoodRescue Marketplace. Trincomalee, Sri Lanka.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
