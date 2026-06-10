<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Awaiting Approval</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: 0.02em; }
        .header p { color: rgba(255,255,255,0.85); font-size: 13px; margin: 0; }
        .body { padding: 36px 40px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
        .info-table td:first-child { color: #94a3b8; font-weight: 600; width: 120px; }
        .info-table td:last-child { color: #0f172a; font-weight: 600; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #b45309, #f59e0b);
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
            <h1>Club Awaiting Approval</h1>
            <p>Archery Stats Management System</p>
        </div>
        <div class="body">
            <p>A new club has verified its email and is <strong>awaiting your approval</strong>.
               Its subdomain stays offline until you activate it.</p>

            <table class="info-table">
                <tr>
                    <td>Club</td>
                    <td>{{ $club->name }}</td>
                </tr>
                <tr>
                    <td>Subdomain</td>
                    <td>{{ $club->slug }}.{{ config('app.root_domain', 'sportdns.com') }}</td>
                </tr>
                <tr>
                    <td>Admin</td>
                    <td>{{ $admin->name }}</td>
                </tr>
                <tr>
                    <td>Admin Email</td>
                    <td>{{ $admin->email }}</td>
                </tr>
                @if($club->state)
                <tr>
                    <td>State</td>
                    <td>{{ $club->state }}</td>
                </tr>
                @endif
            </table>

            <a href="{{ route('admin.clubs.show', $club) }}" class="btn">Review &amp; Approve Club</a>

            <p style="font-size:13px; color:#94a3b8; margin-top:24px;">
                Approve by clicking <strong>Activate</strong> on the club page. The club admin
                will be notified automatically once the club goes live.
            </p>
        </div>
        <div class="footer">
            <p>Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
