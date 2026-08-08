<?php

namespace App\Enums;

enum RescheduleRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
