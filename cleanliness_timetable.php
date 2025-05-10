<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch areas, shifts, and staff for form dropdowns
$areas = [];
$shifts = [];
$staffs = [];

$area_result = $conn->query("SELECT id, area_name FROM cleanliness_areas ORDER BY area_name ASC");
if ($area_result) {
    while ($row = $area_result->fetch_assoc()) {
        $areas[] = $row;
    }
}

$shift_result = $conn->query("SELECT id, shift_name FROM cleanliness_shifts ORDER BY start_time ASC");
if ($shift_result) {
    while ($row = $shift_result->fetch_assoc()) {
        $shifts[] = $row;
    }
}

$staff_result = $conn->query("SELECT id, name FROM cleaning_staff ORDER BY name ASC");
if ($staff_result) {
    while ($row = $staff_result->fetch_assoc()) {
        $staffs[] = $row;
    }
}

// Handle form submission for adding new timetable entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_id'], $_POST['shift_id'], $_POST['staff_id'], $_POST['cleaning_date'])) {
    $area_id = intval($_POST['area_id']);
    $shift_id = intval($_POST['shift_id']);
    $staff_id = intval($_POST['staff_id']);
    $cleaning_date = $_POST['cleaning_date'];

    if ($area_id <= 0 || $shift_id <= 0 || $staff_id <= 0 || $cleaning_date === '') {
        $error = 'All fields are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO cleanliness_timetable (area_id, shift_id, staff_id, cleaning_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $area_id, $shift_id, $staff_id, $cleaning_date);
        if ($stmt->execute()) {
            $success = 'Cleanliness timetable entry added successfully.';
        } else {
            $error = 'Error adding timetable entry: ' . $conn->error;
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM cleanliness_timetable WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Cleanliness timetable entry deleted successfully.';
    } else {
        $error = 'Error deleting timetable entry: ' . $conn->error;
    }
}

// Fetch timetable entries with joins for display
$sql = "SELECT ct.id, ca.area_name, cs.shift_name, cs.start_time, cs.end_time, cst.name AS staff_name, ct.cleaning_date
        FROM cleanliness_timetable ct
        JOIN cleanliness_areas ca ON ct.area_id = ca.id
        JOIN cleanliness_shifts cs ON ct.shift_id = cs.id
        JOIN cleaning_staff cst ON ct.staff_id = cst.id
        ORDER BY ct.cleaning_date DESC, cs.start_time ASC";

$result = $conn->query($sql);
$timetables = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $timetables[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cleanliness Timetable - Gym Supervision System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #000;
            color: #fff;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="bg-gray-900 p-4 flex justify-between items-center">
        <div class="text-xl font-bold">Gym Supervision System</div>
        <div>
            <a href="index.php" class="mr-4 hover:underline">Dashboard</a>
            <a href="logout.php" class="bg-white text-black px-3 py-1 rounded hover:bg-gray-300 transition">Logout</a>
        </div>
    </nav>

    <main class="flex-grow p-6 max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Cleanliness Timetable</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="cleanliness_timetable.php" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label for="area_id" class="block mb-2 font-semibold">Area</label>
                <select id="area_id" name="area_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Area</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['area_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="shift_id" class="block mb-2 font-semibold">Shift</label>
                <select id="shift_id" name="shift_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Shift</option>
                    <?php foreach ($shifts as $shift): ?>
                        <option value="<?php echo $shift['id']; ?>"><?php echo htmlspecialchars($shift['shift_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="staff_id" class="block mb-2 font-semibold">Staff</label>
                <select id="staff_id" name="staff_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Staff</option>
                    <?php foreach ($staffs as $staff): ?>
                        <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="cleaning_date" class="block mb-2 font-semibold">Date</label>
                <input type="date" id="cleaning_date" name="cleaning_date" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Add Entry</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Area</th>
                    <th class="p-3 border border-gray-700">Shift</th>
                    <th class="p-3 border border-gray-700">Shift Time</th>
                    <th class="p-3 border border-gray-700">Staff</th>
                    <th class="p-3 border border-gray-700">Date</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timetables as $entry): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $entry['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($entry['area_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($entry['shift_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($entry['start_time']) . ' - ' . htmlspecialchars($entry['end_time']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($entry['staff_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($entry['cleaning_date']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="cleanliness_timetable.php?delete=<?php echo $entry['id']; ?>" onclick="return confirm('Are you sure you want to delete this entry?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($timetables)): ?>
                    <tr>
                        <td colspan="7" class="p-3 text-center text-gray-400">No timetable entries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
