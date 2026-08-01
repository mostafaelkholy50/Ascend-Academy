# Paused Schedule Conflict Behavior

## What changed

- A schedule whose enrollment day is marked `active: false` in `enrollments.schedule_pattern` no longer blocks new bookings for the same teacher or student on that day.
- Conflict checks still ignore `cancelled` schedules, and now they also ignore schedules that belong to paused recurring days.

## Affected flows

- `admin/schedules/create`
- `admin/schedules/enrollment/{enrollment}/edit-pattern`
- `admin/schedules/enrollment/{enrollment}/toggle-all`
- recurring generation in `ScheduleService`

## Database impact

- No schema change.
- Existing rows in `schedules` remain the source of truth for booked sessions.
- The `enrollments.schedule_pattern` JSON column controls whether a day is considered active for conflict detection.

## Result

- If Amna's Monday schedule is paused for Ms. Samar, the system allows another student to be assigned to the same teacher time slot without throwing a teacher conflict.
- Once the day is resumed, those slots become conflict-relevant again.
