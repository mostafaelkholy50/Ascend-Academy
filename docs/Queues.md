# Queues & Jobs

- Heavy processing tasks, scheduled commands, and external API calls (e.g., mailings) are pushed to the Queue.
- **Location**: `app/Jobs`.
- The default queue connection is defined in `.env`. Ensure the queue worker (`php artisan queue:work`) is running in production.
- Always handle job failures by implementing the `failed()` method in the Job class.
