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
<body class="h-full flex bg-black text-white font-sans">
<?php
}

function render_nav($active = "dashboard") {
?>
<aside class="w-64 bg-gray-900 flex flex-col min-h-screen p-6 shadow-lg">
    <h1 class="text-2xl font-bold mb-8 text-white">Gym Supervision</h1>
    <nav class="flex flex-col space-y-2 text-gray-300">
        <a href="index.php" class="<?php echo $active === 'dashboard' ? 'text-white font-semibold' : 'hover:text-white'; ?> mb-4">Dashboard</a>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" onclick="toggleMenu('cleanlinessMenu')">
                Cleanliness
                <svg id="cleanlinessIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="cleanlinessMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1">
                <a href="cleanliness_areas.php" class="hover:text-white">Areas</a>
                <a href="cleanliness_shifts.php" class="hover:text-white">Shifts</a>
                <a href="cleaning_staff.php" class="hover:text-white">Staff</a>
                <a href="cleanliness_timetable.php" class="hover:text-white">Timetable</a>
                <a href="cleanliness_ratings.php" class="hover:text-white">Ratings</a>
            </div>
        </div>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" onclick="toggleMenu('classesMenu')">
                Classes
                <svg id="classesIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="classesMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1">
                <a href="coaches.php" class="hover:text-white">Coaches</a>
                <a href="gym_classes.php" class="hover:text-white">Gym Classes</a>
                <a href="class_attendance.php" class="hover:text-white">Class Attendance</a>
                <a href="coach_ratings.php" class="hover:text-white">Coach Ratings</a>
                <a href="admin_users.php" class="hover:text-white">Admin Users</a>
            </div>
        </div>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" onclick="toggleMenu('gymAreaMenu')">
                Gym Area
                <svg id="gymAreaIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="gymAreaMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1">
                <a href="gym_areas.php" class="hover:text-white">Areas</a>
                <a href="gym_area_timetable.php" class="hover:text-white">Timetable</a>
            </div>
        </div>

        <div>
            <button type="button" class="w-full text-left hover:text-white font-semibold flex justify-between items-center" onclick="toggleMenu('machinesMenu')">
                Machines
                <svg id="machinesIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="machinesMenu" class="ml-4 mt-2 hidden flex flex-col space-y-1">
                <a href="machine_categories.php" class="hover:text-white">Categories</a>
                <a href="machines.php" class="hover:text-white">Machines</a>
                <a href="machine_status_logs.php" class="hover:text-white">Status Logs</a>
            </div>
        </div>

        <a href="reports.php" class="hover:text-white font-semibold mt-4">Reports</a>

        <a href="logout.php" class="hover:text-red-600 mt-8 font-semibold">Logout</a>
    </nav>
</aside>

<script>
    function toggleMenu(menuId) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(menuId + 'Icon');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
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
