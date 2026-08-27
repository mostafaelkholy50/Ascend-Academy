# Attach Existing Students to Parent

## Overview

Admins can now link existing student accounts to a parent directly from the **Parent Details** page (`/admin/parents/{parent}`). Previously, the "Add Child" button only allowed creating a brand-new student account. This feature adds a select dropdown (similar to the children selection on the parent creation form) so admins can pick one or more existing active students and attach them instantly.

## User Flow

1. Navigate to **Admin > Parents > Parent Details**.
2. In the **Children** section, a new dropdown lists all active students that are **not** already linked to this parent.
3. Select one or more students.
4. Click **Attach Student(s)**.
5. The page refreshes and the selected students appear in the children list.

## Route

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| POST | `/admin/parents/{parent}/attach-students` | `admin.parents.attach-students` | `Admin\ParentController@attachStudents` |

## Request Validation

Handled by `App\Http\Requests\Admin\AttachStudentsRequest`:

- `student_ids` is required and must be an array with at least one item.
- Every `student_ids.*` value must be an integer and must exist in the `users` table with:
  - `role = 'Student'`
  - `active = true`

## Backend Logic

- `ParentController::show()` loads the parent with children and queries `availableStudents`:
  - Active students (`role = 'Student'`, `active = true`).
  - Excludes students already attached to the current parent.
- `ParentService::attachStudents()` runs inside a database transaction:
  - Deduplicates the submitted IDs.
  - Uses `$parent->children()->attach($studentIds)` to create the relationships.

## UI Location

`resources/views/admin/parents/show.blade.php`

- The attach form is rendered inline in the **Children** card header, next to the existing **Add Child** button.
- The dropdown is hidden automatically when no available students remain.

## Tests

Tests are located in `tests/Feature/Admin/ParentTest.php`:

- `test_parent_show_page_displays_available_students`
- `test_admin_can_attach_existing_student_to_parent`
- `test_admin_can_attach_multiple_existing_students_to_parent`
- `test_attach_students_requires_at_least_one_student_id`
- `test_attach_students_rejects_invalid_student_ids`
- `test_already_attached_student_is_not_shown_in_available_students`

Run the parent feature tests with:

```bash
php artisan test --filter=ParentTest
```

## Files Changed

- `app/Http/Controllers/Admin/ParentController.php`
- `app/Http/Requests/Admin/AttachStudentsRequest.php` (new)
- `app/Services/ParentService.php`
- `resources/views/admin/parents/show.blade.php`
- `routes/admin.php`
- `tests/Feature/Admin/ParentTest.php`
