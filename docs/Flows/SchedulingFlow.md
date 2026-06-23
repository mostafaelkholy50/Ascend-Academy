# Scheduling Flow

## Why it exists
To translate an abstract `Enrollment` (e.g., "Math, 3 days a week") into concrete, date-and-time specific `Schedule` sessions assigned to a specific `Teacher`.

## What it does
Managed by the massive `ScheduleService` (the most complex service in the application):
1. Calculates default days based on `days_per_week` (e.g., 3 days = Monday, Wednesday, Friday).
2. Normalizes the schedule pattern into `day => [times...]` so a single weekday can generate multiple sessions.
3. Generates individual `Schedule` records for a given month, one row per selected time.
4. Detects conflicts: It actively queries if the Teacher or the Student already has an overlapping schedule (`hasTeacherConflict`, `hasStudentConflict`). If so, it aborts the transaction and returns a descriptive error.
5. **Side Effect**: Dispatches notifications (`ScheduleAssignedNotification`, `StudentScheduleAssignedNotification`) to Teachers, Students, and Parents when schedules are created or modified.

## Dependencies & Triggers
- **Who calls it**: 
  1. Admins manually via `ScheduleController`.
  2. `PaymentService` automatically when a month is paid.
  3. `AttendanceService` automatically for "auto-renewal".
- **Related Database Tables**: `schedules`, `enrollments`, `users` (Teacher/Student).

## Business Constraints
- Schedules strictly check for overlaps. A teacher cannot be double-booked.
- Schedules carry the `status` (scheduled, completed, cancelled).
- A single weekday may now expand into multiple sessions; all downstream consumers must treat schedules as a list of session rows, not a day summary.
- Existing single-time patterns remain valid input, but they are normalized internally before any persistence or conflict check.

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 1

## Source References
- `app/Services/ScheduleService.php`
