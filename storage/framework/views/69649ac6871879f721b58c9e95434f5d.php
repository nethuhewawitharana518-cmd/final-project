<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #1e1e1e;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #2d2d2d;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .header {
            background: linear-gradient(135deg, #ff6b00, #ff8800);
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .message {
            color: #a0a0a0;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .otp-code {
            display: inline-block;
            background-color: rgba(255, 107, 0, 0.1);
            border: 1px dashed #ff6b00;
            color: #ff6b00;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 0.15em;
            padding: 12px 30px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .expiry-text {
            color: #666666;
            font-size: 12px;
            margin-top: 15px;
        }
        .footer {
            background-color: #161616;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #2d2d2d;
            font-size: 12px;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verification Code</h1>
        </div>
        <div class="content">
            <p class="message">You have requested to reset your password. Use the following 6-digit One-Time Password (OTP) to complete the verification process:</p>
            
            <div class="otp-code"><?php echo e($otp); ?></div>
            
            <p class="expiry-text">This code is valid for 15 minutes. If you did not make this request, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; <?php echo e(date('Y')); ?> FoodRescue. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\projectn_dark\resources\views/emails/forgot-password-otp.blade.php ENDPATH**/ ?>