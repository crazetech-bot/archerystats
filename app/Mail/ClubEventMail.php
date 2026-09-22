<?php

namespace App\Mail;

use App\Models\ClubEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClubEvent $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->event->club->name . '] Event: ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-event',
        );
    }
}
