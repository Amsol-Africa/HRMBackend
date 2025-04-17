<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject('New Task Assignment: ' . $this->task->title)
            ->markdown('emails.task_assigned')
            ->with([
                'task' => $this->task,
                'business' => $this->task->business,
            ]);
    }
}
