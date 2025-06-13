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

if (isset($_GET['value'])) {
  $value = intval($_GET['value']);
  $conn->query("UPDATE realtime_loudness SET value = $value WHERE id = 1");
  echo "OK";
  $conn->close();
  exit;
}

$result = $conn->query("SELECT value FROM realtime_loudness WHERE id = 1");
$row = $result->fetch_assoc();
echo $row ? $row['value'] : "0";
$conn->close();
