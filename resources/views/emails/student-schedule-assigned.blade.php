@component('mail::message')
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
