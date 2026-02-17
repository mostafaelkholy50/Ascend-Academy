# Testing Timezone Feature

## Understanding the System

**Important**: Your schedules are stored in **Egypt timezone (Africa/Cairo)** in the database. The conversion logic now properly handles this by:

1. Treating stored times as Egypt time
2. Converting to the user's target timezone

## Method 1: Update Database Directly (Recommended for Quick Testing)

### Step 1: Run the Migration

```bash
php artisan migrate
```

### Step 2: Set a Test User's Timezone

**For New York (EST/EDT - 7 hours behind Egypt)**:

```sql
UPDATE users SET timezone = 'America/New_York' WHERE email = 'student@example.com';
```

**For London (GMT/BST - 2 hours behind Egypt)**:

```sql
UPDATE users SET timezone = 'Europe/London' WHERE email = 'student@example.com';
```

**For Dubai (GST - 2 hours ahead of Egypt)**:

```sql
UPDATE users SET timezone = 'Asia/Dubai' WHERE email = 'student@example.com';
```

### Step 3: View Schedule

Login as that user and check the schedule times.

### Example Test Case:

- **Schedule in database**: 14:00 (2:00 PM Egypt time)
- **Student in New York sees**: 7:00 AM EST (or 8:00 AM EDT depending on DST)
- **Student in London sees**: 12:00 PM GMT (or 1:00 PM BST depending on DST)
- **Teacher always sees**: 2:00 PM EET (Egypt time)

---

## Method 2: Use Tinker to Test Conversion

### Step 1: Open Tinker

```bash
php artisan tinker
```

### Step 2: Test Timezone Conversion

```php
// Get a schedule
$schedule = \App\Models\Schedule::first();

// Show original time (Egypt time)
echo "Egypt time: " . $schedule->starts_at->format('Y-m-d H:i:s T') . "\n";

// Test conversion to New York
$nyTime = $schedule->getStartsAtInTimezone('America/New_York');
echo "New York time: " . $nyTime->format('Y-m-d H:i:s T') . "\n";

// Test conversion to London
$londonTime = $schedule->getStartsAtInTimezone('Europe/London');
echo "London time: " . $londonTime->format('Y-m-d H:i:s T') . "\n";

// Test conversion to Dubai
$dubaiTime = $schedule->getStartsAtInTimezone('Asia/Dubai');
echo "Dubai time: " . $dubaiTime->format('Y-m-d H:i:s T') . "\n";
```

---

## Method 3: Temporarily Change App Timezone (For Testing Only)

**⚠️ Warning**: This is for testing only, don't use in production!

### Step 1: Modify .env

```env
APP_TIMEZONE=America/New_York
```

### Step 2: Clear Config Cache

```bash
php artisan config:clear
```

### Step 3: View the Application

The entire application will now operate in New York timezone.

### Step 4: Restore After Testing

```env
APP_TIMEZONE=UTC
```

---

## Method 4: Browser DevTools Timezone Override

Modern browsers allow timezone override:

### Chrome/Edge:

1. Open DevTools (F12)
2. Press Ctrl+Shift+P (Cmd+Shift+P on Mac)
3. Type "timezone"
4. Select "Show Sensors"
5. Change timezone in the Sensors panel

### Firefox:

1. Type `about:config` in address bar
2. Search for `intl.tz.override`
3. Set to desired timezone (e.g., "America/New_York")
4. Restart browser

---

## Common Timezones to Test

| Location    | Timezone String       | Offset from Egypt                 |
| ----------- | --------------------- | --------------------------------- |
| New York    | `America/New_York`    | -7 hours (EST) or -6 hours (EDT)  |
| Los Angeles | `America/Los_Angeles` | -10 hours (PST) or -9 hours (PDT) |
| London      | `Europe/London`       | -2 hours (GMT) or -1 hour (BST)   |
| Dubai       | `Asia/Dubai`          | +2 hours                          |
| Tokyo       | `Asia/Tokyo`          | +7 hours                          |
| Sydney      | `Australia/Sydney`    | +8 or +9 hours (depends on DST)   |

---

## Verification Checklist

- [ ] Schedule times display correctly for students in different timezones
- [ ] Timezone abbreviation shows (EST, PST, GMT, etc.)
- [ ] Teachers and admins always see Egypt time regardless of timezone field
- [ ] "In Progress" and "Upcoming" badges work correctly based on actual current time
- [ ] Weekly view shows correct times
- [ ] Daily view shows correct times
- [ ] Parent view shows correct times for their children's schedules

---

## Example SQL Queries for Testing

### View Current User Timezones

```sql
SELECT id, name, email, role, timezone FROM users;
```

### Set Multiple Test Users

```sql
-- Set a student to New York timezone
UPDATE users SET timezone = 'America/New_York' WHERE id = 1 AND role = 'Student';

-- Set a parent to London timezone
UPDATE users SET timezone = 'Europe/London' WHERE id = 2 AND role = 'Parent';

-- Set a student to Dubai timezone
UPDATE users SET timezone = 'Asia/Dubai' WHERE id = 3 AND role = 'Student';
```

### Create a Test Schedule (in Egypt time)

```sql
INSERT INTO schedules (course_id, teacher_id, student_id, starts_at, ends_at, status, created_at, updated_at)
VALUES (1, 1, 1, '2026-02-18 14:00:00', '2026-02-18 15:00:00', 'scheduled', NOW(), NOW());
```

This schedule is at **2:00 PM Egypt time**:

- New York student should see: **7:00 AM EST**
- London student should see: **12:00 PM GMT**
- Dubai student should see: **4:00 PM GST**
