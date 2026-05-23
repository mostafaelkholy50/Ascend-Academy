<?php

namespace App\Repositories;

use App\Models\Inquiry;

class InquiryRepository
{
    public function createInquiry(array $data): Inquiry
    {
        return Inquiry::create($data);
    }
}
