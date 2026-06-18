# Enrollment Flow

## Why it exists
The Enrollment flow is the core entry point for a student to begin taking classes. It binds a `Student` (User) to a `Course`, defining their schedule shape (days per week, duration) and financial obligation (admin price, currency).

## What it does
Managed primarily by `EnrollmentService::storeEnrollments`:
1. Validates that the student is not already enrolled in the selected course.
2. Creates the `Enrollment` record.
3. **Side Effect**: Instantly creates the first month's `EnrollmentPayment` record as 'unpaid'.

## Dependencies & Triggers
- **Who calls it**: Admin or Superadmin via `Admin\EnrollmentController`.
- **What depends on it**: The `ScheduleService` requires an active enrollment to generate class sessions.
- **Related Database Tables**: `enrollments`, `enrollment_payments`, `courses`, `users`.

## Business Constraints
- An enrollment must define `days_per_week` and `session_duration` to establish the schedule shape.
- Deleting an enrollment (via `EnrollmentService::deleteEnrollment`) triggers a cascading deletion of related Payments, Schedules, and Attendances wrapped in a strict Database Transaction to maintain integrity.

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 1

## Source References
- `app/Services/EnrollmentService.php`
