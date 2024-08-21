<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; }
        .header { background-color: #f8f9fa; padding: 10px; text-align: center; }
        .content { margin: 20px; }
        .footer { background-color: #f1f1f1; padding: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }} Notification</h1>
    </div>
    <div class="content">
        <p>Dear {{ $company->name }},</p>
        <p>Your company status has been updated to: <strong>{{ $status }}</strong>.</p>
    </div>
    <div class="footer">
        <p>Thank you for using our platform.</p>
    </div>
</body>
</html>
