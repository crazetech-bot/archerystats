<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registered</title>
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
        .info-table td:first-child { color: #94a3b8; font-weight: 600; width: 120px; }
        .info-table td:last-child { color: #0f172a; font-weight: 600; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .badge-archer { background: #ede9fe; color: #5b21b6; }
        .badge-coach { background: #ccfbf1; color: #0f766e; }
        .badge-club_admin { background: #fef9c3; color: #854d0e; }
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
            <h1>New User Registered</h1>
            <p>Archery Stats Management System</p>
        </div>
        <div class="body">
            <p>A new user has just created an account on <strong>SportDNS</strong>.</p>

            <table class="info-table">
                <tr>
                    <td>Name</td>
                    <td>{{ $newUser->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $newUser->email }}</td>
                </tr>
                <tr>
                    <td>Role</td>
                    <td>
                        <span class="badge badge-{{ $newUser->role }}">
                            {{ str_replace('_', ' ', $newUser->role) }}
                        </span>
                    </td>
                </tr>
                @if($newUser->role === 'club_admin' && $newUser->club)
                <tr>
                    <td>Club</td>
                    <td>{{ $newUser->club->name }}</td>
                </tr>
                @endif
                <tr>
                    <td>Registered</td>
                    <td>{{ $newUser->created_at->format('d M Y, g:i A') }}</td>
                </tr>
            </table>

            <a href="{{ url('/admin/settings') }}" class="btn">View Admin Dashboard</a>

            <p style="font-size:13px; color:#94a3b8; margin-top:24px;">
                This is an automated notification. No action is required unless you need to review or manage the new account.
            </p>
        </div>
        <div class="footer">
            <p>Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
