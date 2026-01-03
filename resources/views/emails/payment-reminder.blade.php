@component('mail::message')
Assalamu Alaikum Warahmatullah

@if($payment->currency === 'CAD')
🌺 Your fee for **{{ $payment->getMonthName() }}** is: **CAD {{ number_format($payment->amount, 2) }}**

Please send your fee to this Canadian bank account:

@component('mail::panel')
**E-transfer:** ranidixit105@yahoo.com  
**Name:** Naseem Kana
@endcomponent

@elseif($payment->currency === 'GBP')
🌺 Your fee for **{{ $payment->getMonthName() }}** is: **£{{ number_format($payment->amount, 2) }}**

Please send your fee to this UK bank account:

@component('mail::panel')
**Bank Account:** 13995909  
**Sort Code:** 20-05-74  
**Name:** Amjad Butt
@endcomponent

@else
🌺 Your fee for **{{ $payment->getMonthName() }}** is: **{{ $payment->getFormattedAmount() }}**

Please contact the admin for payment instructions.
@endif

## Enrollment Details

**Student:** {{ $payment->enrollment->student->name }}  
**Course:** {{ $payment->enrollment->course->title }}  
**Amount Due:** {{ $payment->getFormattedAmount() }}  
**Due Date:** {{ $payment->month->format('F Y') }}

@component('mail::button', ['url' => route('student.dashboard')])
View Dashboard
@endcomponent

@component('mail::panel')
**Important:** Please ensure your payment is sent before the end of the month to avoid any interruption in classes.
@endcomponent

Jazzakum Allah Khairan

Best regards,<br>
{{ config('app.name') }}
@endcomponent
