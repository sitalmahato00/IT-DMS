<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Department Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f5f7fa;
            padding: 20px 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .email-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .email-header p {
            font-size: 14px;
            opacity: 0.95;
        }
        
        /* Logo/Icon */
        .email-logo {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        /* Body Content */
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .email-body p {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 15px;
            line-height: 1.8;
        }
        
        /* Credentials Box */
        .credentials-box {
            background-color: #f9fafb;
            border-left: 4px solid #dc2626;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .credential-item {
            margin-bottom: 12px;
            font-size: 14px;
        }
        .credential-label {
            font-weight: 700;
            color: #1f2937;
            display: inline-block;
            min-width: 140px;
        }
        .credential-value {
            color: #dc2626;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        /* Lists */
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .feature-list li {
            padding: 8px 0 8px 30px;
            position: relative;
            font-size: 14px;
            color: #4b5563;
        }
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
            font-size: 18px;
        }
        
        /* CTA Button */
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 14px 42px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            margin: 25px 0;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        }
        
        /* Security Note */
        .security-note {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            padding: 15px 20px;
            border-radius: 6px;
            margin: 25px 0;
            font-size: 13px;
            color: #854d0e;
        }
        .security-note strong {
            color: #7c2d12;
        }
        
        /* Info Box */
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px 20px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 13px;
            color: #1e40af;
        }
        
        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .email-footer p {
            margin-bottom: 10px;
        }
        .email-footer a {
            color: #dc2626;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        
        /* Social Links */
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: #e5e7eb;
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin: 0 5px;
            color: #dc2626;
            text-decoration: none;
            font-size: 14px;
        }
        .social-links a:hover {
            background-color: #dc2626;
            color: white;
        }
        
        /* Divider */
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 25px 0;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            .email-body, .email-footer {
                padding: 25px 20px;
            }
            .email-header {
                padding: 30px 20px;
            }
            .email-header h1 {
                font-size: 22px;
            }
            .credential-label {
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            @yield('content')
        </div>
    </div>
</body>
</html>
