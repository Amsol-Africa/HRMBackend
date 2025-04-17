<?php

namespace App\Mail;

use App\Models\AccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $accessRequest;
    public $tempPassword;

    public function __construct(AccessRequest $accessRequest, $tempPassword = null)
    {
        $this->accessRequest = $accessRequest;
        $this->tempPassword = $tempPassword;
    }

    public function build()
    {
        return $this->subject('Business Access Request')
            ->view('emails.access_request')
            ->with([
                'business' => $this->accessRequest->business,
                'tempPassword' => $this->tempPassword,
                'loginUrl' => route('login'),
            ]);
    }
}
