# Schedule Management & Conflict Resolution

## Overview
The Schedule Management system in Ascend Academy handles the booking of classes for students with teachers. It supports both manual schedule creation (via the admin dashboard), automated monthly schedule generation for active enrollments, and bulk schedule pattern editing.

## Bulk Schedule Pattern Editing
Admins can modify the schedule pattern (days of the week, times, session duration, and teacher) for an active enrollment.

**How it works:**
1. The admin selects new days, times, duration, and a teacher.
2. The system locates **ALL** sessions for the enrollment (both past and future).
3. The system deletes all these sessions.
4. It then generates new sessions starting from the very first session date up to the date of the very last session, using the new pattern.
5. Similar to initial creation, this process is strictly atomic. If the new pattern causes a conflict on *any* generated day (past or future), the entire transaction is rolled back, no old sessions are deleted, and no new sessions are created. An error is shown to the user.

## Conflict Resolution Logic
To prevent double-booking and assure data consistency, the system enforces a strict all-or-nothing (atomic) scheduling policy. When an admin or automated script attempts to generate schedules for a student, the system checks for two types of conflicts:

1. **Teacher Conflicts**: A teacher is already booked with another student at the requested time.
2. **Student Conflicts**: The student is already booked for another course/teacher at the requested time.

### Error Messages & Transparency
If a conflict is detected, the system immediately halts the scheduling process and provides a detailed error message indicating:
- The exact date and time of the conflict.
- The name of the student who currently holds the booking.
- The name of the course the booking is for.

**Example Error Message:**
`Cannot create schedule due to conflicts:`
`Teacher conflict on Sunday, Feb 01, 2026 at 8:30 PM (Teacher John Doe is booked with Student Jane Smith for Quran Basics)`

## Transactions & Atomic Operations
The system ensures that partial bookings never occur. It does this by wrapping the schedule generation process inside a database transaction (`DB::transaction` / `DB::beginTransaction()`).

- **If all sessions are conflict-free:** The transaction commits, and all schedules are saved to the database.
- **If any session has a conflict:** The transaction is rolled back. No schedules are created, preventing situations where a student is partially booked for a month.

### Code Locations
- **`App\Models\Schedule`**: Contains the `getTeacherConflict()` and `getStudentConflict()` methods which retrieve the conflicting schedule along with its associated student, teacher, and course models.
- **`App\Services\ScheduleService`**: Contains `storeSchedule()`, `generateMonthlySchedules()`, and `updateSchedulePattern()`. All methods are fully transactional and rely on the model's conflict detection to abort and throw descriptive exceptions.

## Testing
The conflict and transaction logic is covered by automated feature tests located in:
- `tests/Feature/Admin/ScheduleCreationConflictTest.php`
- `tests/Feature/Admin/SchedulePatternEditTest.php`

This ensures that any future changes to the system will not inadvertently reintroduce partial bookings or vague error messages.
