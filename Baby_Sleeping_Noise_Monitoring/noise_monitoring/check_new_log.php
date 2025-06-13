<?php
// $conn = new mysqli("tagabaryo.com", "tagabary_group_8", "tagabary_group_8", "Y=O_jtGjuvl2");
$conn = new mysqli("localhost", "root", "", "noise_monitoring");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT value FROM log_flags WHERE id = 1");
$row = $result->fetch_assoc();
$flag = $row['value'];

if ($flag == 1) {
  $conn->query("UPDATE log_flags SET value = 0 WHERE id = 1");
  echo "1";
} else {
  echo "0";
}
$conn->close();
