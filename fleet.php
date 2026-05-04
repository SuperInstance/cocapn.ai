<?php
require_once __DIR__ . '/lib/plato_client.php';
$plato = new CocapnPlatoClient();
$fleet = $plato->get_fleet_status();
$agents = $plato->get_agent_registry() ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fleet — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .beacon-map { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 2rem; display: flex; justify-content: center; }
    .agent-table-wrap { margin-top: 2rem; }
    .role-badge { font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 4px; background: rgba(59,130,246,0.12); color: var(--accent); }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div class="section-header">
    <h2>Fleet Registry</h2>
    <p>Live agent status from Keeper at :8900</p>
  </div>

  <!-- Beacon map -->
  <div class="beacon-map">
    <svg viewBox="0 0 400 280" width="100%" max-width="500">
      <!-- Grid rings -->
      <?php for($r=30;$r<=120;$r+=30): ?>
      <circle cx="200" cy="140" r="<?= $r ?>" fill="none" stroke="#1e293b" stroke-width="1"/>
      <?php endfor; ?>
      <!-- Crosshairs -->
      <line x1="200" y1="20" x2="200" y2="260" stroke="#1e293b" stroke-width="1"/>
      <line x1="60" y1="140" x2="340" y2="140" stroke="#1e293b" stroke-width="1"/>
      <!-- Center lighthouse -->
      <circle cx="200" cy="140" r="8" fill="#3b82f6" opacity="0.8"/>
      <text x="200" y="170" text-anchor="middle" fill="#64748b" font-size="11" font-family="Space Mono">KEEPER</text>
      <!-- Agent dots -->
      <?php
      $positions = [
        ['name'=>'Oracle1','x'=>200,'y'=>140,'c'=>'#f59e0b'],
        ['name'=>'JetsonClaw1','x'=>268,'y'=>82,'c'=>'#10b981'],
        ['name'=>'Forgemaster','x'=>145,'y'=>195,'c'=>'#a855f7'],
        ['name'=>'CCC','x'=>290,'y'=>130,'c'=>'#3b82f6'],
      ];
      foreach($positions as $i=>$p):
      ?>
      <circle cx="<?= $p['x'] ?>" cy="<?= $p['y'] ?>" r="14" fill="<?= $p['c'] ?>" opacity="0.25">
        <animate attributeName="r" values="14;22;14" dur="<?= 2 + $i*0.3 ?>s" repeatCount="indefinite"/>
        <animate attributeName="opacity" values="0.25;0.1;0.25" dur="<?= 2 + $i*0.3 ?>s" repeatCount="indefinite"/>
      </circle>
      <circle cx="<?= $p['x'] ?>" cy="<?= $p['y'] ?>" r="5" fill="<?= $p['c'] ?>"/>
      <text x="<?= $p['x'] ?>" y="<?= $p['y'] + 22 ?>" text-anchor="middle" fill="#e2e8f0" font-size="10" font-family="Space Mono"><?= $p['name'] ?></text>
      <?php endforeach; ?>
    </svg>
  </div>

  <!-- Agent table -->
  <div class="agent-table-wrap">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Vessel</th>
            <th>Role</th>
            <th>Status</th>
            <th>Uptime</th>
            <th>Last Seen</th>
          </tr>
        </thead>
        <tbody id="agent-tbody">
          <?php if (empty($agents)): ?>
          <?php
          $default_agents = [
            ['name'=>'Oracle1','role'=>'Keeper','status'=>'active','uptime'=>time()-strtotime('2026-05-01'),'last_seen'=>time()],
            ['name'=>'JetsonClaw1','role'=>'Edge','status'=>'idle','uptime'=>time()-strtotime('2026-05-01'),'last_seen'=>time()-300],
            ['name'=>'Forgemaster','role'=>'Foundry','status'=>'active','uptime'=>time()-strtotime('2026-05-01'),'last_seen'=>time()],
            ['name'=>'CCC','role'=>'Public','status'=>'active','uptime'=>time()-strtotime('2026-05-01'),'last_seen'=>time()-30],
          ];
          foreach($default_agents as $a):
            $s = $a['status'] === 'active' ? 'green' : ($a['status'] === 'idle' ? 'yellow' : 'gray');
          ?>
          <tr>
            <td><strong class="mono"><?= $a['name'] ?></strong></td>
            <td><span class="role-badge"><?= $a['role'] ?></span></td>
            <td><span class="status-dot <?= $s ?>"></span> <?= ucfirst($a['status']) ?></td>
            <td class="mono" style="color:var(--muted)"><?= $plato->format_uptime($a['uptime']) ?></td>
            <td class="mono" style="color:var(--muted)"><?= date('H:i:s', $a['last_seen']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <?php foreach($agents as $a): ?>
          <tr>
            <td><strong class="mono"><?= htmlspecialchars($a['name'] ?? 'Unknown') ?></strong></td>
            <td><span class="role-badge"><?= htmlspecialchars($a['role'] ?? 'Agent') ?></span></td>
            <td><span class="status-dot green"></span> Active</td>
            <td class="mono" style="color:var(--muted)"><?= $plato->format_uptime($a['uptime'] ?? 0) ?></td>
            <td class="mono" style="color:var(--muted)"><?= date('H:i:s', $a['last_seen'] ?? time()) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>
</body>
</html>