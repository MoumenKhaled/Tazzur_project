<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4A90E2;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .code {
            font-size: 24px;
            color: #e63946; 
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our Service</h1>
        <p>Your verification code is: <strong class="code">{{ $code }}</strong></p>
    </div>
</body>
</html>
