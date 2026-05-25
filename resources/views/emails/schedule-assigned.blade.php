@component('mail::message')
# @if($isMultiple) New Schedules Assigned @else New Schedule Assigned @endif

Assalamu Alaikum {{ $schedule->teacher->name }},

@if($isMultiple)
You have been assigned to teach **{{ $count }} new classes** starting soon.
@else
You have been assigned a new class schedule.
@endif

## Class Details

**Course:** {{ $schedule->course->title }}  
**Student:** {{ $schedule->student->name }}  
**Start Date:** {{ $schedule->starts_at->format('l, F d, Y') }}  
**Time:** {{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}  
**Duration:** {{ $schedule->starts_at->diffInMinutes($schedule->ends_at) }} minutes

@if($schedule->zoom_link)
**Zoom Link:** {{ $schedule->zoom_link }}
@endif

@if($schedule->notes)
## Additional Notes

{{ $schedule->notes }}
@endif

@component('mail::button', ['url' => route('teacher.schedule.index')])
View My Schedule
@endcomponent

Please prepare your materials and ensure you're ready for the class.

JazakAllah Khair,<br>
{{ config('app.name') }} Admin Team
@endcomponent
