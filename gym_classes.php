<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch coaches for dropdown
$coaches = [];
$result = $conn->query("SELECT id, name FROM coaches ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $coaches[] = $row;
    }
}

// Handle form submission for adding new class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_name'], $_POST['coach_id'], $_POST['studio_location'], $_POST['class_date'], $_POST['start_time'], $_POST['end_time'])) {
    $class_name = trim($_POST['class_name']);
    $coach_id = intval($_POST['coach_id']);
    $studio_location = trim($_POST['studio_location']);
    $class_date = $_POST['class_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if ($class_name === '' || $studio_location === '' || $class_date === '' || $start_time === '' || $end_time === '') {
        $error = 'All fields are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO gym_classes (class_name, coach_id, studio_location, class_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $class_name, $coach_id, $studio_location, $class_date, $start_time, $end_time);
        if ($stmt->execute()) {
            $success = 'Gym class added successfully.';
        } else {
            $error = 'Error adding class: ' . $conn->error;
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM gym_classes WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Gym class deleted successfully.';
    } else {
        $error = 'Error deleting class: ' . $conn->error;
    }
}

// Fetch all classes with coach name
$sql = "SELECT gc.id, gc.class_name, c.name AS coach_name, gc.studio_location, gc.class_date, gc.start_time, gc.end_time
        FROM gym_classes gc
        JOIN coaches c ON gc.coach_id = c.id
        ORDER BY gc.class_date DESC, gc.start_time ASC";

$result = $conn->query($sql);
$classes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gym Classes - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Gym Classes</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="gym_classes.php" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="class_name" class="block mb-2 font-semibold">Class Name</label>
                <input type="text" id="class_name" name="class_name" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="coach_id" class="block mb-2 font-semibold">Coach</label>
                <select id="coach_id" name="coach_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Coach</option>
                    <?php foreach ($coaches as $coach): ?>
                        <option value="<?php echo $coach['id']; ?>"><?php echo htmlspecialchars($coach['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="studio_location" class="block mb-2 font-semibold">Studio Location</label>
                <select id="studio_location" name="studio_location" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Studio Location</option>
                    <option value="Roof Top Studio">Roof Top Studio</option>
                    <option value="Ladies Studio">Ladies Studio</option>
                </select>
            </div>
            <div>
                <label for="class_date" class="block mb-2 font-semibold">Class Date</label>
                <select id="class_date" name="class_date" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>
            <div>
                <label for="start_time" class="block mb-2 font-semibold">Start Time</label>
                <input type="time" id="start_time" name="start_time" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="end_time" class="block mb-2 font-semibold">End Time</label>
                <input type="time" id="end_time" name="end_time" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Add Class</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Class Name</th>
                    <th class="p-3 border border-gray-700">Coach</th>
                    <th class="p-3 border border-gray-700">Studio Location</th>
                    <th class="p-3 border border-gray-700">Date</th>
                    <th class="p-3 border border-gray-700">Start Time</th>
                    <th class="p-3 border border-gray-700">End Time</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $class['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($class['class_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($class['coach_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($class['studio_location']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($class['class_date']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($class['start_time']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($class['end_time']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="gym_classes.php?delete=<?php echo $class['id']; ?>" onclick="return confirm('Are you sure you want to delete this class?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="8" class="p-3 text-center text-gray-400">No gym classes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
