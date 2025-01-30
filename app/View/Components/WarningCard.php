<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WarningCard extends Component
{
    public $message;
    public $longText;
    public $icon;
    public $bgColor;
    public $textColor;

    public function __construct($message, $longText, $icon = 'fa-info-circle', $bgColor = 'bg-warning', $textColor = 'text-muted')
    {
        $this->message = $message;
        $this->longText = $longText;
        $this->icon = $icon;
        $this->bgColor = $bgColor;
        $this->textColor = $textColor;
    }
    public function render(): View|Closure|string
    {
        return view('components.warning-card');
    }
}
