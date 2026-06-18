# Business Logic

This document maps the core business logic of Ascend-Academy, which is heavily centralized within the `app/Services/` directory.

## 1. Scheduling (`ScheduleService`, `TeacherScheduleService`, etc.)
- **Logic**: Handles the complex logic of generating class schedules based on enrollments, managing recurrences (`schedule_pattern`), handling conflicts, and allowing flexible scheduling.
- **Constraints**: Teachers must have availability that matches the scheduled time. 

## 2. Enrollments (`EnrollmentService`, `PaymentService`)
- **Logic**: Manages enrolling students into courses. Tracks `pricing_tiers`, the currency used, and linking the enrollment to specific payments.
- **Constraints**: Enrollments must define session duration and rate.

## 3. Attendances & Reports (`AttendanceService`, `ReportService`, `TeacherReportService`)
- **Logic**: Tracks if a student or teacher attended a scheduled class. Teachers must submit a report for completed classes.
- **Constraints**: A schedule cannot be marked as "Completed" without proper attendance and reporting.

## 4. HR & Payroll (`TeacherHourService`, `TeacherApplicationService`, `EvaluationService`)
- **Logic**: Calculates the hours worked by teachers based on completed schedules and their `hourly_rate`. Manages the recruitment process (applications) and quality control (evaluations).
- **Constraints**: Payroll access is strictly limited to specific roles (`admin`, `accountant`, `superadmin`).

## 5. Dashboards (`*DashboardService`)
- **Logic**: Prepares customized data sets for the various role-based dashboards (Admin, Teacher, Student, Parent, Scheduler).
- **Constraints**: Data must be tightly scoped to the authenticated user's role and relations (e.g., a parent only sees their children's data).

## Handling Side Effects
When a business action occurs (e.g., a schedule is changed), side effects must be handled via **Events and Listeners** or **Jobs** (e.g., sending email notifications). Services should dispatch events rather than sending emails directly inline to maintain fast response times and decoupled logic.

---
*Note: This document is part of the Project Memory.*
