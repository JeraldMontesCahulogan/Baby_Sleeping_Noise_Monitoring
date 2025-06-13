<?php
// $conn = new mysqli("tagabaryo.com", "tagabary_group_8", "tagabary_group_8", "Y=O_jtGjuvl2");
$conn = new mysqli("localhost", "root", "", "noise_monitoring");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$logs = $conn->query("SELECT * FROM cry_logs ORDER BY time DESC");
if ($logs && $logs->num_rows > 0) {
  while ($row = $logs->fetch_assoc()) {
    $formatted_time = date("g:i:s A", strtotime($row['time']));
    echo "<tr><td>$formatted_time</td><td>{$row['loudness']}</td><td>{$row['loudness_level']}</td></tr>";
  }
} else {
  echo "<tr><td colspan='3'>No logs yet.</td></tr>";
}
$conn->close();
