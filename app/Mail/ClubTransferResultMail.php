<?php

namespace App\Mail;

use App\Models\ClubTransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubTransferResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClubTransferRequest $transfer,
        public bool $approved,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Club Transfer ' . ($this->approved ? 'Approved' : 'Declined')
                . ' — ' . $this->transfer->toClub->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-transfer-result',
        );
    }
}
