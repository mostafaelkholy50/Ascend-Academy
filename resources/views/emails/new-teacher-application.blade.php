@component('mail::message')
# New Teacher Application

A new teacher has submitted an application to join Ascend Academy.

## Applicant Information

**Name:** {{ $application->name }}  
**Email:** {{ $application->email }}  
**Phone:** {{ $application->phone }}

## Qualifications

**Education:** {{ $application->education }}  
**Experience:** {{ $application->experience }} years  
**Specialization:** {{ $application->specialization }}

@if($application->certifications)
**Certifications:** {{ $application->certifications }}
@endif

@if($application->languages)
**Languages:** {{ $application->languages }}
@endif

## Availability

{{ $application->availability }}

@if($application->message)
## Additional Message

{{ $application->message }}
@endif

@if($application->cv_path)
**CV Attached:** Yes (Available in admin panel)
@endif

@component('mail::button', ['url' => route('admin.teacher-applications.index')])
View Application
@endcomponent

Please review and respond within 3-5 business days.

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
