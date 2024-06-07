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

$thread_id = $_GET['idthread'];
$thread = getThreadDetails($conn, $thread_id);
$posts = getThreadReplies($conn, $thread_id);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>64chan - View Thread</title>
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

    <div class="thread-container">
        <h3><?= $thread['tittel'] ?? 'Thread not found'; ?></h3>
        <p>Posted by <?= $thread['username'] ?? 'Unknown User'; ?> on <?= $thread['timestamp'] ?? 'Unknown Date'; ?></p>
        <p><?= $thread['innhold'] ?? 'No content available'; ?></p>
    </div>

    <button class="show-replies" onclick="toggleReplies()">Show Replies</button>

    <div class="replies-container">
        <?php foreach ($posts as $post): ?>
            <div class="reply-container">
                <p>Posted by <?= $post['brukernavn'] ?? 'Unknown User'; ?> on <?= $post['timestamp'] ?? 'Unknown Date'; ?></p>
                <p><?= $post['innhold'] ?? 'No content available'; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="reply-form">
        <h3>Reply to Thread</h3>
        <form action="create_reply.php" method="post">
            <input type="hidden" name="idthread" value="<?= $thread_id; ?>">
            <textarea id="reply_content" name="reply_content" rows="4" cols="50" required></textarea>
            <br>
            <input type="submit" value="Post Reply">
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

        function toggleReplies() {
            var repliesDiv = document.querySelector('.replies-container');
            repliesDiv.style.display = (repliesDiv.style.display === 'none' || repliesDiv.style.display === '') ? 'block' : 'none';
        }
    </script>
</body>
</html>

<?php
function getThreadDetails($connection, $threadId) {
    $sql = "SELECT * FROM threads WHERE idthread = $threadId";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return [];
    }
}

function getThreadReplies($connection, $threadId) {
    $sql = "SELECT replies.*, bruker.brukernavn
            FROM replies
            LEFT JOIN bruker ON replies.idbruker = bruker.idbruker
            WHERE idthread = $threadId
            ORDER BY replies.timestamp DESC";

    $result = $connection->query($sql);

    if ($result) {
        $replies = [];
        while ($row = $result->fetch_assoc()) {
            $replies[] = $row;
        }
        return $replies;
    } else {
        return [];
    }
}
?>
