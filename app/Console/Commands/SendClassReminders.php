<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Notifications\ClassReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendClassReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'class:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send class reminder emails to students, parents, and teachers for classes in the next 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending class reminders...');

        // Get all scheduled classes in the next 24 hours
        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay();

        $schedules = Schedule::with(['student', 'teacher', 'course', 'student.parents'])
            ->where('status', 'scheduled')
            ->whereBetween('starts_at', [$now, $tomorrow])
            ->get();

        $sentCount = 0;

        foreach ($schedules as $schedule) {
            try {
                // Send to student
                $schedule->student->notify(new ClassReminderNotification($schedule));
                $sentCount++;

                // Send to teacher
                $schedule->teacher->notify(new ClassReminderNotification($schedule));
                $sentCount++;

                // Send to parent(s)
                foreach ($schedule->student->parents as $parent) {
                    $parent->notify(new ClassReminderNotification($schedule));
                    $sentCount++;
                }

                $this->info("Sent reminders for: {$schedule->course->title} - {$schedule->student->name}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for schedule {$schedule->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully sent {$sentCount} class reminder emails for " . $schedules->count() . " classes.");

        return Command::SUCCESS;
    }
}
