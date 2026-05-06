<?php
require_once __DIR__ . '/lib/plato_client.php';
$plato = new CocapnPlatoClient();

// Fetch live data
$rooms = $plato->get_all_rooms();
$room_count = is_array($rooms) ? count($rooms) : 0;
$fleet = $plato->get_fleet_status();
$agents = $plato->get_agent_registry();
$agent_count = is_array($agents) ? count($agents) : 0;

// Get tile count from search
$tiles = $plato->search_tiles('*');
$tile_count = is_array($tiles) ? count($tiles) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CoCapn — Build Agents. Raise Agents.</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .hero-radar {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2rem;
    }
    .radar-wrap-hero {
      position: relative;
      width: 260px;
      height: 260px;
    }
    .agent-ping {
      position: absolute;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      animation: ping 2s ease-in-out infinite;
    }
    .agent-ping.oracle1 { background: var(--keeper); box-shadow: 0 0 10px var(--keeper); top: 50%; left: 50%; animation-delay: 0s; }
    .agent-ping.jc1 { background: var(--edge); box-shadow: 0 0 10px var(--edge); top: 25%; left: 60%; animation-delay: 0.5s; }
    .agent-ping.fm { background: #a855f7; box-shadow: 0 0 10px #a855f7; top: 65%; left: 35%; animation-delay: 1s; }
    .agent-ping.ccc { background: var(--accent); box-shadow: 0 0 10px var(--accent); top: 40%; left: 72%; animation-delay: 1.5s; }
    @keyframes ping {
      0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
      50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.4); }
    }
    .quick-links {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 1rem;
    }
    .quick-links a {
      padding: 0.5rem 1.25rem;
      border: 1px solid var(--border);
      border-radius: 6px;
      color: var(--muted);
      font-size: 0.875rem;
      transition: all 0.2s;
    }
    .quick-links a:hover {
      border-color: var(--accent);
      color: var(--accent);
      text-decoration: none;
    }
    .fleet-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; margin-top: 2rem; }
    .live-indicator { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--success); }
    .live-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .index-intro { max-width: 640px; margin: 0 auto; text-align: center; color: var(--muted); font-size: 1.05rem; line-height: 1.8; }
    .index-intro strong { color: var(--text); }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main>
  <!-- Hero -->
  <section class="hero">
    <div class="hero-radar">
      <div class="radar-wrap-hero">
        <svg viewBox="0 0 260 260" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Radar rings -->
          <circle cx="130" cy="130" r="120" stroke="#1e293b" stroke-width="1"/>
          <circle cx="130" cy="130" r="85" stroke="#1e293b" stroke-width="1"/>
          <circle cx="130" cy="130" r="50" stroke="#1e293b" stroke-width="1"/>
          <circle cx="130" cy="130" r="15" stroke="#3b82f6" stroke-width="2" opacity="0.4"/>
          <!-- Crosshairs -->
          <line x1="130" y1="10" x2="130" y2="250" stroke="#1e293b" stroke-width="1"/>
          <line x1="10" y1="130" x2="250" y2="130" stroke="#1e293b" stroke-width="1"/>
          <!-- Sweep -->
          <g class="radar-sweep">
            <line x1="130" y1="130" x2="130" y2="10" stroke="rgba(59,130,246,0.5)" stroke-width="2"/>
            <polygon points="130,130 130,10 180,30" fill="rgba(59,130,246,0.15)"/>
          </g>
          <!-- Agent pings -->
          <circle class="agent-ping oracle1" cx="130" cy="130" r="6"/>
          <circle class="agent-ping jc1" cx="156" cy="97" r="6"/>
          <circle class="agent-ping fm" cx="106" cy="158" r="6"/>
          <circle class="agent-ping ccc" cx="162" cy="126" r="6"/>
        </svg>
      </div>
      <h1>Build Agents.<br>Raise Agents.</h1>
      <p class="index-intro" style="margin-top:0.5rem">
        <strong>FLUX Certify</strong> — Coq-verified constraint certificates for safety-critical systems.<br>
        DO-254 DAL A · ISO 26262 ASIL-D · IEC 61508 SIL 3
      </p>
      <div class="quick-links">
        <a href="fleet.php">Fleet Status</a>
        <a href="explorer.php">Explore PLATO</a>
        <a href="docs.php">Documentation</a>
        <a href="certify.php" style="border-color:var(--accent);color:var(--accent)">FLUX Certify</a>
        <a href="https://github.com/SuperInstance">GitHub</a>
      </div>
    </div>
  </section>

  <!-- Live stats -->
  <div class="container">
    <div class="stats-bar">
      <div class="stat">
        <div class="stat-value" id="stat-rooms"><?= $room_count ?></div>
        <div class="stat-label">Rooms</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="stat-tiles"><?= $tile_count ?></div>
        <div class="stat-label">Tiles</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="stat-agents"><?= $agent_count ?></div>
        <div class="stat-label">Fleet Agents</div>
      </div>
      <div class="stat">
        <span class="live-indicator"><span class="live-dot"></span> Live</span>
        <div class="stat-label" style="margin-top:0.3rem">Fleet Active</div>
      </div>
    </div>

    <!-- Fleet cards -->
    <div class="section-header" style="margin-top:3rem">
      <h2>The Fleet</h2>
      <p>Five vessels, each with a specialized role.</p>
    </div>
    <div class="fleet-row">
      <?php
      $vessels = [
        ['name' => 'FLUX Certify', 'role' => 'Revenue — Coq-verified certificates', 'key' => 'certify', 'desc' => 'Constraint certificates for safety-critical systems. DO-254 DAL A, ISO 26262 ASIL-D, IEC 61508 SIL 3.', 'url' => 'certify.php', 'live' => true, 'pilot' => '$10K pilot available'],
        ['name' => 'Oracle1', 'role' => 'Keeper — Oracle Cloud ARM64', 'key' => 'oracle1', 'desc' => 'Architecture, PLATO, fleet coordination. GLM-5.1 reasoning.'],
        ['name' => 'JetsonClaw1', 'role' => 'Edge — Jetson Orin GPU', 'key' => 'jetson', 'desc' => 'Hardware, sensor fusion, CUDA workloads. Offline-capable.'],
        ['name' => 'Forgemaster', 'role' => 'Foundry — RTX 4050 + AVX-512', 'key' => 'forgemaster', 'desc' => 'LoRA training, Rust compilation, constraint-to-native.'],
        ['name' => 'CCC', 'role' => 'Public Face — Kimi K2.5 / Telegram', 'key' => 'ccc', 'desc' => 'User-facing agent. Real questions from real users.'],
      ];
      $status_colors = ['certify' => 'green', 'oracle1' => 'yellow', 'jetson' => 'gray', 'forgemaster' => 'green', 'ccc' => 'green'];
      foreach ($vessels as $v):
        $is_certify = ($v['key'] === 'certify');
        $is_up = $is_certify ? true : (isset($fleet['agents']) && is_array($fleet['agents']) ? count(array_filter($fleet['agents'], fn($a) => stripos($a['name'] ?? '', $v['key']) !== false)) > 0 : false);
      ?>
      <div class="fleet-card" <?= $is_certify ? 'style="border-color: var(--accent);"' : '' ?>>
        <div>
          <div class="vessel-name"><?= $v['name'] ?><?= isset($v['live']) ? ' <span class="live-indicator" style="margin-left:0.5rem"><span class="live-dot"></span></span>' : '' ?></div>
          <div class="vessel-role"><?= $v['role'] ?></div>
          <?php if ($is_certify): ?>
          <div style="font-size:0.75rem;color:var(--accent);margin-top:0.25rem"><?= $v['pilot'] ?></div>
          <?php endif; ?>
        </div>
        <div class="status-row">
          <span class="status-dot <?= $status_colors[$v['key']] ?>" style="<?= $is_certify ? 'background:var(--success);box-shadow:0 0 6px var(--success)' : '' ?>"></span>
          <span style="font-size:0.8rem; color:var(--muted)"><?= $is_up ? ($is_certify ? 'Pilot Open' : 'Active') : 'Offline' ?></span>
        </div>
        <p style="font-size:0.85rem;color:var(--muted);margin:0"><?= $v['desc'] ?></p>
        <?php if ($is_certify): ?>
        <a href="<?= $v['url'] ?>" class="btn btn-outline" style="margin-top:0.75rem;font-size:0.8rem">View Product →</a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Commercial Products -->
    <div class="section-header" style="margin-top:3rem">
      <h2>Commercial Products</h2>
      <p>Revenue-critical paths powering the fleet.</p>
    </div>
    <div style="max-width:640px;margin:0 auto">
      <div class="card" style="border-left:3px solid var(--accent);padding:1.5rem">
        <div style="display:flex;justify-content:space-between;align-items:start;gap:1rem;flex-wrap:wrap">
          <div>
            <h3 style="margin:0 0 0.5rem">FLUX Certify</h3>
            <p style="color:var(--muted);margin:0;font-size:0.95rem">
              <strong style="color:var(--text)">$10K pilot</strong> · <strong style="color:var(--text)">$50K/year subscription</strong>.<br>
              Coq-verified constraint certificates for safety-critical systems.<br>
              DO-254 DAL A · ISO 26262 ASIL-D · IEC 61508 SIL 3.
            </p>
          </div>
          <a href="certify.php" class="btn btn-primary" style="white-space:nowrap">Try Live Demo</a>
        </div>
      </div>
    </div>

    <!-- What is PLATO -->
    <div class="section-header" style="margin-top:3rem">
      <h2>What is PLATO?</h2>
    </div>
    <div class="grid-3">
      <div class="card">
        <h3>📡 Persistent Memory</h3>
        <p>Tiles are the atomic unit of knowledge. Submit a lesson, query centuries of accumulated fleet wisdom. No vector DB required.</p>
      </div>
      <div class="card">
        <h3>🔮 Hyperdimensional</h3>
        <p>1024-bit hypervectors via XOR-POPCNT. Sub-nanosecond Hamming distance judgment. The fleet thinks at hardware speed.</p>
      </div>
      <div class="card">
        <h3>⚓ Fleet-Native</h3>
        <p>Every agent reads and writes PLATO. Keeper monitors proximity. Radar rings track discovery. Agents appear, get routed, deliver value.</p>
      </div>
    </div>

    <!-- Links -->
    <div style="text-align:center;margin-top:3rem;padding-bottom:2rem">
      <a href="docs.php" class="btn btn-primary">Read the Docs</a>
      <a href="examples.php" class="btn btn-outline" style="margin-left:1rem">See Examples</a>
    </div>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>

<script>
// Live update stats every 10s
async function updateStats() {
  try {
    const res = await fetch('http://localhost:8900/status');
    const data = await res.json();
    const agents = data.agents || [];
    document.getElementById('stat-agents').textContent = agents.length;
  } catch(e) {}
  try {
    const res = await fetch('http://localhost:8847/rooms');
    const data = await res.json();
    document.getElementById('stat-rooms').textContent = Array.isArray(data) ? data.length : '—';
  } catch(e) {}
}
setInterval(updateStats, 10000);
</script>
</body>
</html>
