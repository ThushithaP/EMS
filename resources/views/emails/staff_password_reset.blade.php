<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Password Reset</h2>
        <p>Hello {{ $mailData['name'] }},</p>
        <p>Your account password reset successfully. Here are your login credentials with new password:</p>
        <ul>
            <li><strong>Email:</strong> {{ $mailData['email'] }}</li>
            <li><strong>Password:</strong> {{ $mailData['password'] }}</li>
        </ul>
        <p>Please keep your credentials secure and do not share them with anyone.</p>
        <p>If you have any questions or need further assistance, feel free to contact us.</p>
        <p>Best regards,<br> [Your Company Name]</p>
    </div>
</body>
</html>