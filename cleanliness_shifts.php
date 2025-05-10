<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission for adding new shift
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shift_name'], $_POST['start_time'], $_POST['end_time'])) {
    $shift_name = trim($_POST['shift_name']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if ($shift_name === '') {
        $error = 'Shift name cannot be empty.';
    } elseif ($start_time === '' || $end_time === '') {
        $error = 'Start time and end time are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO cleanliness_shifts (shift_name, start_time, end_time) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $shift_name, $start_time, $end_time);
        if ($stmt->execute()) {
            $success = 'Cleanliness shift added successfully.';
        } else {
            $error = 'Error adding shift: ' . $conn->error;
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM cleanliness_shifts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Cleanliness shift deleted successfully.';
    } else {
        $error = 'Error deleting shift: ' . $conn->error;
    }
}

// Fetch all shifts
$result = $conn->query("SELECT * FROM cleanliness_shifts ORDER BY start_time ASC");
$shifts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $shifts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cleanliness Shifts - Gym Supervision System</title>
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

    <main class="flex-grow p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Cleanliness Shifts</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="cleanliness_shifts.php" class="mb-6 space-y-4 max-w-md">
            <div>
                <label for="shift_name" class="block mb-2 font-semibold">Shift Name</label>
                <input type="text" id="shift_name" name="shift_name" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="start_time" class="block mb-2 font-semibold">Start Time</label>
                <input type="time" id="start_time" name="start_time" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="end_time" class="block mb-2 font-semibold">End Time</label>
                <input type="time" id="end_time" name="end_time" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <button type="submit" class="bg-white text-black font-bold px-6 py-3 rounded hover:bg-gray-300 transition">Add Shift</button>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Shift Name</th>
                    <th class="p-3 border border-gray-700">Start Time</th>
                    <th class="p-3 border border-gray-700">End Time</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shifts as $shift): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $shift['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($shift['shift_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($shift['start_time']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($shift['end_time']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="cleanliness_shifts.php?delete=<?php echo $shift['id']; ?>" onclick="return confirm('Are you sure you want to delete this shift?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($shifts)): ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-400">No cleanliness shifts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
