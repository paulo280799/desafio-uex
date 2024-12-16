<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .email-header img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .email-content {
            font-size: 16px;
            color: #333333;
            margin-bottom: 20px;
        }
        .reset-button {
            display: inline-block;
            background-color: #000000;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .reset-button:hover {
            background-color: #444444;
        }
        .email-footer {
            font-size: 12px;
            color: #aaaaaa;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">

        <div class="email-content">
            <h1>Forgot your password?</h1>
            <p>That's okay, it happens! Click on the button below to reset your password.</p>
        </div>
        <a href="{{ $url }}" class="reset-button">RESET YOUR PASSWORD</a>
        <div class="email-footer">
            <p>If you didn’t request a password reset, you can safely ignore this email.</p>
        </div>
    </div>
</body>
</html>
