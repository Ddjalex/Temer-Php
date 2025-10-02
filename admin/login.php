<?php
require_once __DIR__ . '/../backend/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: /admin');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}

if (isLoggedIn()) {
    header('Location: /admin');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Temer Properties</title>
    <link rel="stylesheet" href="/frontend/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            color: var(--temer-dark);
            margin-bottom: 30px;
            text-align: center;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--temer-dark);
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
            background: var(--bg-color);
            color: var(--text-color);
        }
        .login-form button {
            width: 100%;
            padding: 12px;
            background: var(--temer-green);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-form button:hover {
            background: var(--temer-dark);
        }
        .error {
            background: #f44336;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--temer-green);
            text-decoration: none;
        }
        .dark-mode-toggle-login {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--temer-green);
            border: none;
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .dark-mode-toggle-login:hover {
            background: var(--temer-dark);
            transform: scale(1.1);
        }
        .dark-mode-toggle-login svg {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <button class="dark-mode-toggle-login" onclick="toggleDarkMode()">
        <svg id="darkModeIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="back-link">
            <a href="/">Back to Home</a>
        </div>
    </div>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
        }

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>
