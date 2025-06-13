<?php
// $conn = new mysqli("tagabaryo.com", "tagabary_group_8", "tagabary_group_8", "Y=O_jtGjuvl2");
$conn = new mysqli("localhost", "root", "", "noise_monitoring");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT value FROM monitor_intervals WHERE id = 1");
$row = $result->fetch_assoc();
echo $row['value'];
$conn->close();
