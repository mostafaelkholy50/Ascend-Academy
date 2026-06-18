# Feature History

*(This document traces the historical additions and evolution of major features.)*

## 1. Consolidated Teacher Notifications (June 2026)
- **Problem**: Teachers were receiving individual reminder emails for every single class, leading to email fatigue.
- **Solution**: The `SendClassReminders` command was refactored to group daily schedules by `teacher_id`. A new `TeacherDailyScheduleNotification` was introduced to send a single daily digest.
- **Affected Files**: `SendClassReminders.php`, `TeacherDailyScheduleNotification.php`, `teacher-daily-schedule.blade.php`.
