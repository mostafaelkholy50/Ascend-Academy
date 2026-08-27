# Attach Existing Students to Parent

## Overview

Admins can now link existing student accounts to a parent directly from the **Parent Details** page (`/admin/parents/{parent}`). The existing **Add Child** modal now has two modes:

1. **Create New** — create a brand-new student account (previous behavior).
2. **Select Existing** — pick one or more existing active students and attach them instantly.

The existing-student list is filtered in real-time on the front-end as the admin types a name.

## User Flow

1. Navigate to **Admin > Parents > Parent Details**.
2. Click **Add Child**.
3. Choose the **Select Existing** tab.
4. Type a student name in the search box; the list filters instantly (no server request).
5. Check the desired student(s).
6. Click **Attach Selected**.
7. The page refreshes and the selected students appear in the children list.

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

- The attach feature lives inside the existing **Add Child** modal.
- A tab toggle switches between **Create New** and **Select Existing**.
- The existing-student list is rendered as checkboxes with a front-end search filter.
- If no available students exist, an empty-state message is shown.

## Front-End Filter

Implemented with vanilla JavaScript in the same Blade file:

- `filterStudents(query)` hides/shows checkbox labels based on the `data-name` attribute.
- Filtering happens instantly on `input` events.
- A "No students match your search." message appears when no results remain.

## Tests

Tests are located in `tests/Feature/Admin/ParentTest.php`:

- `test_parent_show_page_displays_available_students_in_modal`
- `test_admin_can_attach_existing_student_to_parent`
- `test_admin_can_attach_multiple_existing_students_to_parent`
- `test_attach_students_requires_at_least_one_student_id`
- `test_attach_students_rejects_invalid_student_ids`
- `test_attached_students_are_excluded_from_available_list`
- `test_modal_shows_empty_state_when_no_available_students`

Run the parent feature tests with:

```bash
php artisan test --filter=ParentTest
```

## Files Changed

- `app/Http/Controllers/Admin/ParentController.php`
- `app/Http/Requests/Admin/AttachStudentsRequest.php`
- `app/Services/ParentService.php`
- `resources/views/admin/parents/show.blade.php`
- `routes/admin.php`
- `tests/Feature/Admin/ParentTest.php`
