<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Function to send email notification
function send_email_notification($to, $subject, $message) {
    $headers = "From: no-reply@gymsupervision.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return mail($to, $subject, $message, $headers);
}

// Fetch machines for dropdown
$machines = [];
$result = $conn->query("SELECT id, machine_name, location FROM machines ORDER BY machine_name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $machines[] = $row;
    }
}

$error = '';
$success = '';

// Handle form submission for adding status log and updating machine status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['machine_id'], $_POST['status'], $_POST['remarks'])) {
    $machine_id = intval($_POST['machine_id']);
    $status = $_POST['status'];
    $remarks = trim($_POST['remarks']);
    $changed_by = $_SESSION['user_id'];

    if ($machine_id <= 0 || $status === '') {
        $error = 'Machine and status are required.';
    } else {
        // Insert status log
        $stmt = $conn->prepare("INSERT INTO machine_status_logs (machine_id, status, remarks, changed_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $machine_id, $status, $remarks, $changed_by);
        if ($stmt->execute()) {
            // Update machine status
            $update_stmt = $conn->prepare("UPDATE machines SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $status, $machine_id);
            $update_stmt->execute();

            // Fetch machine details for notification
            $machine_stmt = $conn->prepare("SELECT machine_name, location FROM machines WHERE id = ?");
            $machine_stmt->bind_param("i", $machine_id);
            $machine_stmt->execute();
            $machine_result = $machine_stmt->get_result();
            $machine = $machine_result->fetch_assoc();

            // Prepare notification message
            $message = "Machine Status Update:\n";
            $message .= "Machine: " . $machine['machine_name'] . "\n";
            $message .= "Location: " . $machine['location'] . "\n";
            $message .= "Status: " . $status . "\n";
            $message .= "Remarks: " . $remarks . "\n";

            // TODO: Fetch admin and maintenance staff emails and phone numbers from users or staff tables
            // For now, use placeholders
            $admin_email = "admin@example.com";
            $maintenance_email = "maintenance@example.com";
            $admin_phone = "1234567890";
            $maintenance_phone = "0987654321";

            // Send email notifications
            send_email_notification($admin_email, "Machine Status Update", $message);
            send_email_notification($maintenance_email, "Machine Status Update", $message);

            // Send SMS notifications using send_sms function from config.php
            send_sms($admin_phone, $message);
            send_sms($maintenance_phone, $message);

            $success = 'Machine status updated and notifications sent.';
        } else {
            $error = 'Error updating machine status: ' . $conn->error;
        }
    }
}

// Handle deletion of status log
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM machine_status_logs WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Machine status log deleted successfully.';
    } else {
        $error = 'Error deleting status log: ' . $conn->error;
    }
}

// Fetch status logs with machine and user info
$sql = "SELECT msl.id, m.machine_name, msl.status, msl.remarks, u.username, msl.changed_at
        FROM machine_status_logs msl
        JOIN machines m ON msl.machine_id = m.id
        JOIN users u ON msl.changed_by = u.id
        ORDER BY msl.changed_at DESC";

$result = $conn->query($sql);
$status_logs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status_logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Machine Status Logs - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Machine Status Logs</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="machine_status_logs.php" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="machine_id" class="block mb-2 font-semibold">Machine</label>
                <select id="machine_id" name="machine_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Machine</option>
                    <?php foreach ($machines as $machine): ?>
                        <option value="<?php echo $machine['id']; ?>"><?php echo htmlspecialchars($machine['machine_name'] . ' (' . $machine['location'] . ')'); ?></option>
                    <?php endforeach; ?>
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
            <div>
                <label for="remarks" class="block mb-2 font-semibold">Remarks</label>
                <input type="text" id="remarks" name="remarks" class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Update Status</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Machine</th>
                    <th class="p-3 border border-gray-700">Status</th>
                    <th class="p-3 border border-gray-700">Remarks</th>
                    <th class="p-3 border border-gray-700">Changed By</th>
                    <th class="p-3 border border-gray-700">Changed At</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($status_logs as $log): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $log['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($log['machine_name']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($log['status']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($log['remarks']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($log['username']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($log['changed_at']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <a href="machine_status_logs.php?delete=<?php echo $log['id']; ?>" onclick="return confirm('Are you sure you want to delete this status log?');" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($status_logs)): ?>
                    <tr>
                        <td colspan="7" class="p-3 text-center text-gray-400">No status logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
