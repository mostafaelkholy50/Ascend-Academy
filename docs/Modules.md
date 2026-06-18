# System Modules

The Ascend-Academy application is composed of several tightly integrated modules, managed via `app/Services`:

## 1. Scheduling Module (`ScheduleService`, `TeacherScheduleService`)
- Responsible for generating class sessions from a parent `Enrollment`.
- Handled automatically when a payment is marked as `paid`.
- Supports recurring scheduling patterns and conflict detection.

## 2. Enrollment & Financials Module (`EnrollmentService`, `PaymentService`)
- Manages student registration into courses.
- Payments are tracked internally via the `enrollment_payments` table on a monthly basis.
- Currently, there are no direct 3rd-party gateway integrations (e.g., Stripe/PayPal) found in `PaymentService`; payments are managed administratively (e.g., Accountant marks as 'paid').

## 3. Human Resources & Evaluations Module (`TeacherApplicationService`, `EvaluationService`)
- Handles recruitment flow (`teacher_applications`).
- Tracks hours worked (`teacher_hours`) based on completed schedules.
- Collects and aggregates Quality Control evaluations for both teachers and students.

## 4. Notifications Module (`NotificationService`)
- Sends system alerts (e.g., Schedule Reminders, Application status updates).
- Relies on Laravel's Database and Mail notification channels.
