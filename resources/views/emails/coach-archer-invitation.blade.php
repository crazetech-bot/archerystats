<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Assignment Request</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 30px 0; }
        .wrap { max-width: 560px; margin: 0 auto; }
        .card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #0f172a; padding: 28px 32px; text-align: center; }
        .header h1 { color: #f59e0b; font-size: 20px; margin: 0; letter-spacing: 1px; }
        .header p { color: #94a3b8; font-size: 13px; margin: 6px 0 0; }
        .body { padding: 32px; }
        .body p { color: #334155; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .info-box p { margin: 4px 0; font-size: 14px; color: #475569; }
        .info-box strong { color: #0f172a; }
        .btn-row { text-align: center; margin: 28px 0 12px; display: flex; gap: 12px; justify-content: center; }
        .btn { display: inline-block; padding: 13px 28px; border-radius: 8px; font-size: 14px; font-weight: bold; text-decoration: none; letter-spacing: .5px; }
        .btn-accept  { background: #0d9488; color: #fff; }
        .btn-decline { background: #fff; color: #dc2626; border: 2px solid #dc2626; }
        .expiry { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 8px; }
        .footer { background: #f8fafc; padding: 18px 32px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="header">
            <h1>ARCHERY STATS</h1>
            <p>Coach Assignment Request</p>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $invitation->archer->user->name }}</strong>,</p>
            <p>You have received a coach assignment request. The coach below would like to add you to their roster.</p>

            <div class="info-box">
                <p><strong>Coach:</strong> {{ $invitation->coach->full_name }} ({{ $invitation->coach->ref_no }})</p>
                <p><strong>Club:</strong> {{ $invitation->coach->club?->name ?? '—' }}</p>
                <p><strong>Coaching Level:</strong> {{ $invitation->coach->coaching_level ?? '—' }}</p>
            </div>

            <p>Please respond within <strong>72 hours</strong>. The invitation expires on <strong>{{ $invitation->expires_at->format('d M Y, h:i A') }}</strong>.</p>

            <div class="btn-row">
                <a href="{{ route('coach-archer-invitations.accept', $invitation->token) }}" class="btn btn-accept">✔ Accept</a>
                <a href="{{ route('coach-archer-invitations.decline', $invitation->token) }}" class="btn btn-decline">✘ Decline</a>
            </div>
            <p class="expiry">This link expires in 72 hours and can only be used once.</p>
        </div>
        <div class="footer">
            <p>If you did not expect this email, you can safely ignore it.</p>
            <p style="margin-top:4px;">Archery Stats &mdash; {{ config('app.name') }}</p>
        </div>
    </div>
</div>
</body>
</html>
