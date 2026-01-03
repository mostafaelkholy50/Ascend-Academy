@component('mail::message')
# New Inquiry Received

**Type:** {{ $typeLabel }}

## Contact Information

**Name:** {{ $inquiry->name }}  
**Email:** {{ $inquiry->email }}  
**Phone:** {{ $inquiry->phone ?? 'Not provided' }}

@if($inquiry->message)
## Message

{{ $inquiry->message }}
@endif

@if($inquiry->preferred_contact_time)
**Preferred Contact Time:** {{ $inquiry->preferred_contact_time }}
@endif

@component('mail::button', ['url' => route('admin.inquiries.index')])
View All Inquiries
@endcomponent

Please respond to this inquiry within 24 hours.

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
