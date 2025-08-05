<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class P9Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeePayroll;
    public $pdfPath;
    public $year;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\EmployeePayroll $employeePayroll
     * @param string $pdfPath
     * @param int $year
     * @param \App\Models\User $user
     */
    public function __construct($employeePayroll, $pdfPath, $year, $user)
    {
        $this->employeePayroll = $employeePayroll;
        $this->pdfPath = $pdfPath;
        $this->year = $year;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your P9 Form for ' . $this->year)
                    ->view('emails.p9')
                    ->attach($this->pdfPath, [
                        'as' => 'P9_' . $this->employeePayroll->id . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
