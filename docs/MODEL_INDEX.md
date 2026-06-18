# Model Index

| Model | Table | Fillable | Relations |
|-------|-------|----------|-----------|
| App\Models\Attendance | attendances | schedule_id, student_id, teacher_id, student_present, teacher_present, remark, student_report, teacher_report |  |
| App\Models\Book | books | title, description, file_path, cover_image, is_active |  |
| App\Models\Children | children | parent_id, child_id |  |
| App\Models\Course | courses | title, description, photo, level, age_group, language, is_free |  |
| App\Models\Enrollment | enrollments | student_id, course_id, start_date, status, days_per_week, session_duration, schedule_pattern, admin_price, currency |  |
| App\Models\EnrollmentPayment | enrollment_payments | enrollment_id, month, amount, currency, payment_status, paid_at, notes |  |
| App\Models\Inquiry | inquiries | type, full_name, email, phone, child_name, child_age, child_gender, country, city, preferred_course, message, status, admin_notes, join_date, age, study_hours, courses_needed, sessions_per_week, available_days, referrer, gender, city_state |  |
| App\Models\News | news | title, slug, image, description, is_published, published_at |  |
| App\Models\PricingTier | pricing_tiers | days_per_week, session_duration, price_cad, price_usd, price_gbp, is_active, notes |  |
| App\Models\Report | reports | teacher_id, student_id, course_id, level, mastery_score, strengths, weaknesses, behavior, notes, report_date |  |
| App\Models\Resource | resources | teacher_id, student_id, course_id, title, description, type, file_path, mime_type, external_url |  |
| App\Models\Role | roles | name, guard_name, allowed_countries, can_access_payroll |  |
| App\Models\Schedule | schedules | enrollment_id, course_id, teacher_id, student_id, starts_at, ends_at, zoom_link, status, notes |  |
| App\Models\StudentEvaluation | student_evaluations | teacher_id, student_id, course_id, evaluation_date, evaluation_month, evaluation_year, q1_score, q2_score, q3_score, q4_score, q5_score, q6_score, q7_score, q8_score, q9_score, q10_score, total_score, notes |  |
| App\Models\TeacherApplication | teacher_applications | full_name, email, phone, country, city, gender, birth_date, education_level, certifications, years_of_experience, teaching_experience, subjects, age_groups, teaching_methodology, availability, has_stable_internet, has_quiet_space, why_join, cv_path, status, admin_notes |  |
| App\Models\TeacherEvaluation | teacher_evaluations | teacher_id, evaluator_id, evaluation_date, week_start_date, q1_score, q2_score, q3_score, q4_score, q5_score, q6_score, q7_score, q8_score, q9_score, q10_score, total_score, notes |  |
| App\Models\TeacherHour | teacher_hours | teacher_id, year, month, total_hours, total_salary, notes, is_paid, paid_at |  |
| App\Models\User | users | name, email, password, role, avatar, gender, phone, timezone, country, allowed_countries, can_access_payroll, birth_date, active, class_reminders_enabled, hourly_rate, teacher_application_id | roles, permissions |
| App\Models\UserAvailability | user_availabilities | user_id, day_of_week, start_time, end_time |  |
