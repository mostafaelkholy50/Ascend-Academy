# Policy Map

This map outlines the verified Policies in the `app/Policies` directory.

## Verified Policies
**None.** 

The directory `app/Policies` does not exist in this project. 
Authorization is handled via:
1. Route-level middleware (`spatie/laravel-permission`) grouping routes by role (e.g., `routes/teacher.php`).
2. Manual query scoping inside Repositories and Services (e.g., `where('teacher_id', auth()->id())`).

*Note: This architectural pattern has been flagged in `SecurityAudit.md` as a potential IDOR risk if a developer forgets to apply manual data scoping.*

---
**Last Scan Date:** June 2026
**Analyzed Files Count:** 0

## Source References
- `app/Policies/` (Directory Not Found)
