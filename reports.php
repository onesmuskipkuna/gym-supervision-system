<?php
require_once 'auth.php';
require_once 'layout.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

require_once 'config.php';

render_header("Reports - Gym Supervision System");
render_nav("reports");

// Fetch reports data

// Cleanliness Areas Report - count of ratings per area
$cleanlinessReport = [];
$sql = "SELECT ca.area_name, COUNT(cr.id) AS rating_count, AVG(cr.rating) AS avg_rating
        FROM cleanliness_areas ca
        LEFT JOIN cleanliness_timetable ct ON ca.id = ct.area_id
        LEFT JOIN cleanliness_ratings cr ON ct.id = cr.timetable_id
        GROUP BY ca.area_name
        ORDER BY ca.area_name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cleanlinessReport[] = $row;
    }
}

// Gym Areas Report - count of timetable entries per area
$gymAreaReport = [];
$sql = "SELECT ga.area_name, COUNT(gt.id) AS timetable_count
        FROM gym_areas ga
        LEFT JOIN gym_area_timetable gt ON ga.id = gt.area_id
        GROUP BY ga.area_name
        ORDER BY ga.area_name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gymAreaReport[] = $row;
    }
}

// Classes Report - count of attendance per class
$classesReport = [];
$sql = "SELECT gc.class_name, COUNT(ca.id) AS attendance_count
        FROM gym_classes gc
        LEFT JOIN class_attendance ca ON gc.id = ca.class_id
        GROUP BY gc.class_name
        ORDER BY gc.class_name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classesReport[] = $row;
    }
}

// Machines Report - count of machines by status
$machinesReport = [
    'Good Condition' => 0,
    'Under Maintenance' => 0,
    'Fault' => 0
];
$sql = "SELECT status, COUNT(*) AS count FROM machines GROUP BY status";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'];
        $count = intval($row['count']);
        if ($status === 'good condition') {
            $machinesReport['Good Condition'] = $count;
        } elseif ($status === 'under maintenance') {
            $machinesReport['Under Maintenance'] = $count;
        } elseif ($status === 'fault') {
            $machinesReport['Fault'] = $count;
        }
    }
}
?>

<main class="flex-grow p-6 overflow-auto bg-white rounded-lg shadow max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Reports</h2>

    <section class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Cleanliness Areas Report</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 text-left">Area Name</th>
                    <th class="border border-gray-300 p-2 text-right">Number of Ratings</th>
                    <th class="border border-gray-300 p-2 text-right">Average Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cleanlinessReport as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($row['area_name']); ?></td>
                        <td class="border border-gray-300 p-2 text-right"><?php echo intval($row['rating_count']); ?></td>
                        <td class="border border-gray-300 p-2 text-right"><?php echo $row['avg_rating'] !== null ? number_format($row['avg_rating'], 2) : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($cleanlinessReport)): ?>
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Gym Areas Report</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 text-left">Area Name</th>
                    <th class="border border-gray-300 p-2 text-right">Number of Timetable Entries</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gymAreaReport as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($row['area_name']); ?></td>
                        <td class="border border-gray-300 p-2 text-right"><?php echo intval($row['timetable_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($gymAreaReport)): ?>
                    <tr>
                        <td colspan="2" class="p-4 text-center text-gray-500">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Classes Report</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 text-left">Class Name</th>
                    <th class="border border-gray-300 p-2 text-right">Attendance Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classesReport as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td class="border border-gray-300 p-2 text-right"><?php echo intval($row['attendance_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($classesReport)): ?>
                    <tr>
                        <td colspan="2" class="p-4 text-center text-gray-500">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h3 class="text-xl font-semibold mb-4">Machines Report</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 text-left">Status</th>
                    <th class="border border-gray-300 p-2 text-right">Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($machinesReport as $status => $count): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($status); ?></td>
                        <td class="border border-gray-300 p-2 text-right"><?php echo intval($count); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($machinesReport)): ?>
                    <tr>
                        <td colspan="2" class="p-4 text-center text-gray-500">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php
render_footer();
?>
