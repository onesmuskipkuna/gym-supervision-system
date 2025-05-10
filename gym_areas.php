<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission for adding new gym area
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_name'])) {
    $area_name = trim($_POST['area_name']);
    if ($area_name === '') {
        $error = 'Area name cannot be empty.';
    } else {
        $stmt = $conn->prepare("INSERT INTO gym_areas (area_name) VALUES (?)");
        $stmt->bind_param("s", $area_name);
        if ($stmt->execute()) {
            $success = 'Gym area added successfully.';
        } else {
            $error = 'Error adding gym area: ' . $conn->error;
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM gym_areas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Gym area deleted successfully.';
    } else {
        $error = 'Error deleting gym area: ' . $conn->error;
    }
}

// Fetch all gym areas
$result = $conn->query("SELECT * FROM gym_areas ORDER BY area_name ASC");
$areas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $areas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gym Areas - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Gym Areas</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="gym_areas.php" class="mb-6 flex gap-4">
            <input type="text" name="area_name" placeholder="New gym area name" required class="flex-grow p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            <button type="submit" class="bg-white text-black font-bold px-6 rounded hover:bg-gray-300 transition">Add Area</button>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Area Name</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($areas as $area): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $area['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($area['area_name']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="gym_areas.php?delete=<?php echo $area['id']; ?>" onclick="return confirm('Are you sure you want to delete this gym area?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($areas)): ?>
                    <tr>
                        <td colspan="3" class="p-3 text-center text-gray-400">No gym areas found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
