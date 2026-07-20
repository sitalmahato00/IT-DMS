<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #991b1b;
        }
        .timer {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #dc2626;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .timer-value {
            font-size: 48px;
            font-weight: 700;
            color: #dc2626;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .timer-label {
            font-size: 14px;
            color: #7f1d1d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        a {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset</h1>
            <p>Manmohan Memorial Polytechnic</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $notifiable->name }}!</div>

            <div class="message">
                You are receiving this email because we received a password reset request for your account. If you did not request a password reset, you can safely ignore this email.
            </div>

            <div class="button-container">
                <a href="{{ $actionUrl }}" class="button">Reset Password</a>
            </div>

            <div class="timer">
                <div class="timer-label">⏱️ Link Expires In</div>
                <div class="timer-value">60:00</div>
                <div style="font-size: 12px; color: #7f1d1d;">Minutes : Seconds</div>
            </div>

            <div class="divider"></div>

            <div class="divider"></div>

            <div class="message">
                <strong>🔒 Security Note:</strong> Do not share this email or the reset link with anyone else. For your protection, keep this email private.
            </div>

            <div style="background-color: #fef3c7; border: 1px solid #fcd34d; padding: 15px; border-radius: 6px; margin-top: 25px; font-size: 13px; color: #854d0e;">
                <strong>If the button doesn't work:</strong> You can also copy and paste this link into your browser:
                <div style="word-break: break-all; margin-top: 10px; font-family: 'Courier New', monospace; background-color: rgba(255,255,255,0.5); padding: 10px; border-radius: 4px;">
                    {{ $actionUrl }}
                </div>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;">© 2026 Manmohan Memorial Polytechnic</p>
            <p style="margin: 0;">All Rights Reserved | Streamlining Academic Operations</p>
            <p style="margin: 10px 0 0 0; color: #9ca3af;">
                Do not share this email or the reset link with anyone else.
            </p>
        </div>
    </div>
</body>
</html>

