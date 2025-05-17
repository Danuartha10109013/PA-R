<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Reminder: {{ $reminder->project->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-logo {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .email-title {
            font-size: 22px;
            font-weight: 500;
            margin: 0;
        }

        .email-body {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
        }

        .project-card {
            background: #f9f9ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
        }

        .project-name {
            font-size: 20px;
            font-weight: 600;
            color: #3a3a3a;
            margin-bottom: 10px;
        }

        .project-detail {
            display: flex;
            align-items: center;
            margin: 8px 0;
        }

        .project-detail i {
            color: #667eea;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
            text-align: center;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            background: #f5f7fa;
            color: #777;
            font-size: 14px;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }

        .highlight {
            color: #667eea;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="email-logo">Task Manager</div>
            <h1 class="email-title">Project Reminder</h1>
        </div>

        <div class="email-body">
            <p class="greeting">Hi {{ $reminder->user->name }},</p>
            <p>This is a friendly reminder about an upcoming project:</p>

            <div class="project-card">
                <h2 class="project-name">{{ $reminder->project->name }}</h2>
                <div class="project-detail">
                    <i>📅</i>
                    <span>Starts <span class="highlight">tomorrow</span> ({{ \Carbon\Carbon::parse($reminder->project->start_date)->format('l, F j, Y') }})</span>
                </div>
                <div class="project-detail">
                    <i>⏳</i>
                    <span>Status: <span class="highlight">{{ ucfirst($reminder->project->status) }}</span></span>
                </div>
                @if($reminder->project->end_date)
                <div class="project-detail">
                    <i>🏁</i>
                    <span>Target completion: {{ \Carbon\Carbon::parse($reminder->project->end_date)->format('M j, Y') }}</span>
                </div>
                @endif
            </div>

            <p>Make sure you're prepared for the kickoff!</p>

            <center>
                <a href="{{ route('projects.show', $reminder->project) }}" class="cta-button">View Project Details</a>
            </center>

            <p>Best of luck with the project! Let us know if you need any assistance.</p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} Task Manager. All rights reserved.</p>
            <p>If you didn't request this reminder, please ignore this email.</p>
        </div>
    </div>
</body>
</html>
