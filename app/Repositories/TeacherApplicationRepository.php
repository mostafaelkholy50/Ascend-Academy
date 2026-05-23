<?php

namespace App\Repositories;

use App\Models\TeacherApplication;

class TeacherApplicationRepository
{
    public function createApplication(array $data): TeacherApplication
    {
        return TeacherApplication::create($data);
    }
}
