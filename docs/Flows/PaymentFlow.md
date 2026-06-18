# Payment Flow

## Why it exists
To track financial obligations month-by-month for active enrollments and act as the gatekeeper for generating class schedules.

## What it does
Managed by `PaymentService`:
1. Automatically generates unpaid payment records for active enrollments every month.
2. Allows admins/accountants to toggle payment status between `paid` and `unpaid`.
3. **Crucial Side Effect**: When a payment is marked as `paid`, the service automatically triggers `ScheduleService::generateMonthlySchedules()` to create the student's classes for that specific month.

## Dependencies & Triggers
- **Who calls it**: Admin/Accountant dashboards.
- **Dependencies**: Relies on `ScheduleService` to fulfill the educational commitment once money is received.
- **Related Database Tables**: `enrollment_payments`, `enrollments`, `schedules`.

## Business Constraints
- Payments are rigidly tied to a specific `month` (stored as the first day of the month).
- There is currently no Webhook integration; payments are manually reconciled.

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 1

## Source References
- `app/Services/PaymentService.php`
