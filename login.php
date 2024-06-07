<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM bruker WHERE brukernavn = ? AND passord = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION["username"] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 64chan</title>
    <link id="stylesheet" rel="stylesheet" href="stylesoppgaven.css">
</head>

<body>
    <div class="navbar">
        <a href="#" class="navbar-center">64chan</a>
        <div class="navbar-right">
            <?php if (isset($_SESSION["username"])): ?>
                <a href="login.php" class="logged-in">Logged in as <?= $_SESSION["username"] ?></a>
            <?php else: ?>
                <a href="login.php">Log In</a>
            <?php endif; ?>
            <button id="modeSwitch">Switch to Light Mode</button>
        </div>
    </div>

    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Log In">
        </form>
    </div>

    <script>
        const modeSwitch = document.getElementById('modeSwitch');
        const stylesheet = document.getElementById('stylesheet');

        document.addEventListener('DOMContentLoaded', () => {
            const mode = localStorage.getItem('mode') || 'dark';
            if (mode === 'light') {
                stylesheet.href = 'stylesoppgaven_light.css';
                document.body.classList.add('light-mode');
                modeSwitch.textContent = 'Switch to Dark Mode';
            } else {
                stylesheet.href = 'stylesoppgaven.css';
                document.body.classList.remove('light-mode');
                modeSwitch.textContent = 'Switch to Light Mode';
            }
        });

        modeSwitch.addEventListener('click', () => {
            if (stylesheet.href.includes('stylesoppgaven_light.css')) {
                stylesheet.href = 'stylesoppgaven.css';
                document.body.classList.remove('light-mode');
                modeSwitch.textContent = 'Switch to Light Mode';
                localStorage.setItem('mode', 'dark');
            } else {
                stylesheet.href = 'stylesoppgaven_light.css';
                document.body.classList.add('light-mode');
                modeSwitch.textContent = 'Switch to Dark Mode';
                localStorage.setItem('mode', 'light');
            }
        });
    </script>
</body>

</html>
