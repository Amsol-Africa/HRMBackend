<?php

namespace App\Mail;

use App\Models\AccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $accessRequest;

    public function __construct(AccessRequest $accessRequest)
    {
        $this->accessRequest = $accessRequest;
    }

    public function build()
    {
        return $this->subject('Invitation to Join Business')
            ->view('emails.invite_user')
            ->with([
                'business' => $this->accessRequest->business,
                'registerUrl' => route('register', ['token' => $this->accessRequest->registration_token]),
            ]);
    }
}
