<?php
// $conn = new mysqli("tagabaryo.com", "tagabary_group_8", "tagabary_group_8", "Y=O_jtGjuvl2");
$conn = new mysqli("localhost", "root", "", "noise_monitoring");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_GET['cooldown'])) {
  $value = intval($_GET['cooldown']);
  $conn->query("UPDATE cooldowns SET value = $value WHERE id = 1");
  echo "Cooldown updated";
  $conn->close();
  exit;
}

$result = $conn->query("SELECT value FROM cooldowns WHERE id = 1");
$row = $result->fetch_assoc();
echo $row['value'];
$conn->close();
