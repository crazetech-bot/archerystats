<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Membership Invitation</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #312e81 0%, #4338ca 100%); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: 0.02em; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin: 0; }
        .body { padding: 36px 40px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .club-name { font-weight: 700; color: #312e81; }
        .buttons { display: flex; gap: 12px; margin: 28px 0; }
        .btn-accept { flex: 1; background: linear-gradient(135deg, #059669, #10b981); color: #fff; text-align: center;
                      padding: 14px 20px; border-radius: 10px; font-weight: 700; font-size: 15px;
                      text-decoration: none; display: block; }
        .btn-decline { flex: 1; background: #f1f5f9; color: #64748b; text-align: center;
                       padding: 14px 20px; border-radius: 10px; font-weight: 600; font-size: 15px;
                       text-decoration: none; display: block; border: 1px solid #e2e8f0; }
        .notice { background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px;
                  padding: 14px 18px; margin-bottom: 16px; }
        .notice p { color: #92400e; font-size: 13px; margin: 0; }
        .expiry { color: #94a3b8; font-size: 12px; text-align: center; margin-top: 20px; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Club Membership Invitation</h1>
            <p>Archery Stats Management System</p>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $inviteeName }}</strong>,</p>
            <p>
                You have been invited to join
                <span class="club-name">{{ $invitation->club->name }}</span>
                as a member on the Archery Stats platform.
            </p>

            @if($invitation->club->location)
            <p style="color:#64748b; font-size:13px;">
                📍 {{ $invitation->club->location }}
                @if($invitation->club->state) · {{ $invitation->club->state }} @endif
            </p>
            @endif

            @php
                $invitable = $invitation->invitable_model;
                $currentClub = $invitable?->club;
            @endphp

            @if($currentClub && $currentClub->id !== $invitation->club_id)
            <div class="notice">
                <p>⚠️ <strong>Note:</strong> You are currently a member of <strong>{{ $currentClub->name }}</strong>. Accepting this invitation will transfer your membership to {{ $invitation->club->name }}.</p>
            </div>
            @endif

            <p>Please click one of the buttons below to respond to this invitation:</p>

            <div class="buttons">
                <a href="{{ $acceptUrl }}" class="btn-accept">✓ Accept Invitation</a>
                <a href="{{ $declineUrl }}" class="btn-decline">✕ Decline</a>
            </div>

            <p class="expiry">This invitation expires on {{ $invitation->expires_at->format('d M Y, g:i A') }}.</p>

            <p style="font-size:13px; color:#94a3b8; margin-top:24px;">
                If you did not expect this invitation or believe it was sent in error, you can safely ignore this email. Your membership will not change unless you click "Accept".
            </p>
        </div>
        <div class="footer">
            <p>Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
