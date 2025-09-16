<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $recipientRole;

    public function __construct(LeaveRequest $leaveRequest, $recipientRole = 'general')
    {
        $this->leaveRequest = $leaveRequest;
        $this->recipientRole = $recipientRole;
    }

    public function build()
    {
        $subject = $this->getEmailSubject();
        
        return $this->subject($subject)
                    ->view('emails.leave.request_submitted')
                    ->with([
                        'leave' => $this->leaveRequest,
                        'role' => $this->recipientRole,
                        'employee' => $this->leaveRequest->employee,
                        'business' => $this->leaveRequest->business,
                        'leaveType' => $this->leaveRequest->leaveType,
                        'actionUrl' => $this->getActionUrl(),
                        'currentLevel' => $this->leaveRequest->current_approval_level ?? 0,
                        'requiredLevels' => optional($this->leaveRequest->leaveType)->approval_levels ?? 1,
                    ]);
    }

    protected function getEmailSubject()
    {
        $employeeName = optional(optional($this->leaveRequest->employee)->user)->name ?? 'Employee';
        $status = $this->leaveRequest->status;

        switch ($this->recipientRole) {
            case 'employee':
                switch ($status) {
                    case 'approved':
                        return "Leave Request Approved - {$this->leaveRequest->reference_number}";
                    case 'rejected':
                        return "Leave Request Rejected - {$this->leaveRequest->reference_number}";
                    default:
                        return "Leave Request Submitted - {$this->leaveRequest->reference_number}";
                }
            case 'hod':
                return "Leave Approval Required - {$employeeName} ({$this->leaveRequest->reference_number})";
            case 'hr':
                $currentLevel = $this->leaveRequest->current_approval_level ?? 0;
                if ($currentLevel > 0) {
                    return "Final Approval Required - {$employeeName} ({$this->leaveRequest->reference_number})";
                }
                return "Leave Request for Review - {$employeeName} ({$this->leaveRequest->reference_number})";
            case 'admin':
            case 'head':
                return "Leave Request Approved - {$employeeName} ({$this->leaveRequest->reference_number})";
            default:
                return "Leave Request Notification - {$this->leaveRequest->reference_number}";
        }
    }

    protected function getActionUrl()
    {
        $businessSlug = $this->leaveRequest->business->slug ?? 'default';
        $reference = $this->leaveRequest->reference_number;

        switch ($this->recipientRole) {
            case 'employee':
                return route('myaccount.leave.show', ['business' => $businessSlug, 'leave' => $reference]);
            case 'hod':
            case 'hr':
            case 'admin':
            case 'head':
                return route('business.leave.show', ['business' => $businessSlug, 'leave' => $reference]);
            default:
                return url('/');
        }
    }
}