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

$idthread = $_POST["idthread"] ?? '';
$reply_content = $_POST["reply_content"] ?? '';

$sql = "INSERT INTO replies (idthread, idbruker, timestamp, innhold) VALUES (?, ?, NOW(), ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iss", $idthread, $idbruker, $reply_content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: view_thread.php?idthread=" . $idthread);
        exit();
    } else {
        echo "Error creating reply: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
