# Notifications

- Notifications are handled via Laravel's Notification system.
- **Location**: `app/Notifications`.
- Supported channels typically include `mail` and `database`.
- Examples: `TeacherDailyScheduleNotification`, `SendClassReminders`.
- Ensure templates in `resources/views/emails` are correctly formatted using Blade components.
