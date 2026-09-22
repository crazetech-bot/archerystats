<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Request</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #312e81 0%, #4338ca 100%); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: 0.02em; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin: 0; }
        .body { padding: 36px 40px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
        .info-table td:first-child { color: #94a3b8; font-weight: 600; width: 130px; }
        .info-table td:last-child { color: #0f172a; font-weight: 600; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #4338ca, #6366f1);
               color: #fff; padding: 14px 24px; border-radius: 10px; font-weight: 700;
               font-size: 15px; text-decoration: none; margin: 28px 0 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Archer Transfer Request</h1>
            <p>Archery Stats Management System</p>
        </div>
        <div class="body">
            <p>An archer has requested to <strong>transfer their primary membership</strong> to your club.
               The request is awaiting your approval.</p>

            <table class="info-table">
                <tr>
                    <td>Archer</td>
                    <td>{{ $transfer->archer?->user?->name ?? 'Unknown' }}</td>
                </tr>
                <tr>
                    <td>Ref No</td>
                    <td>{{ $transfer->archer?->ref_no ?? '—' }}</td>
                </tr>
                <tr>
                    <td>From Club</td>
                    <td>{{ $transfer->fromClub?->name ?? 'Unaffiliated' }}</td>
                </tr>
                <tr>
                    <td>To Club</td>
                    <td>{{ $transfer->toClub->name }}</td>
                </tr>
                <tr>
                    <td>Expires</td>
                    <td>{{ $transfer->expires_at->format('d M Y, g:i A') }}</td>
                </tr>
            </table>

            <a href="{{ 'http://' . $transfer->toClub->slug . '.' . config('app.root_domain', 'sportdns.com') . '/members' }}" class="btn">
                Review in Members Page
            </a>

            <p style="font-size:13px; color:#94a3b8; margin-top:24px;">
                Approving moves the archer's primary membership to your club. Their previous club
                membership is kept as secondary.
            </p>
        </div>
        <div class="footer">
            <p>Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
