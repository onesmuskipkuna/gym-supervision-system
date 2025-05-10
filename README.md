# Gym Supervision System

This is a Gym Supervision System built with PHP and MySQLi. It includes features for managing cleanliness, gym classes, gym areas, machine maintenance, user authentication, and analytics.

## Features Implemented So Far

- User authentication with admin user creation
- Cleanliness management: areas and shifts modules
- Dashboard with sample analytics charts
- SMS integration placeholder for textsms.co.ke API
- Modular structure for easy expansion

## Setup Instructions

1. Import the database schema:

```bash
mysql -u your_user -p < gym_supervision_system.sql
```

2. Configure database and SMS API credentials in `config.php`.

3. Place the project files in your PHP server root.

4. Access `login.php` to start using the system.

## Next Steps

- Implement remaining modules: cleaning staff, timetables, ratings, gym classes, gym areas, machine maintenance, reports.
- Enhance dashboard with real data.
- Add user management for admin.
- Integrate real SMS API credentials.

## Technologies Used

- PHP 7+
- MySQLi
- Tailwind CSS (via CDN)
- Chart.js (via CDN)

## Notes

- SMS sending uses textsms.co.ke API; replace API key in `config.php`.
- Passwords are hashed using PHP's `password_hash`.
- Sessions are used for authentication.
