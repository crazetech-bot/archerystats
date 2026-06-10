<?php

namespace App\Mail;

use App\Models\Club;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Club $club,
        public User $admin,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Club is Live — ' . $this->club->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-activated',
        );
    }
}
