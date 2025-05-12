<?php
require_once 'auth.php';
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Check if current user is admin (role_id = 1 assumed as admin)
session_start();
if ($_SESSION['role_id'] != 1) {
    echo "Access denied. Admins only.";
    exit;
}

$error = '';
$success = '';

// CSRF token generation and validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Handle delete user request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'], $_POST['csrf_token'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token.';
    } else {
        $delete_user_id = intval($_POST['delete_user_id']);
        if ($delete_user_id === $_SESSION['user_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $delete_user_id);
            if ($stmt->execute()) {
                $success = 'User deleted successfully.';
            } else {
                $error = 'Error deleting user: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Handle form submission for adding new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['role_id'], $_POST['csrf_token'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role_id = intval($_POST['role_id']);

        if ($username === '' || $email === '' || $password === '' || $role_id === 0) {
            $error = 'All fields are required.';
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'Username or email already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $username, $email, $hashed_password, $role_id);
                if ($stmt->execute()) {
                    $success = 'User added successfully.';
                } else {
                    $error = 'Error adding user: ' . $conn->error;
                }
            }
            $stmt->close();
        }
    }
}

// Fetch all users with role names
$sql = "SELECT u.id, u.username, u.email, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.username ASC";
$result = $conn->query($sql);
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Users - Gym Supervision System</title>
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
        <h1 class="text-3xl font-bold mb-6">Admin Users</h1>

        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_users.php" class="mb-6 space-y-4 max-w-md">
            <div>
                <label for="username" class="block mb-2 font-semibold">Username</label>
                <input type="text" id="username" name="username" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="email" class="block mb-2 font-semibold">Email</label>
                <input type="email" id="email" name="email" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="password" class="block mb-2 font-semibold">Password</label>
                <input type="password" id="password" name="password" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="role_id" class="block mb-2 font-semibold">Role</label>
                <select id="role_id" name="role_id" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-white text-black font-bold px-6 py-3 rounded hover:bg-gray-300 transition">Add User</button>
        </form>

            <table class="w-full text-left border-collapse border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-3 border border-gray-700">ID</th>
                    <th class="p-3 border border-gray-700">Username</th>
                    <th class="p-3 border border-gray-700">Email</th>
                    <th class="p-3 border border-gray-700">Role</th>
                    <th class="p-3 border border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border border-gray-700 hover:bg-gray-700">
                        <td class="p-3 border border-gray-700"><?php echo $user['id']; ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="p-3 border border-gray-700"><?php echo htmlspecialchars($user['role_name']); ?></td>
                        <td class="p-3 border border-gray-700">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" action="admin_users.php" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>" />
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                            <?php else: ?>
                                <span class="text-gray-500">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-400">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
