@component('mail::message')
@php
    $recipient = $user ?? $recipient ?? null;
    $timezone = $recipient?->getUserTimezone() ?? 'Africa/Cairo';
    $startsAt = $schedule->getStartsAtInTimezone($timezone);
    $endsAt = $schedule->getEndsAtInTimezone($timezone);
    $timezoneAbbr = $startsAt->format('T');
@endphp
# @if($isMultiple) New Class Schedules Assigned @else New Class Schedule Assigned @endif

Assalamu Alaikum {{ $user->name }},

@if($user->role === 'Parent')
A new class schedule has been assigned for your child, **{{ $schedule->student->name }}**.
@else
A new class schedule has been assigned for you.
@endif

## Class Details

**Course:** {{ $schedule->course->title }}  
**Teacher:** {{ $schedule->teacher->name }}  
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

@if($schedule->zoom_link)
@component('mail::button', ['url' => $schedule->zoom_link])
Join Zoom Class
@endcomponent
@else
@component('mail::button', ['url' => $user->role === 'Parent' ? route('parent.schedule.weekly') : route('student.schedule.weekly')])
View My Schedule
@endcomponent
@endif

JazakAllah Khair,<br>
{{ config('app.name') }} Team
@endcomponent
