<?php
require_once 'auth.php';
require_once 'layout.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

require_once 'config.php'; // DB config

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch average cleanliness ratings grouped by area
$cleanlinessRatings = [];
$sql = "SELECT ca.area_name, AVG(cr.rating) as avg_rating
        FROM cleanliness_ratings cr
        JOIN cleanliness_timetable ct ON cr.timetable_id = ct.id
        JOIN cleanliness_areas ca ON ct.area_id = ca.id
        GROUP BY ca.area_name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cleanlinessRatings[$row['area_name']] = round(floatval($row['avg_rating']), 2);
    }
}

// Fetch attendance counts grouped by gym class
$attendanceCounts = [];
$sql = "SELECT gc.class_name, COUNT(ca.id) as attendance_count
        FROM class_attendance ca
        JOIN gym_classes gc ON ca.class_id = gc.id
        GROUP BY gc.class_name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $attendanceCounts[$row['class_name']] = intval($row['attendance_count']);
    }
}

// Fetch machine counts grouped by status
$machineStatusCounts = ['good condition' => 0, 'under maintenance' => 0, 'fault' => 0];
$sql = "SELECT status, COUNT(*) as count FROM machines GROUP BY status";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $machineStatusCounts[$row['status']] = intval($row['count']);
    }
}

// Fetch recent activities from machine_status_logs (last 5)
$recentActivities = [];
$sql = "SELECT m.machine_name, msl.status, msl.remarks, u.username, msl.changed_at
        FROM machine_status_logs msl
        JOIN machines m ON msl.machine_id = m.id
        JOIN users u ON msl.changed_by = u.id
        ORDER BY msl.changed_at DESC
        LIMIT 5";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentActivities[] = sprintf(
            "Machine %s marked as %s by %s at %s. Remarks: %s",
            htmlspecialchars($row['machine_name']),
            htmlspecialchars($row['status']),
            htmlspecialchars($row['username']),
            $row['changed_at'],
            htmlspecialchars($row['remarks'])
        );
    }
}

$conn->close();

render_header("Dashboard - Gym Supervision System");
render_nav("dashboard");
?>

<main class="flex-grow p-6 overflow-auto bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <section class="bg-white text-gray-900 rounded-lg p-4 shadow">
            <h3 class="text-lg font-semibold mb-4">Cleanliness Ratings</h3>
            <canvas id="cleanlinessChart" class="w-full h-48"></canvas>
        </section>
        <section class="bg-white text-gray-900 rounded-lg p-4 shadow">
            <h3 class="text-lg font-semibold mb-4">Class Attendance</h3>
            <canvas id="attendanceChart" class="w-full h-48"></canvas>
        </section>
        <section class="bg-white text-gray-900 rounded-lg p-4 shadow">
            <h3 class="text-lg font-semibold mb-4">Machine Status</h3>
            <canvas id="machineStatusChart" class="w-full h-48"></canvas>
        </section>
    </div>

    <section class="bg-white text-gray-900 rounded-lg p-4 shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
        <ul class="list-disc list-inside space-y-1">
            <?php
            if (count($recentActivities) > 0) {
                foreach ($recentActivities as $activity) {
                    echo "<li>" . $activity . "</li>";
                }
            } else {
                echo "<li>No recent activities found.</li>";
            }
            ?>
        </ul>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const cleanlinessData = {
        labels: <?php echo json_encode(array_keys($cleanlinessRatings)); ?>,
        datasets: [{
            label: 'Cleanliness Ratings',
            data: <?php echo json_encode(array_values($cleanlinessRatings)); ?>,
            backgroundColor: 'rgba(37, 99, 235, 0.7)'
        }]
    };

    const attendanceData = {
        labels: <?php echo json_encode(array_keys($attendanceCounts)); ?>,
        datasets: [{
            label: 'Attendance',
            data: <?php echo json_encode(array_values($attendanceCounts)); ?>,
            backgroundColor: 'rgba(245, 158, 11, 0.7)'
        }]
    };

    const machineStatusData = {
        labels: <?php echo json_encode(array_keys($machineStatusCounts)); ?>,
        datasets: [{
            label: 'Machines',
            data: <?php echo json_encode(array_values($machineStatusCounts)); ?>,
            backgroundColor: [
                'rgba(16, 185, 129, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(107, 114, 128, 0.7)'
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
                legend: { labels: { color: '#111827' } }
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
                legend: { labels: { color: '#111827' } }
            }
        }
    };

    const configMachineStatus = {
        type: 'doughnut',
        data: machineStatusData,
        options: {
            plugins: {
                legend: { labels: { color: '#111827' } }
            }
        }
    };

    new Chart(document.getElementById('cleanlinessChart'), configCleanliness);
    new Chart(document.getElementById('attendanceChart'), configAttendance);
    new Chart(document.getElementById('machineStatusChart'), configMachineStatus);
</script>

<?php
render_footer();
?>
