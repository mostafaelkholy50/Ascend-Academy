<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-test {email : The recipient email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipient = $this->argument('email');
        $currentTime = Carbon::now()->format('Y-m-d H:i:s');

        $this->info('Sending test email...');

        try {
            Mail::raw(
                "Email System Test - Working Successfully!\n\n"
                . "This is an automated test email sent from your Ascend Academy platform.\n\n"
                . "Sent at: {$currentTime}\n"
                . "Server: " . gethostname() . "\n"
                . "Environment: " . config('app.env') . "\n\n"
                . "If you're receiving this email, your email configuration is working correctly!\n\n"
                . "---\n"
                . "This is an automated message from Ascend Academy Email System.\n",
                function ($message) use ($currentTime, $recipient) {
                    $message->to($recipient)
                        ->subject('Email Test - ' . $currentTime);
                }
            );

            $this->info("Test email sent successfully to {$recipient}");
            \Log::info('Test email sent successfully at ' . $currentTime . ' to ' . $recipient);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
            \Log::error('Failed to send test email: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
