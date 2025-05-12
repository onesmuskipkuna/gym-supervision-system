<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['phone'], $_POST['email'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if ($name === '') {
        $error = 'Coach name cannot be empty.';
    } else {
        $stmt = $conn->prepare("INSERT INTO coaches (name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $email);
        if ($stmt->execute()) {
            $success = 'Coach added successfully.';
        } else {
            $error = 'Error adding coach: ' . $conn->error;
        }
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM coaches WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Coach deleted successfully.';
    } else {
        $error = 'Error deleting coach: ' . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM coaches ORDER BY name ASC");
$coaches = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $coaches[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Coaches - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Coaches</h1>

        <?php if (!empty($success)): ?>
            <div class="mb-4 p-3 rounded bg-green-600 text-white"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 rounded bg-red-600 text-white"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="coaches.php" class="mb-6 space-y-4 max-w-md">
            <div>
                <label for="name" class="block mb-2 font-semibold">Name</label>
                <input type="text" id="name" name="name" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="phone" class="block mb-2 font-semibold">Phone</label>
                <input type="text" id="phone" name="phone" class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="email" class="block mb-2 font-semibold">Email</label>
                <input type="email" id="email" name="email" class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <button type="submit" class="bg-white text-black font-bold px-6 py-3 rounded hover:bg-gray-300 transition">Add Coach</button>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Name</th>
                    <th class="p-3 border border-gray-700">Phone</th>
                    <th class="p-3 border border-gray-700">Email</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($coaches)): ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-400">No coaches found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($coaches as $coach): ?>
                        <tr data-id="<?php echo $coach['id']; ?>" class="border border-gray-700 hover:bg-gray-700">
                            <td class="p-3 border border-gray-700"><?php echo $coach['id']; ?></td>
                            <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($coach['name']); ?></td>
                            <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($coach['phone']); ?></td>
                            <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($coach['email']); ?></td>
                            <td class="p-3 border border-gray-700">
                                <a href="coaches.php?delete=<?php echo $coach['id']; ?>" onclick="return confirm('Are you sure you want to delete this coach?');" class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
