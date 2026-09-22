<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Event</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%); padding: 32px 40px; text-align: center; }
        .header p.k { color: rgba(255,255,255,0.8); font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin: 0 0 6px; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0; }
        .body { padding: 32px 40px; }
        .when { background: #eef2ff; border-radius: 12px; padding: 18px 20px; margin: 0 0 20px; }
        .when .row { display: flex; gap: 10px; font-size: 14px; color: #3730a3; margin: 0 0 6px; }
        .when .row:last-child { margin: 0; }
        .when .row b { color: #1e1b4b; }
        .desc { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 8px; }
        .btns { text-align: center; margin: 26px 0 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #4338ca, #6366f1); color: #fff;
               padding: 13px 28px; border-radius: 10px; font-weight: 700; font-size: 15px; text-decoration: none; }
        .footer { background: #f8fafc; padding: 18px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
@php($clubUrl = 'http://' . $event->club->slug . '.' . config('app.root_domain', 'sportdns.com'))
<div class="wrapper">
    <div class="card">
        <div class="header">
            <p class="k">{{ $event->club->name }} · {{ $event->typeLabel() }}</p>
            <h1>{{ $event->title }}</h1>
        </div>
        <div class="body">
            <div class="when">
                <div class="row">🗓 <span><b>When:</b>
                    {{ $event->starts_at->format('D, d M Y · g:i A') }}@if($event->ends_at) – {{ $event->ends_at->format('g:i A') }}@endif
                </span></div>
                @if($event->location)
                <div class="row">📍 <span><b>Where:</b> {{ $event->location }}</span></div>
                @endif
            </div>
            @if($event->description)
                <p class="desc">{!! nl2br(e($event->description)) !!}</p>
            @endif
            <div class="btns">
                <a href="{{ $clubUrl }}/events/{{ $event->id }}" class="btn">View &amp; RSVP</a>
            </div>
        </div>
        <div class="footer">
            <p>{{ $event->club->name }} on Archery Stats &mdash; sportdns.com</p>
        </div>
    </div>
</div>
</body>
</html>
