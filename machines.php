<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch machine categories for dropdown
$categories = [];
$result = $conn->query("SELECT id, category_name FROM machine_categories ORDER BY category_name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Handle form submission for adding new machine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['machine_name'], $_POST['category_id'], $_POST['location'], $_POST['status'])) {
    $machine_name = trim($_POST['machine_name']);
    $category_id = intval($_POST['category_id']);
    $location = $_POST['location'];
    $status = $_POST['status'];

    if ($machine_name === '' || $location === '' || $status === '') {
        $error = 'All fields are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO machines (machine_name, category_id, location, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $machine_name, $category_id, $location, $status);
        if ($stmt->execute()) {
            $success = 'Machine added successfully.';
        } else {
            $error = 'Error adding machine: ' . $conn->error;
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM machines WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Machine deleted successfully.';
    } else {
        $error = 'Error deleting machine: ' . $conn->error;
    }
}

// Fetch all machines with category names
$sql = "SELECT m.id, m.machine_name, mc.category_name, m.location, m.status
        FROM machines m
        JOIN machine_categories mc ON m.category_id = mc.id
        ORDER BY m.machine_name ASC";

$result = $conn->query($sql);
$machines = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $machines[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Machines - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Machines</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="machines.php" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="machine_name" class="block mb-2 font-semibold">Machine Name</label>
                <input type="text" id="machine_name" name="machine_name" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="category_id" class="block mb-2 font-semibold">Category</label>
                <select id="category_id" name="category_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="location" class="block mb-2 font-semibold">Location</label>
                <select id="location" name="location" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Location</option>
                    <option value="main gym">Main Gym</option>
                    <option value="ladies gym">Ladies Gym</option>
                </select>
            </div>
            <div>
                <label for="status" class="block mb-2 font-semibold">Status</label>
                <select id="status" name="status" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Status</option>
                    <option value="good condition">Good Condition</option>
                    <option value="under maintenance">Under Maintenance</option>
                    <option value="fault">Fault</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Add Machine</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Machine Name</th>
                    <th class="p-3 border border-gray-700">Category</th>
                    <th class="p-3 border border-gray-700">Location</th>
                    <th class="p-3 border border-gray-700">Status</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($machines as $machine): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $machine['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($machine['machine_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($machine['category_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($machine['location']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($machine['status']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="machines.php?delete=<?php echo $machine['id']; ?>" onclick="return confirm('Are you sure you want to delete this machine?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($machines)): ?>
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-400">No machines found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
