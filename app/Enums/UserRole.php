<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'SuperAdmin';
    case Admin = 'Admin';
    case SchedulerManager = 'SchedulerManager';
    case Teacher = 'Teacher';
    case Student = 'Student';
    case Parent = 'Parent';
    case Accountant = 'Accountant';
    case QualityControl = 'QualityControl';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
