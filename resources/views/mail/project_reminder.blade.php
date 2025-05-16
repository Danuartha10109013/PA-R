<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Project - {{ $project->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-body {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .project-name {
            color: #4a5568;
            font-weight: 500;
            margin: 15px 0;
        }

        .project-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .detail-item {
            margin-bottom: 10px;
            display: flex;
        }

        .detail-label {
            font-weight: 500;
            min-width: 120px;
            color: #4a5568;
        }

        .detail-value {
            color: #2d3748;
        }

        .cta-button {
            display: inline-block;
            background: #4f46e5;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
            text-align: center;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-not_started {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .badge-in_progress {
            background-color: #bee3f8;
            color: #2b6cb0;
        }

        .badge-completed {
            background-color: #c6f6d5;
            color: #276749;
        }

        .days-left {
            font-size: 16px;
            font-weight: 500;
            color: #4f46e5;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Reminder Project</h1>
        </div>

        <div class="email-body">
            <div class="greeting">
                Halo <strong>{{ $project->user->name }}</strong>,
            </div>

            <p>Berikut reminder untuk project yang akan segera dimulai:</p>

            <div class="days-left">
                ⏰ Dimulai dalam <strong>{{ $daysLeft }} hari</strong> lagi
            </div>

            <div class="project-details">
                <div class="project-name">
                    🚀 {{ $project->name }}
                </div>

                <div class="detail-item">
                    <div class="detail-label">Tanggal Mulai</div>
                    <div class="detail-value">{{ $project->start_date->format('d M Y') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="badge badge-{{ str_replace(' ', '_', $project->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                </div>

                @if ($project->description)
                    <div class="detail-item">
                        <div class="detail-label">Deskripsi</div>
                        <div class="detail-value">{{ $project->description }}</div>
                    </div>
                @endif

                @if ($project->end_date)
                    <div class="detail-item">
                        <div class="detail-label">Tanggal Selesai</div>
                        <div class="detail-value">{{ $project->end_date->format('d M Y') }}</div>
                    </div>
                @endif
            </div>

            <p>Silakan persiapkan segala kebutuhan untuk project ini agar berjalan lancar.</p>

            <center>
                <a href="{{ route('projects.show', $project->id) }}" class="cta-button">
                    Lihat Detail Project
                </a>
            </center>
        </div>

        <div class="footer">
            <p>Terima kasih telah menggunakan {{ config('app.name') }}</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
