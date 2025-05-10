<?php
require_once 'auth.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-black">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Gym Supervision System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #000;
            color: #fff;
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
    </style>
</head>
<body class="h-full flex">
    <aside class="w-64 bg-gray-900 flex flex-col min-h-screen p-6">
        <h1 class="text-2xl font-bold mb-8">Gym Supervision</h1>
        <nav class="flex flex-col space-y-4 text-gray-300">
            <a href="index.php" class="hover:text-white font-semibold">Dashboard</a>
            <a href="cleanliness_areas.php" class="hover:text-white">Cleanliness Areas</a>
            <a href="cleanliness_shifts.php" class="hover:text-white">Cleanliness Shifts</a>
            <a href="cleaning_staff.php" class="hover:text-white">Cleaning Staff</a>
            <a href="cleanliness_timetable.php" class="hover:text-white">Cleanliness Timetable</a>
            <a href="cleanliness_ratings.php" class="hover:text-white">Cleanliness Ratings</a>
            <a href="coaches.php" class="hover:text-white">Coaches</a>
            <a href="gym_classes.php" class="hover:text-white">Gym Classes</a>
            <a href="class_attendance.php" class="hover:text-white">Class Attendance</a>
            <a href="coach_ratings.php" class="hover:text-white">Coach Ratings</a>
            <a href="gym_areas.php" class="hover:text-white">Gym Areas</a>
            <a href="gym_area_timetable.php" class="hover:text-white">Gym Area Timetable</a>
            <a href="machine_categories.php" class="hover:text-white">Machine Categories</a>
            <a href="machines.php" class="hover:text-white">Machines</a>
            <a href="machine_status_logs.php" class="hover:text-white">Machine Status Logs</a>
            <a href="logout.php" class="hover:text-red-500 mt-8 font-semibold">Logout</a>
        </nav>
    </aside>
    <main class="flex-grow p-8 overflow-auto">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-4xl font-bold">Dashboard</h2>
            <div class="text-lg">Welcome, <span class="font-semibold"><?php echo htmlspecialchars($username); ?></span></div>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-semibold mb-4">Cleanliness Reports</h3>
                <canvas id="cleanlinessChart"></canvas>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-semibold mb-4">Class Attendance</h3>
                <canvas id="attendanceChart"></canvas>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-semibold mb-4">Machine Status</h3>
                <canvas id="machineStatusChart"></canvas>
            </div>
        </section>
    </main>

    <script>
        // Sample data for charts - replace with dynamic data from backend
        const cleanlinessData = {
            labels: ['Area 1', 'Area 2', 'Area 3', 'Area 4'],
            datasets: [{
                label: 'Cleanliness Ratings',
                data: [4, 3, 5, 4],
                backgroundColor: 'rgba(255, 255, 255, 0.7)'
            }]
        };

        const attendanceData = {
            labels: ['Yoga', 'Zumba', 'Pilates', 'Crossfit'],
            datasets: [{
                label: 'Attendance',
                data: [20, 15, 10, 25],
                backgroundColor: 'rgba(255, 255, 255, 0.7)'
            }]
        };

        const machineStatusData = {
            labels: ['Good Condition', 'Under Maintenance', 'Fault'],
            datasets: [{
                label: 'Machines',
                data: [10, 3, 2],
                backgroundColor: [
                    'rgba(0, 255, 0, 0.7)',
                    'rgba(255, 165, 0, 0.7)',
                    'rgba(255, 0, 0, 0.7)'
                ]
            }]
        };

        const configCleanliness = {
            type: 'bar',
            data: cleanlinessData,
            options: {
                scales: {
                    y: { beginAtZero: true, max: 5 }
                },
                plugins: {
                    legend: { labels: { color: '#fff' } }
                }
            }
        };

        const configAttendance = {
            type: 'bar',
            data: attendanceData,
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { labels: { color: '#fff' } }
                }
            }
        };

        const configMachineStatus = {
            type: 'doughnut',
            data: machineStatusData,
            options: {
                plugins: {
                    legend: { labels: { color: '#fff' } }
                }
            }
        };

        new Chart(document.getElementById('cleanlinessChart'), configCleanliness);
        new Chart(document.getElementById('attendanceChart'), configAttendance);
        new Chart(document.getElementById('machineStatusChart'), configMachineStatus);
    </script>
</body>
</html>
