# Database Map

This map outlines the chronological database migrations in the `database/migrations` directory, tracking the evolution of the schema.

## Verified Migrations (Key Entities)
The database structure has evolved significantly since November 2025:
1. **Core Entities**: `users`, `courses`, `enrollments`, `schedules`, `attendances`, `reports`, `inquiries`.
2. **Financials**: `pricing_tiers`, `enrollment_payments`.
3. **HR & Evaluations**: `teacher_applications`, `teacher_hours`, `teacher_evaluations`, `student_evaluations`.
4. **Platform Features**: `news`, `books`, `notifications`.
5. **Authorization**: `permission_tables` (Spatie).

*Note: Specific table relationships and constraints are documented within the Model definitions.*

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 46

## Source References
- `database/migrations/` (Directory scan)
