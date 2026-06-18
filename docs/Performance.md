# Performance Optimization

- **Database**: Prevent N+1 queries by eager loading relationships (`with()`).
- **Caching**: Utilize `Cache::remember()` for heavily queried, infrequently changing data (e.g., pricing tiers, static dropdown lists).
- **Queues**: Offload heavy tasks (emails, report generation) to background jobs.
