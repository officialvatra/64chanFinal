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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO bruker (brukernavn, passord, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $username, $hashed_password, $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION["idbruker"] = $stmt->insert_id;
            $_SESSION["username"] = $username;

            header("Location: forum.php");
            exit();
        } else {
            $error_message = "Error creating user: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>64chan - Sign Up</title>
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

    <h2>64chan - Sign Up</h2>

    <div class="signup-form">
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="terms">I have read and agree to the Terms and Conditions</label>
            <input type="checkbox" id="terms" name="terms" required>
            <br>
            <input type="submit" value="Sign Up">
        </form>
        <p><a href="lovverk.txt">Click here to read the Terms and Conditions</a></p>
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
