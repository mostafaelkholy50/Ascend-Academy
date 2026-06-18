# Scheduling Flow

## Why it exists
To translate an abstract `Enrollment` (e.g., "Math, 3 days a week") into concrete, date-and-time specific `Schedule` sessions assigned to a specific `Teacher`.

## What it does
Managed by the massive `ScheduleService` (the most complex service in the application):
1. Calculates default days based on `days_per_week` (e.g., 3 days = Monday, Wednesday, Friday).
2. Generates individual `Schedule` records for a given month.
3. Detects conflicts: It actively queries if the Teacher or the Student already has an overlapping schedule (`hasTeacherConflict`, `hasStudentConflict`). If so, it skips generation for that specific day or throws an error.
4. **Side Effect**: Dispatches notifications (`ScheduleAssignedNotification`, `StudentScheduleAssignedNotification`) to Teachers, Students, and Parents when schedules are created or modified.

## Dependencies & Triggers
- **Who calls it**: 
  1. Admins manually via `ScheduleController`.
  2. `PaymentService` automatically when a month is paid.
  3. `AttendanceService` automatically for "auto-renewal".
- **Related Database Tables**: `schedules`, `enrollments`, `users` (Teacher/Student).

## Business Constraints
- Schedules strictly check for overlaps. A teacher cannot be double-booked.
- Schedules carry the `status` (scheduled, completed, cancelled).

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 1

## Source References
- `app/Services/ScheduleService.php`
