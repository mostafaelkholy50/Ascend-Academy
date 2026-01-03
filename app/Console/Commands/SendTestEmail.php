<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email every hour to verify email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending test email...');

        try {
            $currentTime = Carbon::now()->format('Y-m-d H:i:s');
            
            Mail::raw("
✅ Email System Test - Working Successfully!

This is an automated test email sent from your Ascend Academy platform.

📅 Sent at: {$currentTime}
🖥️ Server: " . gethostname() . "
🌐 Environment: " . config('app.env') . "

If you're receiving this email, your email configuration is working correctly!

---
This is an automated message from Ascend Academy Email System.
            ", function ($message) use ($currentTime) {
                $message->to('mostafaelkholy4321@gmail.com')
                        ->subject('✅ Hourly Email Test - ' . $currentTime);
            });

            $this->info('✅ Test email sent successfully to mostafaelkholy4321@gmail.com');
            
            // Log success
            \Log::info('Test email sent successfully at ' . $currentTime);
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            
            // Log error
            \Log::error('Failed to send test email: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}