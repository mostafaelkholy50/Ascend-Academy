<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'allowed_countries',
        'can_access_payroll',
    ];

    protected $casts = [
        'allowed_countries' => 'array',
        'can_access_payroll' => 'boolean',
    ];
}
