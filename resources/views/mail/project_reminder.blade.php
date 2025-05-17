<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reminder Deadline Proyek</title>
    <style>
        /* [Tetap gunakan CSS dari template sebelumnya] */
        .priority-ceo {
            background-color: #fff8e6;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="email-logo">ProjectHub</div>
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
                <p><strong>Ini adalah notifikasi prioritas untuk CEO:</strong></p>
            @endif

            <p>Proyek <strong>{{ $project->name }}</strong> akan mencapai deadline besok:</p>

            <div class="project-card">
                <!-- [Tetap gunakan struktur card sebelumnya] -->
                @if ($isCEO)
                    <div class="project-detail">
                        <i>👥</i>
                        <span>Total Member: {{ $project->members->count() }}</span>
                    </div>
                @endif
            </div>

            <!-- [Tombol dan footer tetap sama] -->
        </div>
    </div>
</body>

</html>
