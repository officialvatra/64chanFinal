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

$logged_in = isset($_SESSION["username"]);
$idbruker = $logged_in ? $_SESSION["idbruker"] : null;

$thread_title = $_POST["thread_title"] ?? '';
$thread_content = $_POST["thread_content"] ?? '';

$sql = "INSERT INTO threads (idbruker, tittel, timestamp, innhold) VALUES (?, ?, NOW(), ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iss", $idbruker, $thread_title, $thread_content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: forum.php");
        exit();
    } else {
        echo "Error creating thread: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
