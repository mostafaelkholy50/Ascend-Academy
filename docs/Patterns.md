# Project Patterns

This document defines the architectural and design patterns strictly enforced within the Ascend-Academy application. **Consistency is mandatory.** Never introduce a new pattern unless the whole project already uses it or it is approved by the architectural team.

## 1. Service Pattern
- **Usage**: Encapsulates business logic. Controllers should be thin, delegating complex operations to Service classes.
- **Location**: `app/Services/`
- **Rule**: A Service class should generally correspond to a specific domain entity or feature (e.g., `EnrollmentService`, `ScheduleService`).

## 2. Repository Pattern
- **Usage**: Abstracts the data layer (Eloquent ORM).
- **Location**: `app/Repositories/`
- **Rule**: Controllers and Services must not call Eloquent models directly for complex queries. Instead, they should inject the appropriate Repository. This promotes testability and decoupling from the database implementation.

## 3. Form Requests
- **Usage**: Handles all incoming HTTP request validation.
- **Location**: `app/Http/Requests/`
- **Rule**: Do not use inline `$request->validate()` in controllers. Always create a dedicated Form Request class for any endpoint accepting data.

## 4. Policies & Gates
- **Usage**: Handles authorization.
- **Location**: `app/Policies/`
- **Rule**: Every action that modifies data or accesses sensitive information must be protected by a Policy. Use Gates for non-resource specific permissions.

## 5. View Composers
- **Usage**: Binds data to views across multiple controllers without repeating code.
- **Location**: `app/View/Composers/` (if utilized).

## 6. Dependency Injection
- **Usage**: Classes (Controllers, Jobs, Services) should explicitly declare their dependencies in their constructor (or methods) rather than resolving them via Facades or the `app()` helper whenever possible.
- **Rule**: Favor constructor injection.

## 7. SOLID Principles
- **S** - Single Responsibility Principle: A class should have one, and only one, reason to change.
- **O** - Open/Closed Principle: Software entities should be open for extension, but closed for modification.
- **L** - Liskov Substitution Principle: Objects in a program should be replaceable with instances of their subtypes without altering the correctness of that program.
- **I** - Interface Segregation Principle: Many client-specific interfaces are better than one general-purpose interface.
- **D** - Dependency Inversion Principle: Depend upon abstractions, not concretions.

---
*Note: This document is part of the Project Memory. Do not deviate from these patterns.*
