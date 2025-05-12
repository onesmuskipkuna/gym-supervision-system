<?php
// layout.php - Common layout template for Gym Supervision System

function render_header($title = "Gym Supervision System") {
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-black">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #000000;
            color: #ffffff;
        }
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #4b5563;
            border-radius: 4px;
        }
        a {
            transition: color 0.3s ease;
        }
        a:hover {
            color: #3b82f6; /* Tailwind blue-500 */
        }
        button {
            transition: color 0.3s ease, transform 0.3s ease;
        }
        button:hover {
            color: #3b82f6;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="h-full flex bg-black text-white font-sans" x-data>
<?php
}

function render_nav($active = "dashboard") {
?>
<body class="h-full flex bg-black text-white font-sans">
<header class="bg-gray-900 text-white flex items-center justify-between p-4 shadow-lg md:hidden">
    <div class="text-xl font-bold">Gym Supervision</div>
    <button id="mobileMenuButton" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobileMenu" class="focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</header>
<aside id="sidebar" class="w-64 bg-gray-900 flex flex-col min-h-screen p-6 shadow-lg hidden md:flex">
    <h1 class="text-2xl font-bold mb-8 text-white">Gym Supervision</h1>
    <nav class="flex flex-col space-y-2 text-gray-300" role="navigation" aria-label="Main navigation">
        <a href="index.php" class="<?php echo $active === 'dashboard' ? 'text-white font-semibold' : 'hover:text-white'; ?> mb-4" aria-current="<?php echo $active === 'dashboard' ? 'page' : 'false'; ?>">Dashboard</a>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" aria-expanded="false" aria-controls="cleanlinessMenu" onclick="toggleMenu('cleanlinessMenu', this)">
                Cleanliness
                <svg id="cleanlinessIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="cleanlinessMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1" role="region" aria-label="Cleanliness submenu">
                <a href="cleanliness_areas.php" class="hover:text-white" tabindex="-1">Areas</a>
                <a href="cleanliness_shifts.php" class="hover:text-white" tabindex="-1">Shifts</a>
                <a href="cleaning_staff.php" class="hover:text-white" tabindex="-1">Staff</a>
                <a href="cleanliness_timetable.php" class="hover:text-white" tabindex="-1">Timetable</a>
                <a href="cleanliness_ratings.php" class="hover:text-white" tabindex="-1">Ratings</a>
            </div>
        </div>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" aria-expanded="false" aria-controls="classesMenu" onclick="toggleMenu('classesMenu', this)">
                Classes
                <svg id="classesIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="classesMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1" role="region" aria-label="Classes submenu">
                <a href="coaches.php" class="hover:text-white" tabindex="-1">Coaches</a>
                <a href="gym_classes.php" class="hover:text-white" tabindex="-1">Gym Classes</a>
                <a href="class_attendance.php" class="hover:text-white" tabindex="-1">Class Attendance</a>
                <a href="coach_ratings.php" class="hover:text-white" tabindex="-1">Coach Ratings</a>
                <a href="admin_users.php" class="hover:text-white" tabindex="-1">Admin Users</a>
            </div>
        </div>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" aria-expanded="false" aria-controls="gymAreaMenu" onclick="toggleMenu('gymAreaMenu', this)">
                Gym Area
                <svg id="gymAreaIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="gymAreaMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1" role="region" aria-label="Gym Area submenu">
                <a href="gym_areas.php" class="hover:text-white" tabindex="-1">Areas</a>
                <a href="gym_area_timetable.php" class="hover:text-white" tabindex="-1">Timetable</a>
            </div>
        </div>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" aria-expanded="false" aria-controls="machinesMenu" onclick="toggleMenu('machinesMenu', this)">
                Machines
                <svg id="machinesIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="machinesMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1" role="region" aria-label="Machines submenu">
                <a href="machine_categories.php" class="hover:text-white" tabindex="-1">Categories</a>
                <a href="machines.php" class="hover:text-white" tabindex="-1">Machines</a>
                <a href="machine_status_logs.php" class="hover:text-white" tabindex="-1">Status Logs</a>
            </div>
        </div>

        <a href="reports.php" class="hover:text-white font-semibold mt-4" tabindex="0">Reports</a>

        <a href="logout.php" class="hover:text-red-600 mt-8 font-semibold" tabindex="0">Logout</a>
    </nav>
</aside>

<script>
    function toggleMenu(menuId, button) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(menuId + 'Icon');
        const expanded = button.getAttribute('aria-expanded') === 'true';
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180');
            button.setAttribute('aria-expanded', 'true');
            // Set focus to first submenu item
            const firstSubItem = menu.querySelector('a, button');
            if (firstSubItem) firstSubItem.focus();
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180');
            button.setAttribute('aria-expanded', 'false');
        }
    }

    // Responsive mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const sidebar = document.getElementById('sidebar');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => {
            const isHidden = sidebar.classList.contains('hidden');
            if (isHidden) {
                sidebar.classList.remove('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'true');
            } else {
                sidebar.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
</script>

<?php
}

function render_footer() {
?>
</body>
</html>
<?php
}
?>
