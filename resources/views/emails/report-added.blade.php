@component('mail::message')
# New Progress Report Available

Assalamu Alaikum {{ $report->student->name }},

Your teacher has added a new progress report for you.

## Report Details

**Teacher:** {{ $report->teacher->name }}  
**Course:** {{ $report->course ? $report->course->title : 'General' }}  
**Report Date:** {{ $report->report_date->format('F d, Y') }}

@if($report->level)
**Current Level:** {{ $report->level }}
@endif

@if($report->mastery_score)
**Mastery Score:** {{ $report->mastery_score }}%
@endif

@if($report->strengths)
## Strengths

{{ $report->strengths }}
@endif

@if($report->weaknesses)
## Areas for Improvement

{{ $report->weaknesses }}
@endif

@if($report->behavior)
## Behavior & Participation

{{ $report->behavior }}
@endif

@if($report->notes)
## Teacher's Notes

{{ $report->notes }}
@endif

@component('mail::button', ['url' => route('student.reports.index')])
View Full Report
@endcomponent

Keep up the great work!

JazakAllah Khair,<br>
{{ config('app.name') }}
@endcomponent
