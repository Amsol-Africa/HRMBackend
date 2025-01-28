<?php

namespace App\Mail;

use App\Models\AccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccessRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $accessRequest;

    public function __construct(AccessRequest $accessRequest)
    {
        $this->accessRequest = $accessRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Access Request Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.access-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
