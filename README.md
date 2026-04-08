# Ascend-Academy (Quran Memorization Platform)

Ascend-Academy is a comprehensive online platform designed to manage and streamline Quran memorization sessions between teachers and students via Zoom. The system provides role-based access for Admins, Parents, Students, and Teachers, offering a centralized hub for scheduling, attendance tracking, performance reporting, resource sharing, and payment management.

---

## 🎯 Project Objectives

- Provide an easy registration process for parents to add their children.
- Empower administrators to manage schedules, assignments, and payments seamlessly.
- Offer a simple attendance and tracking system for teachers.
- Enable teachers to upload learning resources and submit detailed performance reports.
- Track payment statuses and manage teacher salaries based on logged hours.
- Automate notifications and reminders via scheduled emails.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js, Vite
- **Database:** MySQL
- **Video Sessions:** Zoom Links (Manual Integration)
- **Background Jobs:** Laravel Queue (Database Driver) for automated emails.

---

## 👥 User Roles & Features

### 1. 🛡️ Admin
- Manage Teachers, Courses, and Students manually if needed.
- Create system schedules (assigning Teachers, Students, Courses, Time, and Zoom Links).
- Monitor payment statuses.
- Calculate and track teacher salaries at the end of the month based on hours logged.
- Send targeted and scheduled emails.

### 2. 👨‍👩‍👧 Parent
- Create an account and register their children (Students).
- View performance reports and monitor each child's progress.
- Keep track of payment status.
- Access learning resources shared by the teacher.
- Receive updates, weekly reports, and daily payment reminders via email.

### 3. 🎓 Student
- Unique account associated with an email provided by the parent.
- Receives an automated welcome email upon registration.
- View upcoming class schedules.
- Access educational resources assigned for their courses.

### 4. 👨‍🏫 Teacher
- View daily class schedules.
- Record class attendance (for both themselves and the student).
- Add mandatory remarks for missed classes.
- Upload course resources (PDFs, Images, Videos, Audio, Links).
- Submit weekly/monthly student performance reports detailing strengths, weaknesses, and behavior.

---

## 🚀 Installation & Setup

Follow these steps to set up the project locally:

1. **Clone the repository** (if applicable) or navigate to the project directory.
2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```
3. **Install NPM Dependencies & Compile Assets:**
   ```bash
   npm install
   npm run build
   ```
4. **Environment Configuration:**
   Copy the `.env.example` file to create a new `.env` file:
   ```bash
   cp .env.example .env
   ```
   *Note: Make sure to configure your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in the `.env` file.*
5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
6. **Run Migrations & Seeders (to populate dummy data):**
   ```bash
   php artisan migrate --seed
   ```
7. **Run the Development Server:**
   You can start the server, Vite, and Laravel Queue simultaneously using the pre-configured built-in composer script:
   ```bash
   composer run dev
   ```
   *(Alternatively, run `php artisan serve`, `npm run dev`, and `php artisan queue:work` in separate terminal tabs).*

---

## 📂 Key System Flows

1. **Parent Onboarding:** A parent registers, accesses their dashboard, and adds their child. An automated welcome email is sent to the child.
2. **Scheduling:** The Admin matches a student with a course and a teacher, assigns a specific time, and attaches a Zoom link.
3. **Teaching & Tracking:** The teacher connects to the session, marks attendance, leaves a remark or uploads learning resources, and periodically submits a comprehensive student report.
4. **Payments & Tracking:** Admin tracks if a session/course is "Paid" or "Unpaid". Unpaid statuses trigger daily reminder emails to the parent. Teachers earn their calculated hourly rate based exactly on attended sessions.

---

## 📚 Database ERD Overview

Significant models in this application include:
`users` • `children` (students) • `courses` • `enrollments` • `schedules` • `attendance` • `resources` • `reports` • `payments` • `teacher_hours`
