<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Request</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .button { 
            display: inline-block; 
            padding: 12px 24px; 
            background-color: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0069d9;
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #eee; 
            font-size: 12px; 
            color: #666;
            text-align: center;
        }
        .alert {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .link {
            word-break: break-all;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>
        
        <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>
        
        <p>We received a request to reset your password for your {{ config('app.name') }} account.</p>
        
        <p>Click the button below to create a new password:</p>
        
        <p style="text-align: center;">
            <a href="{{ $url }}" class="button">Reset My Password</a>
        </p>
        
        <div class="alert">
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p><a href="{{ $url }}" class="link">{{ $url }}</a></p>
        </div>
        
        <p>This password reset link will expire in 24 hours for security reasons.</p>
        
        <p>If you didn't request this password reset, please ignore this email. Your account remains secure.</p>
        
        <p>For security reasons, we recommend that you don't share this email with anyone.</p>
        
        <div class="footer">
            <p>This email was sent automatically. Please do not reply to this message.</p>
            <p>If you need assistance, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>