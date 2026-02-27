<?php

namespace App\Mail;

use App\Models\CoachArcherInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoachArcherInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CoachArcherInvitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Coach Assignment Request — ' . $this->invitation->coach->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coach-archer-invitation',
        );
    }
}
