<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use App\Http\Requests\InquiryRequest\StoreInquiryRequest;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request)
    {
        $validated = $request->validated();

        $inquiry = Inquiry::create($validated);

        // Send email notification to admin
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@ascendacademy.com');
            \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                ->notify(new \App\Notifications\NewInquiryNotification($inquiry));
        } catch (\Exception $e) {
            \Log::error('Failed to send inquiry notification: ' . $e->getMessage());
        }

        // Return different messages based on type
        $messages = [
            'trial' => 'Thank you! Your free trial request has been submitted. We will contact you within 24 hours.',
            'contact' => 'Thank you for your message! We will get back to you within 24 hours.',
            'registration' => 'Thank you for your interest! We will contact you shortly to complete your registration.',
        ];

        $message = $messages[$validated['type']] ?? $messages['contact'];

        return back()->with('success', $message);
    }

    /**
     * Show the get started / registration page
     */
    public function getStarted()
    {
        return view('pages.get-started');
    }
}
