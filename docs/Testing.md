# Testing Strategy

- **Rule**: Every feature MUST include adequate testing coverage.
- **Framework**: PHPUnit / Pest (as per `composer.json`).
- **Feature Tests**: Verify full HTTP request/response cycles, including authorization and database state changes.
- **Unit Tests**: Verify isolated business logic within Services.
- Do not deploy code without passing all tests (`php artisan test`).

## Lessons Learned & Gotchas

### 1. Copy-Pasting Assertions Between Views
- **Issue**: A test for the daily schedule view failed because an assertion (`assertSee('min-height: 120px')`) was mistakenly copy-pasted from the weekly schedule view test. While the weekly view hardcoded the style string, the daily view used CSS variables (`--row-height-mobile`), causing the assertion to fail even though the UI was correct.
- **Rule**: Always verify that the assertions match the specific DOM/CSS structure of the view being tested rather than copying them blindly from related tests.

### 2. Enrollment Schedule Patterns
- **Issue**: `TeacherScheduleService` filters out schedules on days that are marked as inactive in an enrollment's `schedule_pattern`. When creating dummy `Enrollment` models for schedule-related tests, failing to include a valid `schedule_pattern` can cause the schedule to be unexpectedly filtered out.
- **Rule**: Always define a valid `schedule_pattern` JSON array when seeding or factory-creating an `Enrollment` if the test relies on `TeacherScheduleService` fetching its schedules.
