# Attendance Monthly Statistics

## Overview
The Attendance page (`/scheduler/attendance`) has been upgraded to provide accurate, easy-to-read statistics cards. These cards automatically separate data for **Students** and **Teachers**, allowing administration to quickly assess the performance of the academy.

## How It Works

### Dynamic Filtering & Default Current Month
The core calculation logic resides in `App\Repositories\AttendanceRepository@getStats`.
- **Default Behavior**: If no specific date filters (`date_from` or `date_to`) are provided in the HTTP request, the system automatically restricts the statistics calculation to the **Current Month** (e.g., all schedules starting in August of the current year).
- **Filtered Behavior**: If the admin applies date filters or selects a specific student/teacher/course, the statistics dynamically adjust to reflect exactly the data present in the filtered view.

### Student Statistics Calculation
For all sessions in the filtered period (or current month):
1. **Total Sessions**: The total number of sessions scheduled.
2. **Attended**: Number of sessions where the student was marked as present (`student_present = true`).
3. **Absent (Student)**: Number of sessions where the student was marked absent (`student_present = false`).
4. **Absent (Teacher)**: Number of sessions where the teacher was marked absent (`teacher_present = false`). This helps clarify if a session failed because the teacher didn't show up.

### Teacher Statistics Calculation
For all sessions in the filtered period (or current month):
1. **Total Sessions**: The total number of sessions scheduled.
2. **Attended**: Number of sessions where the teacher was marked as present (`teacher_present = true`).
3. **Absent (Teacher)**: Number of sessions where the teacher was marked absent (`teacher_present = false`).
4. **Absent (Student)**: Number of sessions where the student was marked absent (`student_present = false`). This clarifies that the teacher was present but the student did not attend.

## UI Design
The UI (`resources/views/admin/attendances/index.blade.php`) splits these statistics into two visually distinct horizontal blocks:
- **Student Statistics** (Blue/Indigo Gradient)
- **Teacher Statistics** (Emerald/Teal Gradient)

Each block contains 3 primary cards (Attended, Absent, Partner Absent) with clear, colored icons (Green for success, Red/Orange for absence) to draw the eye to critical metrics immediately.

## Unit Tests
The logic is fully tested via the `MonthlyAttendanceStatsTest` feature test.
This test ensures that:
- Old attendances (from previous months) are completely ignored if no date filter is applied.
- The separation of absences between teacher and student is calculated mathematically correctly.

## Individual Attendance Profiles
Administrators can now drill down into specific student or teacher attendance records by clicking on their names in the main Attendance index tables.

### Student Profile (`/admin/attendances/student/{user}`)
When viewing a specific student, the system displays:
1. **Teachers Summary**: A responsive grid of cards showing every teacher the student has had sessions with, along with precise counts of Total, Attended, Absent, and Teacher Absent metrics specifically for that student-teacher relationship.
2. **Session History Log**: A comprehensive, paginated (15 per page) log of all past sessions the student was scheduled for, descending by date. 
   - Displays the Date, Course, Teacher, and Status.
   - Crucially, displays any **Absence Reason / Report** (from `remark` or `student_report`) if an absence occurred, allowing admins to immediately see *why* the student missed the class.

### Teacher Profile (`/admin/attendances/teacher/{user}`)
When viewing a specific teacher, the system displays:
1. **Students Summary**: A responsive grid of cards showing every student the teacher has taught, with precise counts of Total, Attended, Absent, and Student Absent metrics specifically for that teacher-student relationship.
2. **Session History Log**: A comprehensive, paginated (15 per page) log of all past sessions the teacher taught, descending by date.
   - Displays the Date, Course, Student, and Status.
   - Displays any **Absence Reason / Report** (from `remark` or `teacher_report`) if an absence occurred.

### Profile UI & Responsiveness
Both profile pages are built with full responsiveness in mind:
- **Desktop**: The session history is displayed in a clean, wide table format.
- **Mobile**: The session history table hides, and is replaced by a stack of clean cards. Each card highlights the date, status badge, the associated user, and a highlighted yellow alert box containing the absence reason (if applicable).

### Unit Tests
The profile routes and data aggregation are tested in `tests/Feature/AttendanceProfileTest.php`, which verifies that:
- Admins can access both profile routes.
- The correct views and view variables (`students`, `teachers`, `attendances`) are returned.
- The relationship counts and queries accurately filter for the specific user being viewed.
