# Events & Listeners

- The application uses Laravel's Event system to decouple secondary actions (like sending emails, updating cached counters, or triggering notifications) from the main business logic.
- **Location**: `app/Events` and `app/Listeners`.
- **Rule**: Listeners performing external network requests (like sending emails) MUST be queued by implementing the `ShouldQueue` interface.
