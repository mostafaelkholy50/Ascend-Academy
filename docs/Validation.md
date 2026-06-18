# Validation Strategy

- **Rule**: All request validation MUST occur in Form Request classes (`app/Http/Requests`).
- Never use inline `$request->validate()` within controllers.
- Use strict typing and rules (e.g., `string`, `max:255`, `exists:table,column`).
- Custom validation logic that requires complex database queries should be extracted into custom validation rules or handled in a Service layer before persistence, throwing a `ValidationException` if it fails.
