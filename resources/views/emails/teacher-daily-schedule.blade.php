@component('mail::message')
# Your Daily Class Schedule

Assalamu Alaikum {{ $teacher->name }},

Here is your class schedule for the next 24 hours.

@component('mail::table')
| Time | Course | Student | Duration | Link |
| :--- | :--- | :--- | :--- | :--- |
@foreach($schedules as $schedule)
@php
    $timezone = $teacher?->getUserTimezone() ?? 'Africa/Cairo';
    $startsAt = $schedule->getStartsAtInTimezone($timezone);
    $endsAt = $schedule->getEndsAtInTimezone($timezone);
    $timezoneAbbr = $startsAt->format('T');
    $timeString = $startsAt->format('g:i A') . ' - ' . $endsAt->format('g:i A') . ' ' . $timezoneAbbr;
    $duration = $startsAt->diffInMinutes($endsAt) . ' min';
    $zoomLink = $schedule->zoom_link ? "[Join](" . $schedule->zoom_link . ")" : "-";
@endphp
| {{ $timeString }} | {{ $schedule->course->title }} | {{ $schedule->student->name }} | {{ $duration }} | {{ $zoomLink }} |
@endforeach
@endcomponent

@component('mail::panel')
**Reminder:** Please join the classes 5 minutes early to ensure everything is set up properly.
@endcomponent

JazakAllah Khair,<br>
{{ config('app.name') }}
@endcomponent
