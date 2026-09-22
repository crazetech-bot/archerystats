<?php

namespace App\Mail;

use App\Models\ClubTransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubTransferRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClubTransferRequest $transfer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transfer Request — ' . ($this->transfer->archer?->user?->name ?? 'Archer'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-transfer-request',
        );
    }
}
