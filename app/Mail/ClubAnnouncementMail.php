<?php

namespace App\Mail;

use App\Models\ClubAnnouncement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClubAnnouncement $announcement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->announcement->club->name . '] ' . $this->announcement->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-announcement',
        );
    }
}
