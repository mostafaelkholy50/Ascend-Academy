@component('mail::message')
# New Learning Resource Available

Assalamu Alaikum {{ $resource->student->name }},

Your teacher has shared a new learning resource with you.

## Resource Details

**Title:** {{ $resource->title }}  
**Teacher:** {{ $resource->teacher->name }}  
**Course:** {{ $resource->course ? $resource->course->title : 'General' }}  
**Type:** {{ ucfirst($resource->type) }}

@if($resource->description)
## Description

{{ $resource->description }}
@endif

@if($resource->file_path)
@component('mail::button', ['url' => route('student.resources.index')])
Download Resource
@endcomponent
@elseif($resource->external_url)
@component('mail::button', ['url' => $resource->external_url])
Access Resource
@endcomponent
@else
@component('mail::button', ['url' => route('student.resources.index')])
View Resource
@endcomponent
@endif

@component('mail::panel')
**Tip:** Make sure to review this resource before your next class!
@endcomponent

Happy learning!

JazakAllah Khair,<br>
{{ config('app.name') }}
@endcomponent
