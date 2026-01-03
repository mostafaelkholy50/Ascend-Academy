@component('mail::message')
# Class Reminder

Assalamu Alaikum {{ $schedule->student->name }},

This is a friendly reminder about your upcoming class.

## Class Details

**Course:** {{ $schedule->course->title }}  
**Teacher:** {{ $schedule->teacher->name }}  
**Date:** {{ $schedule->starts_at->format('l, F d, Y') }}  
**Time:** {{ $schedule->starts_at->format('g:i A') }} - {{ $schedule->ends_at->format('g:i A') }}  
**Duration:** {{ $schedule->starts_at->diffInMinutes($schedule->ends_at) }} minutes

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
