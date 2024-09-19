<!-- resources/views/emails/overdue_notification.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Overdue Book Notification</title>
</head>
<body>
    <h1>Dear {{ $userName }},</h1>

    <p>This is a reminder that the following book is overdue:</p>

    <ul>
        <li>Book Title: {{ $bookTitle }}</li>
        <li>Due Date: {{ $tillDate }}</li>
    </ul>

    <p>Please return the book as soon as possible to avoid further penalties.</p>

    <p>Thank you,</p>
    <p>Atom Library</p>
</body>
</html>
