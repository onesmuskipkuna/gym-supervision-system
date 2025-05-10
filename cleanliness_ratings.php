<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch timetable entries for rating
$timetables = [];
$sql = "SELECT ct.id, ca.area_name, cs.shift_name, cs.start_time, cs.end_time, cst.name AS staff_name, ct.cleaning_date
        FROM cleanliness_timetable ct
        JOIN cleanliness_areas ca ON ct.area_id = ca.id
        JOIN cleanliness_shifts cs ON ct.shift_id = cs.id
        JOIN cleaning_staff cst ON ct.staff_id = cst.id
        ORDER BY ct.cleaning_date DESC, cs.start_time ASC";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $timetables[] = $row;
    }
}

// Handle form submission for adding rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timetable_id'], $_POST['rating'])) {
    $timetable_id = intval($_POST['timetable_id']);
    $rating = intval($_POST['rating']);
    $comments = trim($_POST['comments']);

    if ($timetable_id <= 0 || $rating < 1 || $rating > 5) {
        $error = 'Invalid timetable entry or rating.';
    } else {
        $stmt = $conn->prepare("INSERT INTO cleanliness_ratings (timetable_id, rating, comments) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $timetable_id, $rating, $comments);
        if ($stmt->execute()) {
            $success = 'Rating added successfully.';
        } else {
            $error = 'Error adding rating: ' . $conn->error;
        }
    }
}

// Fetch all ratings with joins for display
$sql_ratings = "SELECT cr.id, ca.area_name, cs.shift_name, cst.name AS staff_name, cr.rating, cr.comments, cr.rating_date
                FROM cleanliness_ratings cr
                JOIN cleanliness_timetable ct ON cr.timetable_id = ct.id
                JOIN cleanliness_areas ca ON ct.area_id = ca.id
                JOIN cleanliness_shifts cs ON ct.shift_id = cs.id
                JOIN cleaning_staff cst ON ct.staff_id = cst.id
                ORDER BY cr.rating_date DESC";

$result_ratings = $conn->query($sql_ratings);
$ratings = [];
if ($result_ratings) {
    while ($row = $result_ratings->fetch_assoc()) {
        $ratings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cleanliness Ratings - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Cleanliness Ratings</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="cleanliness_ratings.php" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="timetable_id" class="block mb-2 font-semibold">Timetable Entry</label>
                <select id="timetable_id" name="timetable_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Entry</option>
                    <?php foreach ($timetables as $entry): ?>
                        <option value="<?php echo $entry['id']; ?>">
                            <?php echo htmlspecialchars($entry['cleaning_date'] . ' - ' . $entry['area_name'] . ' - ' . $entry['shift_name'] . ' - ' . $entry['staff_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="rating" class="block mb-2 font-semibold">Rating (1-5)</label>
                <select id="rating" name="rating" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Rating</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="comments" class="block mb-2 font-semibold">Comments</label>
                <input type="text" id="comments" name="comments" class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Add Rating</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Area</th>
                    <th class="p-3 border border-gray-700">Shift</th>
                    <th class="p-3 border border-gray-700">Staff</th>
                    <th class="p-3 border border-gray-700">Rating</th>
                    <th class="p-3 border border-gray-700">Comments</th>
                    <th class="p-3 border border-gray-700">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ratings as $rating): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $rating['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($rating['area_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($rating['shift_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($rating['staff_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo $rating['rating']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($rating['comments']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($rating['rating_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($ratings)): ?>
                    <tr>
                        <td colspan="7" class="p-3 text-center text-gray-400">No ratings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
