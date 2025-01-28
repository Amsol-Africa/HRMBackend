<?php

namespace App\Mail;

use App\Models\AccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteUserMail extends Mailable
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
            subject: 'Invite User Mail',
        );
    }

    public function content(): Content
    {
        $url = route('register.token', ['token' => $this->accessRequest->registration_token]);
        return new Content(
            view: 'emails.invite-user',
            with: ['url' => $url]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
