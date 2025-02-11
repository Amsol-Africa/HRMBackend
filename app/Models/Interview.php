<?php

namespace App\Models;

use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\InterviewCanceledNotification;
use App\Notifications\InterviewDeclinedNotification;
use App\Notifications\InterviewScheduledNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\InterviewRescheduledNotification;

class Interview extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'application_id',
        'interviewer_id',
        'type',
        'location',
        'meeting_link',
        'scheduled_at',
        'status',
        'feedback',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function jobApplication()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function feedback()
    {
        return $this->hasOne(InterviewFeedback::class);
    }

    public function reschedule($newDateTime)
    {
        $this->update(['scheduled_at' => $newDateTime, 'status' => 'scheduled']);

        // Notify applicant & interviewer
        $this->jobApplication->applicant->user->notify(new InterviewRescheduledNotification($this));
        if ($this->interviewer) {
            $this->interviewer->notify(new InterviewRescheduledNotification($this));
        }
    }

    public function cancel($reason)
    {
        $this->update(['status' => 'canceled']);

        // Notify applicant & interviewer
        $this->jobApplication->applicant->user->notify(new InterviewCanceledNotification($this, $reason));
        if ($this->interviewer) {
            $this->interviewer->notify(new InterviewCanceledNotification($this, $reason));
        }
    }
    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
    }

    public function decline($reason)
    {
        $this->update(['status' => 'declined', 'decline_reason' => $reason]);

        // Notify HR about the decline
        $this->jobApplication->business->hrUsers()->each(function ($hr) {
            $hr->notify(new InterviewDeclinedNotification($this));
        });
    }


}
