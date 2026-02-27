<?php

namespace App\Mail;

use App\Models\ClubInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClubInvitation $invitation,
        public string $inviteeName,
        public string $acceptUrl,
        public string $declineUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Club Membership Invitation — ' . $this->invitation->club->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-invitation',
        );
    }
}
