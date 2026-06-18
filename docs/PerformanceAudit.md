# Performance Audit (Phase 7)

## 1. Heavy Queries & N+1 Problems
- **Finding**: `ScheduleService::getCalendarData` fetches all schedules for the week and groups them manually in PHP. This is acceptable for small datasets, but if the academy grows to thousands of schedules per week, this could become a memory bottleneck.
- **Recommendation**: Ensure `starts_at` and `status` columns in the `schedules` table are indexed. (Verified: Migration `2026_05_09_191918_add_index_to_schedules_starts_at_and_status.php` exists).

## 2. Heavy Processing in Services
- **Finding**: `ScheduleService::generateMonthlySchedules` contains loops that perform individual `Schedule::create()` calls. If generating for many enrollments simultaneously (e.g., via a Job), this could cause DB thrashing.
- **Recommendation**: Consider using bulk inserts (`Schedule::insert()`) and dispatching notifications as batched Jobs.

## 3. Queue Opportunities
- **Finding**: `SendClassReminders` is correctly set up as a command, but the `TeacherDailyScheduleNotification` implements `ShouldQueue`. This is excellent for performance.
- **Recommendation**: Ensure that all other emails sent during synchronous web requests (like welcome emails) are also queued.

## 4. Caching
- **Finding**: Caching is currently underutilized.
- **Recommendation**: Data that changes rarely, such as `PricingTier` structures or `Course` lists, should be wrapped in `Cache::remember()`.
