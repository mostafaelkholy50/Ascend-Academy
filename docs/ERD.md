# Entity Relationship Diagram (ERD)

This document serves as a text-based representation of the database relationships.

- **Users** (`users`): `hasMany` Enrollments, Schedules, Attendances. `hasOne` TeacherApplication.
- **Roles/Permissions** (`roles`, `permissions`): `belongsToMany` Users.
- **Courses** (`courses`): `hasMany` Enrollments, PricingTiers, Resources.
- **Enrollments** (`enrollments`): `belongsTo` User, Course. `hasMany` Schedules, EnrollmentPayments.
- **Schedules** (`schedules`): `belongsTo` Enrollment. `hasOne` Attendance.
- **Attendances** (`attendances`): `belongsTo` Schedule. `hasOne` Report.
- **Evaluations** (`teacher_evaluations`, `student_evaluations`): `belongsTo` User.

*(Keep updated as schema changes)*
