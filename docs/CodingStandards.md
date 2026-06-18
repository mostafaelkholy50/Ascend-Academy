# Coding Standards

This document outlines the coding standards that must be adhered to when contributing to the Ascend-Academy application.

## 1. General PHP Standards
- Follow **PSR-12** coding standards.
- Strict typing should be used where applicable (`declare(strict_types=1);`).
- Use explicit visibility on all methods and properties (`public`, `protected`, `private`).
- Return types must be defined for all methods.
- Type hints must be used for all method arguments.

## 2. Naming Conventions
- **Controllers**: Singular noun followed by `Controller` (e.g., `UserController`, `EnrollmentController`).
- **Models**: Singular noun, PascalCase (e.g., `User`, `Course`, `Schedule`).
- **Tables**: Plural noun, snake_case (e.g., `users`, `courses`, `schedules`).
- **Services**: Domain noun followed by `Service` (e.g., `PaymentService`).
- **Repositories**: Domain noun followed by `Repository` (e.g., `ScheduleRepository`).
- **Methods**: camelCase. Names should clearly describe what the method does (e.g., `calculateTotalAmount()`, `getActiveStudents()`).
- **Variables**: camelCase, descriptive names. Avoid single-letter variables except in simple loops.
- **Constants**: UPPER_SNAKE_CASE.

## 3. Clean Code Rules
- **DRY (Don't Repeat Yourself)**: Extract duplicated logic into reusable Services, Traits, or helper functions.
- **KISS (Keep It Simple, Stupid)**: Avoid over-engineering. Write code that is easy to understand.
- **YAGNI (You Aren't Gonna Need It)**: Do not add functionality until it is actually needed.
- **Small Functions**: Functions and methods should be small and do exactly one thing. If a method exceeds 20-30 lines, consider refactoring it.
- **No Dead Code**: Remove commented-out code, unused variables, and unreachable logic.

## 4. Laravel Specifics
- Use Route grouping heavily to keep `routes/` files organized.
- Do not use `env()` outside of configuration files. Use `config()` instead.
- Use Eloquent Relationships strictly. Avoid raw SQL queries unless absolutely necessary for performance, and document why.
- Avoid N+1 query problems by using Eager Loading (`with()`).

## 5. Validation and Error Handling
- Never trust user input. Validate everything using Form Requests.
- Use Transactions (`DB::transaction`) whenever multiple writes to the database must succeed together.
- Catch specific exceptions rather than a generic `\Exception` where possible.
- Never expose stack traces or sensitive internal data in API responses or production views.

---
*Note: This document is part of the Project Memory. Ensure all code passes these checks before submitting a Pull Request.*
