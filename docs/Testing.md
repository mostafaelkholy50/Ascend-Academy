# Testing Strategy

- **Rule**: Every feature MUST include adequate testing coverage.
- **Framework**: PHPUnit / Pest (as per `composer.json`).
- **Feature Tests**: Verify full HTTP request/response cycles, including authorization and database state changes.
- **Unit Tests**: Verify isolated business logic within Services.
- Do not deploy code without passing all tests (`php artisan test`).
