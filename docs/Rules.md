# Project Rules

These rules are strictly extracted from the existing Ascend-Academy codebase. **Never deviate from these.**

## Architecture & Dependency Injection
1. **Service Pattern**: Business logic MUST reside in `app/Services/`. Controllers must be thin and only handle HTTP routing and returning views/responses.
2. **Repository Pattern**: Data access MUST reside in `app/Repositories/`. Services should inject Repositories via constructor. Models should not be queried directly in Controllers for complex operations.
3. **Dependency Injection**: Always inject dependencies into constructors (e.g., `__construct(ScheduleRepository $repository)`). Avoid using `app()` or Facades where DI is possible.

## Validation
1. **Form Requests**: All incoming data MUST be validated using dedicated Form Request classes (`app/Http/Requests`). Never use inline `$request->validate()` inside Controllers.

## Security & Authorization
1. **Route Middleware**: All non-public routes must be wrapped in `auth` middleware. Role-specific routes must be grouped and protected by `spatie/laravel-permission` middleware.
2. **Data Scoping**: Because traditional Laravel Policies (`Gate::authorize()`) are not globally implemented, queries in Services and Repositories MUST be manually scoped to the authenticated user's ID/Role to prevent IDOR (Insecure Direct Object Reference).
3. **Mass Assignment**: Always define `$fillable` on Models. Never pass `$request->all()` to a Model's `create` or `update` method directly.

## Error Handling & Logging
1. **Transactions**: Any logic that performs multiple database writes (e.g., creating an Enrollment and its initial Payment) MUST be wrapped in a `DB::transaction()`.
2. **Logging**: Catch exceptions and log them using the `Log::error()` facade rather than returning raw stack traces to the user.

## Naming Conventions
- **Controllers**: Singular noun + Controller (`TeacherController`).
- **Services**: Domain + Service (`ScheduleService`).
- **Repositories**: Domain + Repository (`ScheduleRepository`).

## Testing & Documentation Updates
- **Mandatory Markdown Documentation**: ANY modification to the codebase (adding routes, controllers, views, logic changes) MUST be recorded in the relevant `.md` files inside the `docs/` folder (e.g. `ROUTE_INDEX.md`, `ControllerMap.md`, `INDEX.md`, etc.). This is a strict rule to ensure I never need to be told to document changes.
- **Mandatory Unit Testing**: ANY modification or new feature MUST have a comprehensive unit test or feature test created or updated to test the functionality thoroughly. This ensures high code quality and confidence when revisiting files.

## 12-Step Quality Gate
Before any code modification is considered complete, it MUST pass the following checks:
1. Architecture consistency
2. Business consistency
3. Coding standards
4. Security validation
5. Performance validation
6. Static analysis
7. Unit tests
8. Feature tests
9. Regression tests
10. Documentation update
11. Dependency validation
12. No dead code / duplicated code / broken routes / failing migrations
