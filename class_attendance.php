<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch gym classes for dropdown
$classes = [];
$result = $conn->query("SELECT id, class_name, class_date FROM gym_classes ORDER BY class_date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Fetch users for dropdown
$users = [];
$result_users = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
if ($result_users) {
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle form submission for adding attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'], $_POST['user_id'], $_POST['attendance_date'])) {
    $class_id = intval($_POST['class_id']);
    $user_id = intval($_POST['user_id']);
    $attendance_date = $_POST['attendance_date'];

    if ($class_id <= 0 || $user_id <= 0 || $attendance_date === '') {
        $error = 'All fields are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO class_attendance (class_id, user_id, attendance_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $class_id, $user_id, $attendance_date);
        if ($stmt->execute()) {
            $success = 'Attendance recorded successfully.';
        } else {
            $error = 'Error recording attendance: ' . $conn->error;
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM class_attendance WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Attendance record deleted successfully.';
    } else {
        $error = 'Error deleting attendance record: ' . $conn->error;
    }
}

// Fetch attendance records with joins for display
$sql = "SELECT ca.id, gc.class_name, u.username, ca.attendance_date
        FROM class_attendance ca
        JOIN gym_classes gc ON ca.class_id = gc.id
        JOIN users u ON ca.user_id = u.id
        ORDER BY ca.attendance_date DESC";

$result = $conn->query($sql);
$attendances = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $attendances[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Class Attendance - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Class Attendance</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="class_attendance.php" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="class_id" class="block mb-2 font-semibold">Class</label>
                <select id="class_id" name="class_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name'] . ' (' . $class['class_date'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="user_id" class="block mb-2 font-semibold">User</label>
                <select id="user_id" name="user_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="attendance_date" class="block mb-2 font-semibold">Date</label>
                <input type="date" id="attendance_date" name="attendance_date" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Add Attendance</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Class</th>
                    <th class="p-3 border border-gray-700">User</th>
                    <th class="p-3 border border-gray-700">Date</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendances as $attendance): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $attendance['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($attendance['class_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($attendance['username']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($attendance['attendance_date']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="class_attendance.php?delete=<?php echo $attendance['id']; ?>" onclick="return confirm('Are you sure you want to delete this attendance record?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($attendances)): ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-400">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
