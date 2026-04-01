<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            color: #333;
            padding: 20px;
            max-width: 600px;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            opacity: 0.3;
        }
        h1 {
            font-size: 32px;
            margin: 20px 0;
            color: #333;
        }
        p {
            font-size: 18px;
            opacity: 0.8;
            line-height: 1.6;
            color: #555;
        }
        .actions {
            margin-top: 30px;
        }
        a {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .contact-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 14px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">500</div>
        <h1>Server Error</h1>
        <p>Something went wrong on our end. Our team has been notified and is working to fix the issue.</p>
        <div class="actions">
            <a href="/">Go Home</a>
            <a href="javascript:location.reload()" style="background: rgba(0,0,0,0.2); color: #333;">Retry</a>
        </div>
        <div class="contact-info">
            If the problem persists, please contact the support team at support@example.com
        </div>
    </div>
</body>
</html>
