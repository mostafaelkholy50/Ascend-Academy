@component('mail::message')
@php
    $recipient = $user ?? $recipient ?? null;
    $timezone = $recipient?->getUserTimezone() ?? 'Africa/Cairo';
    $startsAt = $schedule->getStartsAtInTimezone($timezone);
    $endsAt = $schedule->getEndsAtInTimezone($timezone);
    $timezoneAbbr = $startsAt->format('T');
@endphp
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
**Start Date:** {{ $startsAt->format('l, F d, Y') }}  
**Time:** {{ $startsAt->format('g:i A') }} - {{ $endsAt->format('g:i A') }} {{ $timezoneAbbr }}  
**Duration:** {{ $startsAt->diffInMinutes($endsAt) }} minutes

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
