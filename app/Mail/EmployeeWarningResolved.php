<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Warning;

class EmployeeWarningResolved extends Mailable
{
    use Queueable, SerializesModels;

    public $warning;

    public function __construct(Warning $warning)
    {
        $this->warning = $warning;
    }

    public function build()
    {
        return $this->subject('Warning Resolved - ' . config('app.name'))
            ->view('emails.warning_resolved');
    }
}