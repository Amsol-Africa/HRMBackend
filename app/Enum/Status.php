<?php

namespace App\Enum;

enum Status
{
    const ACTIVE = 'active';
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
    const APPROVED = 'approved';
    const DECLINED = 'declined';
    const UNREAD = 'unread';
}
