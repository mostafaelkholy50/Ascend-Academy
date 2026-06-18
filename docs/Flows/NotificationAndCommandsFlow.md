# Notification & Commands Flow

## Why it exists
To handle asynchronous communication, reminders, and daily digests without blocking the main web requests.

## What it does (SendClassReminders Command)
Managed by the Artisan command `class:send-reminders`:
1. Fetches all scheduled classes in the next 24 hours.
2. **Teacher Digest**: Groups schedules by `teacher_id` and sends a *single* `TeacherDailyScheduleNotification` (Daily Digest) rather than spamming them per class.
3. **Student/Parent Reminders**: Checks the `class_reminders_enabled` flag on parents. If enabled, it sends individual `ClassReminderNotification`s.

## Dependencies & Triggers
- **Who calls it**: The Laravel Scheduler (Cron) runs `php artisan class:send-reminders` daily.
- **Dependencies**: Relies on Laravel Queues. The `TeacherDailyScheduleNotification` implements `ShouldQueue` to prevent timeout failures.
- **Related Database Tables**: `schedules`, `users` (Teachers, Students, Parents), `notifications` (Database channel).
- **Related UI**: The Teacher Dashboard notification bell (`notifications.index` route) and the markdown email template `resources/views/emails/teacher-daily-schedule.blade.php`.

## Business Constraints
- Teachers must not receive individual emails for every single class; they must receive a consolidated digest.
- If a parent disables `class_reminders_enabled`, neither the parent nor the student receives the reminder.

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 2

## Source References
- `app/Console/Commands/SendClassReminders.php`
- `app/Notifications/TeacherDailyScheduleNotification.php`
