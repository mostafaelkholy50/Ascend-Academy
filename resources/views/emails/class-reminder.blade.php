@component('mail::message')
@php
    $recipient = $user ?? $recipient ?? null;
    $timezone = $recipient?->getUserTimezone() ?? 'Africa/Cairo';
    $startsAt = $schedule->getStartsAtInTimezone($timezone);
    $endsAt = $schedule->getEndsAtInTimezone($timezone);
    $timezoneAbbr = $startsAt->format('T');
@endphp
# Class Reminder

Assalamu Alaikum {{ $schedule->student->name }},

This is a friendly reminder about your upcoming class.

## Class Details

**Course:** {{ $schedule->course->title }}  
**Teacher:** {{ $schedule->teacher->name }}  
**Date:** {{ $startsAt->format('l, F d, Y') }}  
**Time:** {{ $startsAt->format('g:i A') }} - {{ $endsAt->format('g:i A') }} {{ $timezoneAbbr }}  
**Duration:** {{ $startsAt->diffInMinutes($endsAt) }} minutes

@if($schedule->zoom_link)
@component('mail::button', ['url' => $schedule->zoom_link])
Join Zoom Class
@endcomponent
@endif

@component('mail::panel')
**Reminder:** Please join the class 5 minutes early to ensure everything is set up properly.
@endcomponent

@if($schedule->notes)
## Class Notes

{{ $schedule->notes }}
@endif

We look forward to seeing you in class!

JazakAllah Khair,<br>
{{ config('app.name') }}
@endcomponent
