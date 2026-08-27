<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Notifications\ClassReminderNotification;
use App\Notifications\TeacherDailyScheduleNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
    protected $description = 'Send class reminder emails to students, parents, and teachers for today\'s classes';

    /**
     * Keep the command lightweight for shared hosting mail limits.
     */
    private const MAX_SCHEDULES_PER_RUN = 20;
    private const MAX_EMAILS_PER_RUN = 40;
    private const LOCK_TTL_MINUTES = 55;

    /**
     * Spread queued emails apart so the SMTP server never sees a burst.
     * 300s = 5 minutes between messages; 40 emails ≈ 3.3 hours.
     */
    private const EMAIL_STAGGER_SECONDS = 300;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending class reminders...');

        $lockKey = 'cron_lock:class_send_reminders';
        if (!Cache::add($lockKey, true, now()->addMinutes(self::LOCK_TTL_MINUTES))) {
            $this->warn('Skipping: another class reminder run is still active.');
            return Command::SUCCESS;
        }

        try {
            // Get all scheduled classes for the current day window
            $now = Carbon::now();
            $dayStart = $now->copy()->startOfDay();
            $dayEnd = $now->copy()->endOfDay();

            $schedules = Schedule::with(['student', 'teacher', 'course', 'student.parents'])
                ->where('status', 'scheduled')
                ->whereBetween('starts_at', [$dayStart, $dayEnd])
                ->orderBy('starts_at')
                ->limit(self::MAX_SCHEDULES_PER_RUN)
                ->get();

            $sentCount = 0;
            $teacherDigestCount = 0;
            $classReminderCount = 0;
            $skippedCount = 0;
            $emailIndex = 0;

            // Group schedules by teacher ID to send one daily email to each teacher
            $schedulesByTeacher = $schedules->groupBy('teacher_id');

            foreach ($schedulesByTeacher as $teacherId => $teacherSchedules) {
                if ($sentCount >= self::MAX_EMAILS_PER_RUN) {
                    $skippedCount += $teacherSchedules->count();
                    break;
                }

                $cacheKey = 'teacher_digest_sent_' . $teacherId . '_' . $dayStart->format('Y-m-d');
                if (!Cache::add($cacheKey, true, now()->addDays(2))) {
                    continue;
                }

                try {
                    $teacher = $teacherSchedules->first()->teacher;
                    $teacher->notify(
                        (new TeacherDailyScheduleNotification($teacherSchedules))
                            ->delay(now()->addSeconds(self::EMAIL_STAGGER_SECONDS * $emailIndex++))
                    );
                    $sentCount++;
                    $teacherDigestCount++;
                    $this->info("Sent daily schedule digest to teacher: {$teacher->name}");
                } catch (\Exception $e) {
                    Cache::forget($cacheKey);
                    $this->error("Failed to send daily schedule digest to teacher {$teacherId}: " . $e->getMessage());
                }
            }

            foreach ($schedules as $schedule) {
                if ($sentCount >= self::MAX_EMAILS_PER_RUN) {
                    $skippedCount++;
                    continue;
                }

                $cacheKey = 'class_reminder_sent_' . $schedule->id . '_' . $dayStart->format('Y-m-d');
                if (!Cache::add($cacheKey, true, now()->addDays(2))) {
                    continue;
                }

                try {
                    $parents = $schedule->student->parents;
                    $remainingEmails = self::MAX_EMAILS_PER_RUN - $sentCount;

                    // Send to student only if class reminders are enabled for all parents (or there are no parents)
                    $anyParentDisabled = $parents->contains(fn($parent) => !$parent->class_reminders_enabled);

                    if (!$anyParentDisabled && $remainingEmails > 0) {
                        $schedule->student->notify(
                            (new ClassReminderNotification($schedule))
                                ->delay(now()->addSeconds(self::EMAIL_STAGGER_SECONDS * $emailIndex++))
                        );
                        $sentCount++;
                        $classReminderCount++;
                        $remainingEmails--;
                    }

                    // Send to parent(s) who have class reminders enabled
                    foreach ($parents as $parent) {
                        if ($remainingEmails <= 0) {
                            $skippedCount++;
                            break;
                        }

                        if ($parent->class_reminders_enabled) {
                            $parent->notify(
                                (new ClassReminderNotification($schedule))
                                    ->delay(now()->addSeconds(self::EMAIL_STAGGER_SECONDS * $emailIndex++))
                            );
                            $sentCount++;
                            $classReminderCount++;
                            $remainingEmails--;
                        }
                    }

                    $this->info("Sent student/parent reminders for: {$schedule->course->title} - {$schedule->student->name}");
                } catch (\Exception $e) {
                    Cache::forget($cacheKey);
                    $this->error("Failed to send reminder for schedule {$schedule->id}: " . $e->getMessage());
                }
            }

            $this->info("Successfully sent {$sentCount} reminder emails for " . $schedules->count() . " classes.");
            $this->info("Teacher digests: {$teacherDigestCount}, class reminders: {$classReminderCount}");

            if ($skippedCount > 0) {
                $this->warn("Skipped {$skippedCount} reminders to stay within the run limit.");
            }

            return Command::SUCCESS;
        } finally {
            Cache::forget($lockKey);
        }
    }
}
