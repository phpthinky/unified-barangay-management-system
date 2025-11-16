<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 40px 30px;
            border-radius: 0 0 10px 10px;
        }
        .verification-code {
            background: white;
            border: 3px dashed #667eea;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 10px;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Welcome to Barangay Portal!</h1>
        <p>Verify your email address to get started</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $user->first_name }}</strong>,</p>

        <p>Thank you for registering at <strong>{{ $user->barangay->name ?? 'Barangay Portal' }}</strong>!</p>

        <p>To complete your registration and access barangay services, please verify your email address using the code below:</p>

        <div class="verification-code">
            <p style="margin: 0; color: #666; font-size: 14px;">YOUR VERIFICATION CODE</p>
            <div class="code">{{ $verificationCode }}</div>
            <p style="margin: 10px 0 0 0; color: #666; font-size: 12px;">Valid for 15 minutes</p>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>This code will expire in <strong>15 minutes</strong></li>
                <li>Do not share this code with anyone</li>
                <li>If you didn't request this, please ignore this email</li>
            </ul>
        </div>

        <p>After verification, you can:</p>
        <ul>
            <li>✅ Request barangay documents and certificates</li>
            <li>✅ Apply for business permits</li>
            <li>✅ File complaints and track cases</li>
            <li>✅ Access all barangay services</li>
        </ul>

        <p>If you have any questions, please contact the barangay office.</p>

        <p style="margin-top: 30px;">
            Best regards,<br>
            <strong>{{ $user->barangay->name ?? 'Barangay Portal' }} Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Barangay Portal. All rights reserved.</p>
    </div>
</body>
</html>