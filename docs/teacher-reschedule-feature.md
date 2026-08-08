# Teacher Reschedule Request Feature

This document explains the technical implementation of the "Teacher Reschedule Request" feature, which allows teachers to request a change of time for a specific session (schedule) directly from their daily schedule interface.

## Overview
Teachers have a "Reschedule" button next to their upcoming sessions. Clicking it opens a modal allowing them to pick a new date and time. If the new time is free of conflicts for both the teacher and the student, a `RescheduleRequest` is created.
The Admin or Scheduler Manager will then see a badge notification in the `Schedules` index page. They can view the pending requests and choose to either `Approve` or `Reject` them. If approved, the session's time is automatically updated to the new requested time.

## Database & Models
- **Table**: `reschedule_requests`
    - `schedule_id`: The ID of the session being rescheduled.
    - `teacher_id`: The teacher making the request.
    - `student_id`: The student associated with the session.
    - `new_starts_at` / `new_ends_at`: The new requested time for the session.
    - `status`: Stored as a string, using the `RescheduleRequestStatus` enum (`pending`, `approved`, `rejected`).
- **Model**: `App\Models\RescheduleRequest`
- **Enum**: `App\Enums\RescheduleRequestStatus`

## Routes
### Teacher
- `POST /teacher/schedule/{schedule}/reschedule-request`: Submits the request. Validates the new time against existing `hasTeacherConflict` and `hasStudentConflict` methods in the `Schedule` model.

### Admin/Scheduler
- `GET /scheduler/schedules/requests` or `/admin/schedules/requests`: Views the list of all pending requests.
- `POST /.../requests/{rescheduleRequest}/approve`: Approves the request, updates the schedule.
- `POST /.../requests/{rescheduleRequest}/reject`: Rejects the request.

## Views
1. **Teacher Daily Schedule (`resources/views/teacher/schedule-daily.blade.php`)**:
    - Added the "Reschedule" button.
    - Included the modal with the Datetime picker.
2. **Admin Schedules Index (`resources/views/admin/schedules/index.blade.php`)**:
    - Added a button and red notification badge for pending requests in the View Toggle header section.
3. **Pending Requests List (`resources/views/admin/schedules/requests.blade.php`)**:
    - A dedicated view displaying a table of requests, comparing original time vs requested time, and providing Approve/Reject action buttons.
