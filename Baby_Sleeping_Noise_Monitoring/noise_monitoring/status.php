<?php
// $host = "tagabaryo.com";
// $db = "tagabary_group_8";
// $user = "tagabary_group_8";
// $pass = "Y=O_jtGjuvl2";
$host = "localhost";
$db = "noise_monitoring";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_GET['set'])) {
  $status = ($_GET['set'] === "pause") ? "pause" : "play";
  $stmt = $conn->prepare("UPDATE monitoring_status SET status = ? WHERE id = 1");
  $stmt->bind_param("s", $status);
  $stmt->execute();
  echo $status;
  $stmt->close();
  $conn->close();
  exit;
}

$result = $conn->query("SELECT status FROM monitoring_status WHERE id = 1");
$row = $result->fetch_assoc();
echo $row ? $row['status'] : "play";
$conn->close();
