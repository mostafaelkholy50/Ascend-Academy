# Attendance & Payroll Flow

## Why it exists
To record class completion, gather quality control reports, trigger automatic schedule renewals, and eventually calculate teacher payroll.

## What it does
Managed by `AttendanceService` and `TeacherHourService` (implicitly):
1. Teacher submits attendance (`student_present`, `teacher_present`, reports).
2. If both are present, the `Schedule` status changes to `completed`.
3. **Crucial Side Effect (Auto-Renewal)**: If the schedule is completed and there are 1 or fewer future sessions left for the enrollment, `AttendanceService` checks if next month's payment is already `paid`. If it is, it automatically calls `ScheduleService::generateMonthlySchedules` for the next month to ensure continuous classes without manual admin intervention.

## Dependencies & Triggers
- **Who calls it**: Teachers via the Teacher portal.
- **Dependencies**: Relies heavily on `ScheduleService`.
- **Related Database Tables**: `attendances`, `schedules`, `reports`, `enrollment_payments`.

## Business Constraints
- A schedule cannot be completed without an attendance record.
- Auto-renewal logic is a silent background process that relies on payments being recorded in advance.

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 1

## Source References
- `app/Services/AttendanceService.php`
