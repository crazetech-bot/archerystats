<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer {{ $approved ? 'Approved' : 'Declined' }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { padding: 36px 40px; text-align: center;
                  background: {{ $approved ? 'linear-gradient(135deg, #047857 0%, #10b981 100%)' : 'linear-gradient(135deg, #6b7280 0%, #9ca3af 100%)' }}; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: 0.02em; }
        .header p { color: rgba(255,255,255,0.85); font-size: 13px; margin: 0; }
        .body { padding: 36px 40px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #047857, #10b981);
               color: #fff; padding: 14px 24px; border-radius: 10px; font-weight: 700;
               font-size: 15px; text-decoration: none; margin: 28px 0 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
@php($clubUrl = 'http://' . $transfer->toClub->slug . '.' . config('app.root_domain', 'sportdns.com'))
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Transfer {{ $approved ? 'Approved 🎉' : 'Declined' }}</h1>
            <p>Archery Stats Management System</p>
        </div>
        <div class="body">
            <p>Hi {{ $transfer->archer?->user?->name ?? 'there' }},</p>
            @if ($approved)
                <p>Your transfer request has been <strong>approved</strong>.
                   <strong>{{ $transfer->toClub->name }}</strong> is now your primary club.
                   @if ($transfer->fromClub)
                       Your membership at {{ $transfer->fromClub->name }} has been kept as secondary.
                   @endif
                </p>
                <a href="{{ $clubUrl }}/login" class="btn">Go to {{ $transfer->toClub->name }}</a>
            @else
                <p>Your request to transfer to <strong>{{ $transfer->toClub->name }}</strong> was
                   <strong>declined</strong> by the club's administrator. Your current membership is unchanged.
                   You can contact the club directly or submit a new request later.</p>
            @endif
        </div>
        <div class="footer">
            <p>Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
