# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]
- **Bugfix**: Fixed schedule overlap validation. It is now possible to schedule back-to-back appointments (e.g. 1:30 PM - 2:00 PM and 2:00 PM - 2:30 PM) for the same teacher or student without triggering a conflict error.
- **Feature**: Consolidated Teacher Appointment Notifications. Teachers now receive a single daily digest email instead of individual emails for every appointment via `SendClassReminders`.
- Initial creation of the project documentation suite (`/docs`).
