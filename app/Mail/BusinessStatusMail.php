<?php

namespace App\Mail;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BusinessStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $business;
    public $status;
    public $remarks;

    public function __construct(Business $business, $status, $remarks)
    {
        $this->business = $business;
        $this->status = $status;
        $this->remarks = $remarks;
    }

    public function build()
    {
        $subject = $this->status === 'verified' ? 'Business Verified' : 'Business Deactivated';
        return $this->subject($subject)
            ->view('emails.business_status')
            ->with([
                'business' => $this->business,
                'status' => $this->status,
                'remarks' => $this->remarks,
                'loginUrl' => route('login'),
            ]);
    }
}
