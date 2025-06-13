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

// Fetch current notification settings (assume id = 1 is fixed)
$query = "SELECT * FROM notification_setting WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$phoneNumber = $settings['phone_number'] ?? '';
$apiKey = $settings['api_key'] ?? '';

if (isset($_POST['save_notification'])) {
  $phone = trim($_POST['phone_number']);
  $key = trim($_POST['api_key']);

  if (!empty($phone) && !empty($key)) {
    $stmt = mysqli_prepare($conn, "UPDATE notification_setting SET phone_number = ?, api_key = ? WHERE id = 1");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $key);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect after successful update to prevent resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
  } else {
    // Redirect with error flag
    header("Location: " . $_SERVER['PHP_SELF'] . "?error=1");
    exit;
  }
}


// Create "text" folder if it doesn't exist
if (!is_dir("text")) {
  mkdir("text", 0777, true);
}

// Define reusable path for interval file
$intervalFile = "text/interval.txt";

// Save monitor interval if provided
if (isset($_GET['time'])) {
  $time = (int)$_GET['time'];
  $conn->query("UPDATE monitor_intervals SET value = $time WHERE id = 1");
}


// Handle loudness logging
if (isset($_GET['loudness'])) {
  $loudness = $_GET['loudness'];

  date_default_timezone_set('Asia/Manila');
  $time = date("Y-m-d H:i:s");

  if ($loudness >= 500 && $loudness < 667) {
    $loudness_level = 'moderate';
  } elseif ($loudness >= 667 && $loudness < 834) {
    $loudness_level = 'loud';
  } elseif ($loudness >= 834 && $loudness <= 1000) {
    $loudness_level = 'dangerous';
  } else {
    $loudness_level = 'unknown';
  }

  $sql = "INSERT INTO cry_logs (time, loudness, loudness_level) VALUES ('$time', '$loudness', '$loudness_level')";

  if ($conn->query($sql) === TRUE) {
    $conn->query("UPDATE log_flags SET value = 1 WHERE id = 1");

    // WhatsApp alert
    $apikey = $apiKey;
    $number = preg_replace('/^0/', '+63', $phoneNumber);
    $message = urlencode("Loud noise detected – please check on the baby immediately.\n📢 Sound level: $loudness dB\n🔊 Status: $loudness_level");
    $url = "https://api.callmebot.com/whatsapp.php?phone=$number&text=$message&apikey=$apikey";
    file_get_contents($url);

    echo "Logged and WhatsApp message sent.<br>";
  } else {
    echo "Error: " . $conn->error . "<br>";
  }
}

date_default_timezone_set('Asia/Manila');

$selected_date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : date("Y-m-d");
$selected_time = $_GET['filter_time'] ?? '';
$selected_status = $_GET['status'] ?? '';

$query = "SELECT * FROM cry_logs WHERE DATE(time) = ?";
$params = [$selected_date];
$types = "s";

if (!empty($selected_time)) {
  $query .= " AND DATE_FORMAT(time, '%l %p') = ?";
  $params[] = date("g A", strtotime($selected_time));
  $types .= "s";
}

if (!empty($selected_status)) {
  $query .= " AND loudness_level = ?";
  $params[] = $selected_status;
  $types .= "s";
}

$query .= " ORDER BY time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$log_result = $stmt->get_result();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/log.css">
  <title>Silent Eye - Baby Monitor</title>
</head>

<body>
  <div class="container">

    <!-- Modal -->
    <div id="settingsModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2 class="modal-title">Update WhatsApp Notification Settings</h2>
        <form id="settingsForm" method="POST">
          <label for="phone" class="labels">Phone Number (e.g., 09XXXXXXXXX):</label>
          <input type="text" id="phone" name="phone_number" required pattern="[0-9]{11}" value="<?php echo htmlspecialchars($phoneNumber); ?>">

          <div class="form-group">
            <label for="api_key">Notification Key</label>
            <div class="password-wrapper" style="position: relative;">
              <input type="password" id="api_key" name="api_key" required id="api_key" value="<?php echo htmlspecialchars($apiKey); ?>">
              <span onclick="togglePassword()" style="position: absolute; right: 10px; top: 45%; transform: translateY(-50%); cursor: pointer;">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </span>
            </div>
          </div>


          <input type="submit" name="save_notification" value="Save Settings" class="save_notification labels">
        </form>
      </div>
    </div>


    <div class="header">
      <div class="logo-container">
        <img src="img/baby_logo.png" alt="" class="logo-img">
        <h1>Silent Eye</h1>
      </div>
      <button class="settings-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings-icon lucide-settings">
          <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
          <circle cx="12" cy="12" r="3" />
        </svg>
      </button>
    </div>

    <div class="card-container">
      <div class="card card-left">
        <div class="live-monitoring-container">
          <script src="https://cdn.lordicon.com/lordicon.js"></script>
          <lord-icon
            src="https://cdn.lordicon.com/ckooqaow.json"
            trigger="loop"
            state="loop-recording"
            class="lordicon">
          </lord-icon>
          <h2>Live Monitoring</h2>
        </div>
        <p style="color:#666;">Real-time sound detection for your baby</p>
        <div class="live-loudness" id="liveLoudness">Loading...</div>
        <div class="loudness-bar-container">
          <div class="loudness-bar-bg">
            <div id="loudnessBar" class="loudness-bar-fill"></div>
          </div>
        </div>

        <div class="current-status">Current Status: <span id="liveLoudnessStatus">Loading...</span></div>
        <div class="monitor-controls">
          <!-- <button id="toggleBtn" onclick="toggleStatus()">Pause Monitoring</button> -->
          <button id="toggleBtn" onclick="toggleStatus()" class="monitor-btn">
            Pause Monitoring
          </button>

        </div>

        <!-- Controls -->
        <div class="controls">
          <div class="controls-sides">
            <span class="control-label">Monitor Interval</span>
            <div class="controls-sides controls-left">
              <select class="select-box" name="time" id="time">
                <option value="30">30 seconds</option>
                <option value="60">1 minute</option>
                <option value="120">2 minutes</option>
              </select>
              <button onclick="setIntervalTime()" class="set-btn">Set</button>
            </div>
          </div>

          <div class="controls-sides">
            <span class="control-label">Current Settings</span>
            <div class="controls-sides controls-right">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock4-icon lucide-clock-4">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              <div>
                <div>Current Interval: <?php
                                        $conn = new mysqli($host, $user, $pass, $db);
                                        if (!$conn->connect_error) {
                                          $res = $conn->query("SELECT value FROM monitor_intervals WHERE id = 1");
                                          $row = $res->fetch_assoc();
                                          echo $row['value'] . ' seconds';
                                          $conn->close();
                                        } else {
                                          echo "N/A";
                                        }
                                        ?> seconds</div>
                <div id="cooldownDisplay">Loading...</div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="card card-right">
        <?php
        $headerDateText = (date("Y-m-d") == $selected_date) ? "Today" : date("F j, Y", strtotime($selected_date));
        ?>
        <h2>📋 Activity Log: <span><?= $headerDateText ?></span></h2>

        <p style="color:#666;">Recent sound detection history</p>

        <form method="GET" class="filter-form">
          <div class="filter-container">
            <div class="filter-group">
              <label for="date">Date:</label>
              <input type="date" name="date" id="date" value="<?= $selected_date ?>" required>
            </div>

            <div class="filter-group">
              <label for="filter_time">Time:</label>
              <input type="time" name="filter_time" id="filter_time" value="<?= $selected_time ?>">
            </div>

            <div class="filter-group">
              <label for="status">Status:</label>
              <select name="status" id="status">
                <option value="">All</option>
                <option value="moderate" <?= $selected_status == 'moderate' ? 'selected' : '' ?>>Moderate</option>
                <option value="loud" <?= $selected_status == 'loud' ? 'selected' : '' ?>>Loud</option>
                <option value="dangerous" <?= $selected_status == 'dangerous' ? 'selected' : '' ?>>Dangerous</option>
              </select>
            </div>
          </div>

          <div class="filter-btn-container">
            <button type="submit" class="filter-btn">Filter</button>
            <a href="?date=<?= date('Y-m-d') ?>" class="reset-btn filter-btn" style="text-align: center;">Reset</a>
          </div>
        </form>



        <script>
          function resetFilters() {
            const today = new Date().toISOString().split('T')[0];
            window.location.href = `?date=${today}`;
          }
        </script>


        <table>
          <thead>
            <tr>
              <th>Time</th>
              <th>Loudness</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="logTableBody">
            <?php if ($log_result && $log_result->num_rows > 0): ?>
              <?php while ($row = $log_result->fetch_assoc()): ?>
                <tr>
                  <td><?= date("g:i:s A", strtotime($row['time'])) ?></td>
                  <td><?= $row['loudness'] ?></td>
                  <td>
                    <span class="status-badge <?= $row['loudness_level'] ?>">
                      <?= ucfirst($row['loudness_level']) ?>
                    </span>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3">No logs yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="toast" class="toast"></div>

  <script>
    function fetchLogs() {
      fetch('fetch_logs.php')
        .then(res => res.text())
        .then(data => {
          document.getElementById('logTableBody').innerHTML = data;
        });
    }

    function checkForNewLogs() {
      fetch('status.php')
        .then(res => res.text())
        .then(status => {
          if (status === "play") {
            fetch('check_new_log.php')
              .then(res => res.text())
              .then(flag => {
                if (flag === "1") fetchLogs();
              });
          }
        });
    }

    function updateCooldown() {
      fetch('status.php')
        .then(res => res.text())
        .then(status => {
          if (status === "play") {
            fetch('cooldown_status.php')
              .then(res => res.text())
              .then(data => {
                document.getElementById('cooldownDisplay').innerText =
                  data === "0" ? "Ready to detect!" : `Cooling down: ${data} seconds remaining`;
              });
          } else {
            document.getElementById('cooldownDisplay').innerText = "Paused";
          }
        });
    }

    function updateStatusDisplay() {
      fetch('status.php')
        .then(res => res.text())
        .then(status => {
          const toggleBtn = document.getElementById("toggleBtn");

          if (status === "pause") {
            toggleBtn.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
          Resume Monitoring
        `;
            toggleBtn.style.backgroundColor = "#23a56f"; // more red
          } else {
            toggleBtn.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/></svg>
          Pause Monitoring
        `;
            toggleBtn.style.backgroundColor = "#e2334e"; // green
          }

          const statusBadge = document.getElementById("statusBadge");
          statusBadge.innerText = status.charAt(0).toUpperCase() + status.slice(1);
        });
    }



    function toggleStatus() {
      const newStatus = document.getElementById("toggleBtn").innerText.includes("Pause") ? "pause" : "play";
      fetch('status.php?set=' + newStatus)
        .then(() => updateStatusDisplay());
    }

    function setIntervalTime() {
      const time = document.getElementById("time").value;
      window.location.href = "?time=" + time;
    }

    function updateRealtimeLoudness() {
      fetch('realtime_loudness.php')
        .then(res => res.text())
        .then(value => {
          document.getElementById("liveLoudness").innerText = value;
        });
    }

    function updateRealtimeStatus() {
      fetch('realtime_loudness.php')
        .then(res => res.text())
        .then(value => {
          const loudness = parseInt(value);
          const statusEl = document.getElementById("liveLoudnessStatus");
          const barEl = document.getElementById("loudnessBar");

          let statusText = "";
          let statusColor = "";

          // Set status text and corresponding color
          if (loudness >= 0 && loudness < 250) {
            statusText = "Quiet";
            statusColor = "#23a56f"; // Green
          } else if (loudness >= 250 && loudness < 500) {
            statusText = "Normal";
            statusColor = "#66bb6a"; // Yellow Green
          } else if (loudness >= 500 && loudness < 667) {
            statusText = "Moderate";
            statusColor = "#ff9800"; // Orange
          } else if (loudness >= 667 && loudness < 834) {
            statusText = "Loud";
            statusColor = "#ff5722"; // Red Orange
          } else if (loudness >= 834) {
            statusText = "Dangerous";
            statusColor = "#e53935"; // Red
          } else {
            statusText = "Unknown";
            statusColor = "#888";
          }

          statusEl.innerText = statusText;
          statusEl.style.color = statusColor;

          // Update bar width
          let percentage = Math.min((loudness / 1000) * 100, 100);
          barEl.style.width = `${percentage}%`;
        });
    }



    setInterval(updateRealtimeLoudness, 500);
    setInterval(updateRealtimeStatus, 500);
    setInterval(updateCooldown, 1000);
    setInterval(checkForNewLogs, 1000);
    setInterval(updateStatusDisplay, 3000);

    updateCooldown();
    updateStatusDisplay();

    const modal = document.getElementById("settingsModal");
    const btn = document.querySelector(".settings-btn");
    const span = document.querySelector(".modal .close");

    btn.onclick = () => modal.style.display = "block";
    span.onclick = () => modal.style.display = "none";
    window.onclick = event => {
      if (event.target == modal) modal.style.display = "none";
    };

    function togglePassword() {
      const input = document.getElementById('api_key');
      const icon = document.getElementById('eyeIcon');

      if (input.type === 'password') {
        input.type = 'text';
        icon.outerHTML = `
        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off">
          <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.06-6.12" />
          <path d="M1 1l22 22" />
        </svg>`;
      } else {
        input.type = 'password';
        icon.outerHTML = `
        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
          <circle cx="12" cy="12" r="3" />
        </svg>`;
      }
    }

    function showToast(message, success = true) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.className = `toast show ${success ? 'success' : 'error'}`;
      setTimeout(() => {
        toast.className = toast.className.replace("show", "");
      }, 3000);
    }

    // Check for query params (success=1 or error=1)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
      showToast("Notification settings updated successfully.", true);
      urlParams.delete('success');
    } else if (urlParams.has('error')) {
      showToast("Please fill in all required fields.", false);
      urlParams.delete('error');
    }

    // Remove the params from URL without reloading the page
    if (urlParams.toString()) {
      const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
      window.history.replaceState({}, document.title, newUrl);
    } else {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  </script>
</body>

</html>