<?php
require_once __DIR__ . '/lib/plato_client.php';
$plato = new CocapnPlatoClient();
$health = $plato->get_service_health();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fleet Status — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .health-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
    .health-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem; }
    .health-card .svc-name { font-weight: 600; font-size: 1rem; margin-bottom: 0.4rem; }
    .health-card .svc-port { color: var(--muted); font-size: 0.8rem; font-family: 'Space Mono', monospace; }
    .health-row { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
    .health-row:last-child { border-bottom: none; }
    .health-name { font-weight: 600; }
    .health-port { color: var(--muted); font-size: 0.8rem; font-family: 'Space Mono', monospace; margin-left: 0.5rem; }
    .health-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .health-badge.up { background: rgba(34,197,94,0.15); color: #22c55e; }
    .health-badge.down { background: rgba(239,68,68,0.15); color: #ef4444; }
    .health-ms { color: var(--muted); font-size: 0.8rem; font-family: 'Space Mono', monospace; }
    .refresh-bar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border); margin-bottom: 1rem; }
    .countdown { color: var(--muted); font-size: 0.85rem; font-family: 'Space Mono', monospace; }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div class="section-header">
    <h2>Fleet Status</h2>
    <p>Live health checks across all CoCapn services.</p>
  </div>

  <div class="refresh-bar">
    <div><span class="badge blue">Auto-refresh: 60s</span></div>
    <div class="countdown" id="countdown">Refresh in 60s</div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Service</th>
          <th>Port</th>
          <th>Status</th>
          <th>Response</th>
        </tr>
      </thead>
      <tbody id="health-body">
        <?php foreach ($health as $name => $svc): ?>
        <tr>
          <td><strong><?= htmlspecialchars($name) ?></strong></td>
          <td class="mono" style="color:var(--muted)">:<?= $svc['port'] ?></td>
          <td>
            <span class="health-badge <?= $svc['status'] === 'up' ? 'up' : 'down' ?>">
              <?= $svc['status'] === 'up' ? '✓ Up' : '✗ Down' ?>
            </span>
          </td>
          <td class="health-ms"><?= $svc['ms'] !== null ? $svc['ms'] . 'ms' : '—' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:2rem">
    <div class="card">
      <h3>PLATO Room Server (:8847)</h3>
      <?php
      $rooms = $plato->get_all_rooms();
      $room_count = is_array($rooms) ? count($rooms) : 0;
      ?>
      <p style="color:var(--muted);margin-top:0.4rem">
        <?= $room_count ?> rooms active &nbsp;•&nbsp; 
        <a href="explorer.php" style="font-size:0.875rem">Open Explorer →</a>
      </p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>

<script>
let seconds = 60;
const countdown = document.getElementById('countdown');

function tick() {
  seconds--;
  countdown.textContent = `Refresh in ${seconds}s`;
  if (seconds <= 0) { location.reload(); }
}
setInterval(tick, 1000);
</script>
</body>
</html>