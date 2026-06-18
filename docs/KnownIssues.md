# Known Issues & Technical Debt

This document serves as a ledger for tracking bugs and technical debt.

## 1. Controller Authorization
- **Issue**: The application currently relies entirely on `spatie/laravel-permission` route middleware and manual service-level scoping to protect data. Standard Laravel Controller Policies (`Gate::authorize()`) are largely absent.
- **Risk**: High risk of IDOR (Insecure Direct Object Reference) if a developer forgets to scope a query (e.g., `User::find($id)` instead of `auth()->user()->students()->find($id)`) in a new feature.
- **Recommendation**: Begin implementing and enforcing `app/Policies/` for all major entities (Enrollments, Schedules, Reports).

## 2. Payment Gateway Integration
- **Issue**: `PaymentService` currently tracks payments manually (admin updates status). There is no automated Webhook integration for Stripe, PayPal, or local gateways (Paymob/Fawry).
- **Recommendation**: Prepare `EnrollmentPayment` model to accept `transaction_id` and webhook payloads for future automation.
