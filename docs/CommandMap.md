# Command Map

This map outlines the verified Artisan commands in the `app/Console/Commands` directory.

## 1. `SendClassReminders` (`class:send-reminders`)
- **Responsibilities**: Cron job that sends reminder emails for today's classes at midnight. Evaluates parent preferences (`class_reminders_enabled`).
- **Notification Types Dispatched**: `TeacherDailyScheduleNotification`, `ClassReminderNotification`.

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 1

## Source References
- `app/Console/Commands/SendClassReminders.php`
