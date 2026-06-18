# Security Strategy

Security is the highest priority for the Ascend-Academy application. All development must adhere to the following principles.

## 1. Authentication & Authorization
- **Authentication**: Managed via Laravel's built-in auth system.
- **Authorization**: Managed via `spatie/laravel-permission`. 
- **Rule**: Every single route (except public landing/inquiry pages) must be protected by the `auth` middleware. Role-specific routes (e.g., `/admin/*`) must be protected by role-checking middleware.

## 2. Policies & Gates
- **Rule**: Access to specific models (e.g., updating an Enrollment, viewing a Schedule) must be authorized using Policies (`app/Policies`).
- Controllers must check authorization before performing any action: `$this->authorize('update', $enrollment);` or via Form Requests `authorize()` method.

## 3. Data Validation & Mass Assignment
- **Rule**: Never pass `$request->all()` directly to a model.
- Always use **Form Requests** (`app/Http/Requests`) to validate input.
- Ensure Models have the `$fillable` property strictly defined to prevent Mass Assignment vulnerabilities.

## 4. SQL Injection & XSS
- **Rule**: Always use Eloquent ORM or the Query Builder, which use PDO parameter binding automatically. Never concatenate raw input into queries.
- **Rule**: Blade templates automatically escape output using `{{ $variable }}`. Only use `{!! $variable !!}` if the content is verified as completely safe and stripped of malicious HTML.

## 5. File Upload Security
- **Rule**: All file uploads (e.g., course resources, teacher applications) must strictly validate:
  - File extension/MIME type (`mimes:pdf,jpg,png`).
  - File size (`max:2048`).
- Files must be stored securely (often in a non-public storage disk) and accessed via controlled routes if sensitive.

## 6. Secrets & Environment
- **Rule**: Never hardcode API keys, passwords, or secrets in the codebase. Always use `.env` and retrieve via `config('app.name')`.
- Passwords must always be hashed using Laravel's `Hash` facade before storing.

## 7. Least Privilege Principle
- Users should only have the permissions strictly necessary to perform their job functions.
- Do not grant `superadmin` access unnecessarily.

---
*Note: This document is part of the Project Memory. Any security flaws found must be patched immediately.*
