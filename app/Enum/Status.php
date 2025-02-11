<?php

namespace App\Enum;

enum Status
{
    const ACTIVE = 'active';
    const PENDING = 'pending';
    const COMPLETED = 'completed'; //inteview
    const CANCELLED = 'cancelled';
    const APPROVED = 'approved';
    const DECLINED = 'declined';
    const SETUP = 'setup';
    const MODULE = 'module';
    const OPEN = 'open';
    const CLOSED = 'closed';
    const DRAFT = 'draft';
    const APPLIED = "applied";
    const SCREENED = "screened";
    const INTERVIEWED = "interviewed";
    const OFFERED = "offered";
    const HIRED = "hired";
    const REJECTED = "rejected";
    const SCHEDULED = "scheduled";
    const CANCELED = "canceled";

}
