<?php
require_once 'auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Gym Supervision System</title>
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
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-gray-900 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center">Gym Supervision Login</h1>
        <?php if ($error): ?>
            <div class="bg-red-600 text-white p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="space-y-6">
            <div>
                <label for="username" class="block mb-2 font-semibold">Username</label>
                <input type="text" id="username" name="username" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <div>
                <label for="password" class="block mb-2 font-semibold">Password</label>
                <input type="password" id="password" name="password" required class="w-full p-3 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-white" />
            </div>
            <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-300 transition">Login</button>
        </form>
    </div>
</body>
</html>
