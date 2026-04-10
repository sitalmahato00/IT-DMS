<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - Service Unavailable</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            color: #00f2fe;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">503</div>
        <h1>Service Under Maintenance</h1>
        <p>We're performing system maintenance and will be back online shortly. Please check back in a few minutes.</p>
        <div class="actions">
            <a href="/">Go Home</a>
        </div>
    </div>
</body>
</html>

