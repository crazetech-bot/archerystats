<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Club is Live</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #047857 0%, #10b981 100%); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: 0.02em; }
        .header p { color: rgba(255,255,255,0.85); font-size: 13px; margin: 0; }
        .body { padding: 36px 40px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .url-box { background: #ecfdf5; border-radius: 12px; padding: 18px 20px; text-align: center; margin: 22px 0; }
        .url-box .label { color: #059669; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 6px; }
        .url-box a { color: #047857; font-size: 18px; font-weight: 800; text-decoration: none; word-break: break-all; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #047857, #10b981);
               color: #fff; padding: 14px 24px; border-radius: 10px; font-weight: 700;
               font-size: 15px; text-decoration: none; margin: 28px 0 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
@php($clubUrl = 'http://' . $club->slug . '.' . config('app.root_domain', 'sportdns.com'))
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Your Club is Live! 🎉</h1>
            <p>Archery Stats Management System</p>
        </div>
        <div class="body">
            <p>Hi {{ $admin->name }},</p>
            <p>Great news — <strong>{{ $club->name }}</strong> has been approved and is now
               live on SportDNS. You can log in and start managing your archers, coaches,
               and competitions.</p>

            <div class="url-box">
                <p class="label">Your club URL</p>
                <a href="{{ $clubUrl }}">{{ $club->slug }}.{{ config('app.root_domain', 'sportdns.com') }}</a>
            </div>

            <a href="{{ $clubUrl }}/login" class="btn">Go to My Club Dashboard</a>

            <p style="font-size:13px; color:#94a3b8; margin-top:24px;">
                Log in with the email and password you used during registration.
            </p>
        </div>
        <div class="footer">
            <p>Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
