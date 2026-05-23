@component('mail::message')
# New Monthly Evaluation Available

Assalamu Alaikum,

A new monthly evaluation has been added for your child, **{{ $evaluation->student->name }}**.

## Evaluation Details

**Teacher:** {{ $evaluation->teacher->name }}  
**Date:** {{ $evaluation->evaluation_date->format('F Y') }}  
**Total Score:** {{ $evaluation->total_score }} / 100

### Scores Breakdown

* **Attendance & Punctuality:** {{ $evaluation->q1_score }} / 10
* **Participation & Engagement:** {{ $evaluation->q2_score }} / 10
* **Homework Completion:** {{ $evaluation->q3_score }} / 10
* **Understanding & Comprehension:** {{ $evaluation->q4_score }} / 10
* **Behavior & Discipline:** {{ $evaluation->q5_score }} / 10
* **Focus & Attention:** {{ $evaluation->q6_score }} / 10
* **Interaction with Teacher:** {{ $evaluation->q7_score }} / 10
* **Progress & Improvement:** {{ $evaluation->q8_score }} / 10
* **Effort & Motivation:** {{ $evaluation->q9_score }} / 10
* **Retention of Previous Lessons:** {{ $evaluation->q10_score }} / 10

@if($evaluation->notes)
## Teacher's Notes

{{ $evaluation->notes }}
@endif

@component('mail::button', ['url' => route('parent.children.show', $evaluation->student_id)])
View Full Details
@endcomponent

JazakAllah Khair,<br>
{{ config('app.name') }}
@endcomponent
