# Service Map

This map outlines the verified services in the `app/Services` directory that have been statically analyzed.

## 1. `EnrollmentService`
- **Responsibilities**: Creates enrollments and initial payments.
- **Dependencies**: `EnrollmentRepository`, `EnrollmentFilter`.
- **References**: `app/Services/EnrollmentService.php`

## 2. `PaymentService`
- **Responsibilities**: Tracks monthly payments, acts as the gatekeeper to trigger `ScheduleService`.
- **Dependencies**: `ScheduleService`, `EnrollmentPayment` model.
- **References**: `app/Services/PaymentService.php`

## 3. `ScheduleService`
- **Responsibilities**: Generates schedules, detects overlap conflicts, generates bulk schedules for a month.
- **Dependencies**: `ScheduleRepository`, `ScheduleFilter`.
- **References**: `app/Services/ScheduleService.php`

## 4. `AttendanceService`
- **Responsibilities**: Records attendance, triggers auto-renewals for the next month.
- **Dependencies**: `AttendanceRepository`, `ScheduleService`.
- **References**: `app/Services/AttendanceService.php`

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 4

## Source References
- `app/Services/EnrollmentService.php`
- `app/Services/PaymentService.php`
- `app/Services/ScheduleService.php`
- `app/Services/AttendanceService.php`
