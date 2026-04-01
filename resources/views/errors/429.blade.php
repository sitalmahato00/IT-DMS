<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - Too Many Requests</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            color: #f5576c;
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
        <div class="error-code">429</div>
        <h1>Too Many Requests</h1>
        <p>You've made too many requests in a short amount of time. Please wait a few minutes and try again.</p>
        <div class="actions">
            <a href="/">Go Home</a>
        </div>
    </div>
</body>
</html>
