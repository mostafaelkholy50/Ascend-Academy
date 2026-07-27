<?php

namespace App\Services;

use App\Repositories\InquiryRepository;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class InquiryService
{
    protected $repository;

    public function __construct(InquiryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function processInquiry(array $validatedData): string
    {
        $inquiry = $this->repository->createInquiry($validatedData);

        // $this->sendAdminNotification($inquiry); // Temporarily disabled to prevent bot spam suspending email

        return $this->getSuccessMessage($validatedData['type'] ?? 'contact');
    }

    protected function sendAdminNotification(Inquiry $inquiry): void
    {
        try {
            $adminEmail = env('ADMIN_EMAIL', 'ascend.quran@gmail.com');
            Notification::route('mail', $adminEmail)
                ->notify(new \App\Notifications\NewInquiryNotification($inquiry));
        } catch (\Exception $e) {
            Log::error('Failed to send inquiry notification: ' . $e->getMessage());
        }
    }

    public function getIndexData($request)
    {
        return Inquiry::when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(15);
    }

    public function updateStatus(Inquiry $inquiry, array $data): void
    {
        $inquiry->update($data);
    }

    public function deleteInquiry(Inquiry $inquiry): void
    {
        $inquiry->delete();
    }

    public function convertToParent(Inquiry $inquiry): array
    {
        $password = \Illuminate\Support\Str::random(10);
        
        $parent = \App\Models\User::create([
            'name' => $inquiry->full_name,
            'email' => $inquiry->email,
            'phone' => $inquiry->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'Parent',
        ]);

        $parent->assignRole('Parent');
        $inquiry->update(['status' => 'converted']);

        return [
            'parent' => $parent,
            'password' => $password
        ];
    }

    protected function getSuccessMessage(string $type): string
    {
        $messages = [
            'trial' => 'Thank you! Your free trial request has been submitted. We will contact you within 24 hours.',
            'contact' => 'Thank you for your message! We will get back to you within 24 hours.',
            'registration' => 'Thank you for your interest! We will contact you shortly to complete your registration.',
        ];

        return $messages[$type] ?? $messages['contact'];
    }
}
