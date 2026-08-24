# Consecutive Absences Tracking System

## Overview
This feature allows administrators to easily track students who have missed more than two consecutive classes. By highlighting these students, the administration can follow up promptly and ensure that the students continue their education without dropping out.

## How It Works

### The Trigger (Attendance Observer)
Every time a teacher or admin records an attendance for a schedule, the `AttendanceObserver` is triggered:
1. It checks if the new attendance marks the student as `absent` (`student_present = false`).
2. If absent, it queries all recent past attendances for that student across all their enrolled schedules.
3. It counts the number of consecutive absences (stopping as soon as it hits a `present` record).
4. If the number of consecutive absences is **greater than 2** (i.e. 3 or more), an automated email notification (`ConsecutiveAbsenceNotification`) is immediately dispatched to all users holding the `Admin` or `SuperAdmin` roles.

### The Dashboard Interface
- **Permission**: To view the tracker, an admin must have the `view_absent_students` permission. By default, SuperAdmins and Admins have this.
- **Sidebar Link**: Located under the "Academic" section in the admin sidebar.
- **The Page**: Displays a counter of "At Risk Students". Below it, a table lists each student's name, their contact email/phone, the current count of consecutive absences they have, and a quick link to jump to their profile for more details.

## Technical Details
- **Controller**: `App\Http\Controllers\Admin\AbsentStudentController@index`
- **Observer**: `App\Observers\AttendanceObserver`
- **Notification**: `App\Notifications\ConsecutiveAbsenceNotification`
- **Permission Name**: `view_absent_students`

## Troubleshooting
If emails are not being sent:
1. Ensure your `.env` has valid `MAIL_*` configuration settings.
2. Check if the `QUEUE_CONNECTION` is set to `sync` or `database`/`redis`. If it's queued, ensure `php artisan queue:work` is running.
3. Ensure the SuperAdmin or Admin accounts have valid email addresses.
