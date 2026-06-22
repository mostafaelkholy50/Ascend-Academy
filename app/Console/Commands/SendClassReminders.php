<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Notifications\ClassReminderNotification;
use App\Notifications\TeacherDailyScheduleNotification;
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
    protected $description = 'Send class reminder emails to students, parents, and teachers for today\'s classes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending class reminders...');

        // Get all scheduled classes for the current day window
        $now = Carbon::now();
        $dayStart = $now->copy()->startOfDay();
        $dayEnd = $now->copy()->endOfDay();

        $schedules = Schedule::with(['student', 'teacher', 'course', 'student.parents'])
            ->where('status', 'scheduled')
            ->whereBetween('starts_at', [$dayStart, $dayEnd])
            ->get();

        $sentCount = 0;
        $teacherDigestCount = 0;
        $classReminderCount = 0;

        // Group schedules by teacher ID to send one daily email to each teacher
        $schedulesByTeacher = $schedules->groupBy('teacher_id');

        foreach ($schedulesByTeacher as $teacherId => $teacherSchedules) {
            try {
                $teacher = $teacherSchedules->first()->teacher;
                $teacher->notify(new TeacherDailyScheduleNotification($teacherSchedules));
                $sentCount++;
                $teacherDigestCount++;
                $this->info("Sent daily schedule digest to teacher: {$teacher->name}");
            } catch (\Exception $e) {
                $this->error("Failed to send daily schedule digest to teacher {$teacherId}: " . $e->getMessage());
            }
        }

        foreach ($schedules as $schedule) {
            try {
                $parents = $schedule->student->parents;
                
                // Check if any parent has disabled daily class reminders
                $anyParentDisabled = $parents->contains(fn($parent) => !$parent->class_reminders_enabled);

                // Send to student only if class reminders are enabled for all parents (or there are no parents)
                if (!$anyParentDisabled) {
                    $schedule->student->notify(new ClassReminderNotification($schedule));
                    $sentCount++;
                    $classReminderCount++;
                }

                // Send to parent(s) who have class reminders enabled
                foreach ($parents as $parent) {
                    if ($parent->class_reminders_enabled) {
                        $parent->notify(new ClassReminderNotification($schedule));
                        $sentCount++;
                        $classReminderCount++;
                    }
                }

                $this->info("Sent student/parent reminders for: {$schedule->course->title} - {$schedule->student->name}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for schedule {$schedule->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully sent {$sentCount} reminder emails for " . $schedules->count() . " classes.");
        $this->info("Teacher digests: {$teacherDigestCount}, class reminders: {$classReminderCount}");

        return Command::SUCCESS;
    }
}
