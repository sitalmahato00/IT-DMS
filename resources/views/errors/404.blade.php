<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            color: white;
            padding: 20px;
            max-width: 600px;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        h1 {
            font-size: 32px;
            margin: 20px 0;
        }
        p {
            font-size: 18px;
            opacity: 0.9;
            line-height: 1.6;
        }
        .actions {
            margin-top: 30px;
        }
        a {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background: white;
            color: #667eea;
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
        a.secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1>Page Not Found</h1>
        <p>Sorry! The page you're looking for doesn't exist or has been moved.</p>
        <div class="actions">
            <a href="/">Go Home</a>
            <a href="javascript:history.back()" class="secondary">Go Back</a>
        </div>
    </div>
</body>
</html>

