# Gym Supervision System - Project Structure

## Root Files
- `config.php` - Database and SMS API configuration, connection setup.
- `index.php` - Dashboard with analytics and navigation.
- `login.php` - User login page.
- `logout.php` - User logout script.
- `auth.php` - Authentication helper functions.
- `README.md` - Project overview and setup instructions.
- `gym_supervision_system.sql` - Database schema.

## Cleanliness Management Module
- `cleanliness_areas.php` - Manage cleanliness areas.
- `cleanliness_shifts.php` - Manage cleanliness shifts.
- `cleaning_staff.php` - Manage cleaning staff.
- `cleanliness_timetable.php` - Manage cleanliness timetable.
- `cleanliness_ratings.php` - Manage cleanliness ratings.

## Gym Classes Module
- `coaches.php` - Manage gym coaches.
- `gym_classes.php` - Manage gym classes timetable.
- `class_attendance.php` - Manage class attendance.
- `coach_ratings.php` - Manage coach ratings.

## Gym Area Management Module
- `gym_areas.php` - Manage gym areas.
- `gym_area_timetable.php` - Manage gym area timetable and staff shifts.

## Machine Maintenance Module
- `machine_categories.php` - Manage machine categories.
- `machines.php` - Manage machines.
- `machine_status_logs.php` - Manage machine status logs with notifications.

## Notes
- All PHP files use Tailwind CSS for styling and Google Fonts for typography.
- SMS notifications are integrated via textsms.co.ke API (placeholder API key).
- The system requires PHP with MySQLi extension enabled.
- The project is modular for easy expansion and maintenance.
