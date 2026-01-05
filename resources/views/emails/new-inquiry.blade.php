@component('mail::message')
# New Registration Received

**Type:** {{ $typeLabel }}

## Contact Information

**Name:** {{ $inquiry->full_name }}  
**Email:** {{ $inquiry->email }}  
**Phone:** {{ $inquiry->phone ?? 'Not provided' }}

@if($inquiry->join_date)
**Joining Date:** {{ $inquiry->join_date->format('M d, Y') }}  
@endif
**Age:** {{ $inquiry->age }}  
**Gender:** {{ ucfirst($inquiry->gender) }}  
**Location:** {{ $inquiry->city_state }}, {{ $inquiry->country }}

## Course Preferences
**Course Needed:** {{ $inquiry->courses_needed }}  
**Sessions Per Week:** {{ $inquiry->sessions_per_week }}  
**Study Hours:** {{ $inquiry->study_hours }}  
**Available Days:** {{ is_array($inquiry->available_days) ? implode(', ', $inquiry->available_days) : $inquiry->available_days }}

@if($inquiry->referrer)
**Referrer:** {{ $inquiry->referrer }}
@endif

@if($inquiry->message)
## Notes
{{ $inquiry->message }}
@endif

@component('mail::button', ['url' => route('admin.inquiries.index')])
View All Inquiries
@endcomponent

Please respond to this inquiry within 24 hours.

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
