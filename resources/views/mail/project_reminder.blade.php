<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reminder Deadline Proyek</title>
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

        .priority-ceo {
            background-color: #fff8e6;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
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
            display: flex;
            align-items: center;
        }

        .urgency-badge {
            background-color: #ff4757;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
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
            font-size: 16px;
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
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
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
            font-size: 16px;
        }

        .highlight {
            color: #667eea;
            font-weight: 500;
        }

        .ceo-alert {
            background-color: #fff8e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }

        .ceo-alert-title {
            color: #d4a017;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .ceo-alert-title i {
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="email-logo">Task Manager</div>
            <h1 class="email-title">
                @if ($isCEO)
                    🚨 Prioritas CEO
                @else
                    ⏰ Deadline Mendekat
                @endif
            </h1>
        </div>

        <div class="email-body @if ($isCEO) priority-ceo @endif">
            <p class="greeting">Halo {{ $user->name }},</p>

            @if ($isCEO)
                <div class="ceo-alert">
                    <div class="ceo-alert-title">
                        <i>⚠️</i>
                        <span>Notifikasi Prioritas untuk CEO</span>
                    </div>
                    <p>Anda menerima email ini sebagai notifikasi khusus untuk direview.</p>
                </div>
            @endif

            <p>Proyek <strong>{{ $project->name }}</strong> akan mencapai deadline:</p>

            <div class="project-card">
                <h2 class="project-name">
                    {{ $project->name }}
                    <span class="urgency-badge">BESOK</span>
                </h2>
                <div class="project-detail">
                    <i>📅</i>
                    <span>Deadline: <span class="highlight">{{ $project->end_date->format('l, d F Y') }}</span></span>
                </div>
                <div class="project-detail">
                    <i>⏳</i>
                    <span>Status: <span
                            class="highlight">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span></span>
                </div>
                @if ($isCEO)
                    <div class="project-detail">
                        <i>👥</i>
                        <span>Total Anggota Tim: <span class="highlight">{{ $project->members->count() }}</span></span>
                    </div>
                    <div class="project-detail">
                        <i>👤</i>
                        <span>Pemimpin Proyek: <span class="highlight">{{ $project->user->name }}</span></span>
                    </div>
                @endif
            </div>

            <p>Segera tinjau dan pastikan proyek ini selesai tepat waktu!</p>

            <center>
                <a href="{{ route('projects.show', $project) }}" class="cta-button">
                    Lihat Detail Proyek
                </a>
            </center>

            <p style="margin-top: 25px;">
                <small>
                    <i>Anda menerima email ini karena terdaftar dalam sistem Task Manager.</i>
                </small>
            </p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} Task Manager. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
