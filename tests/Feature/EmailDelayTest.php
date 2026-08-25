<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Mime\Email;

function makeMessageSendingEvent(string $to, string $subject, string $body): MessageSending
{
    $email = (new Email)->to($to)->subject($subject)->text($body);

    return new MessageSending($email, []);
}

it('allows a single email without an artificial delay', function () {
    $start = microtime(true);

    $halt = Event::until(makeMessageSendingEvent('single@example.com', 'Testing Single Email', 'body'));

    expect($halt)->toBeNull()
        ->and(microtime(true) - $start)->toBeLessThan(2);
});

it('suppresses identical duplicate emails within the dedup window', function () {
    // First identical message is allowed.
    expect(Event::until(makeMessageSendingEvent('dedup@example.com', 'Same Subject', 'Duplicate protection test body.')))
        ->toBeNull();

    // Second identical message within the window must be halted (cancelled).
    expect(Event::until(makeMessageSendingEvent('dedup@example.com', 'Same Subject', 'Duplicate protection test body.')))
        ->toBeFalse();
});

it('allows different emails to the same recipient', function () {
    expect(Event::until(makeMessageSendingEvent('multi@example.com', 'First Subject', 'First body.')))
        ->toBeNull()
        ->and(Event::until(makeMessageSendingEvent('multi@example.com', 'Second Subject', 'Second body.')))
        ->toBeNull();
});
