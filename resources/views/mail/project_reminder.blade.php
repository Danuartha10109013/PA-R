<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            padding: 20px;
            background-color: #ffffff;
            box-sizing: border-box;
        }

        .email-header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 5px 5px 0 0;
        }

        .email-body {
            padding: 20px;
            color: #333;
        }

        .email-footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            color: #777;
            border-radius: 0 0 5px 5px;
        }

        h1 {
            font-size: 24px;
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            margin: 10px 0;
        }

        .highlight {
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Project Reminder</h2>
        </div>
        <div class="email-body">
            <h1>Reminder: Project "{{ $reminder->project->name }}"</h1>
            <p>Hello {{ $reminder->user->name }},</p>
            <p>This is a reminder that the project <span class="highlight">"{{ $reminder->project->name }}"</span> will
                start tomorrow, on
                {{ \Carbon\Carbon::parse($reminder->project->start_date)->format('l, F j, Y') }}.</p>
            <p>Good luck with the project!</p>
        </div>
        <div class="email-footer">
            <p>Best regards, <br> The Project Team</p>
        </div>
    </div>
</body>

</html>
