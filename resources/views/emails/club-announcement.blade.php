<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Announcement</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 36px 40px; text-align: center; border-bottom: 3px solid #f59e0b; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: 0.02em; }
        .header p { color: #f59e0b; font-size: 13px; font-weight: 600; margin: 0; }
        .body { padding: 36px 40px; }
        .body h2 { color: #0f172a; font-size: 19px; font-weight: 800; margin: 0 0 14px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .message { background: #f8fafc; border-left: 4px solid #f59e0b; border-radius: 0 10px 10px 0;
                   padding: 18px 20px; color: #334155; font-size: 15px; line-height: 1.7; }
        .meta { color: #94a3b8; font-size: 12.5px; margin-top: 20px; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #b45309, #f59e0b);
               color: #fff; padding: 14px 24px; border-radius: 10px; font-weight: 700;
               font-size: 15px; text-decoration: none; margin: 28px 0 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
@php($clubUrl = 'http://' . $announcement->club->slug . '.' . config('app.root_domain', 'sportdns.com'))
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Club Announcement</h1>
            <p>{{ $announcement->club->name }}</p>
        </div>
        <div class="body">
            <h2>{{ $announcement->title }}</h2>
            <div class="message">{!! nl2br(e($announcement->body)) !!}</div>
            <p class="meta">
                Posted by {{ $announcement->sender?->name ?? 'Club administrator' }}
                @if($announcement->coach) (Coach) @endif
                · {{ $announcement->created_at->format('d M Y, g:i A') }}
            </p>
            <a href="{{ $clubUrl }}/login" class="btn">Open Archery Stats</a>
        </div>
        <div class="footer">
            <p>You received this because you are a member of {{ $announcement->club->name }} on Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
