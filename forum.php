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

$sql = "SELECT threads.*, bruker.brukernavn 
        FROM threads
        LEFT JOIN bruker ON threads.idbruker = bruker.idbruker
        ORDER BY threads.timestamp DESC";

$result = $conn->query($sql);

if ($result) {
    $threads = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error fetching threads from the database: " . $conn->error;
    $threads = array();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>64chan</title>
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
        </div>
        <div class="navbar-switch">
            <button id="modeSwitch">Switch to Light Mode</button>
        </div>
    </div>

    <div>
        <?php foreach ($threads as $thread): ?>
            <div class="thread-container">
                <h3><?= htmlspecialchars($thread['tittel']) ?? 'No Title' ?></h3>
                <p>Posted by <?= htmlspecialchars($thread['brukernavn']) ?? 'Unknown User' ?> on <?= $thread['timestamp'] ?></p>
                <?php if (isset($thread['innhold'])): ?>
                    <p><?= htmlspecialchars($thread['innhold']) ?></p>
                <?php else: ?>
                    <p>No content available</p>
                <?php endif; ?>
                <a href="view_thread.php?idthread=<?= $thread['idthread'] ?>">View Thread</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div>
        <h3>Create New Thread</h3>
        <form action="create_thread.php" method="post">
            <label for="thread_title">Title:</label>
            <input type="text" id="thread_title" name="thread_title" required>
            <br>
            <label for="thread_content">Content:</label>
            <input type="text" id="thread_content" name="thread_content" required>
            <br>
            <input type="submit" value="Create Thread">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modeSwitch = document.getElementById('modeSwitch');
            const stylesheet = document.getElementById('stylesheet');
            const currentMode = localStorage.getItem('mode');

            if (currentMode === 'light') {
                stylesheet.href = 'stylesoppgaven_light.css';
                document.body.classList.add('light-mode');
                modeSwitch.textContent = 'Switch to Dark Mode';
            } else {
                stylesheet.href = 'stylesoppgaven.css';
                document.body.classList.remove('light-mode');
                modeSwitch.textContent = 'Switch to Light Mode';
            }

            modeSwitch.addEventListener('click', function() {
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
        });
    </script>
</body>

</html>
