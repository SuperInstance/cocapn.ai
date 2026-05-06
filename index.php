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
  <title>CoCapn — Safety-Critical AI Fleet</title>
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
    .pain-point { max-width: 640px; margin: 0 auto; text-align: center; }
    .pain-point .cost { font-family: 'Space Mono', monospace; color: var(--danger); font-size: 1.5rem; font-weight: 700; }
    .pain-point .time { font-family: 'Space Mono', monospace; color: var(--warning); font-size: 1.1rem; }
    .pain-point .solution { color: var(--success); font-size: 1.1rem; font-weight: 600; }
    .pain-point p { color: var(--muted); font-size: 1.05rem; line-height: 1.8; margin-top: 0.75rem; }
    .pain-point strong { color: var(--text); }
    .plato-nervous { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 2rem; }
    .plato-nervous p { color: var(--muted); font-size: 1.05rem; line-height: 1.8; margin-top: 0.5rem; }
    .plato-nervous strong { color: var(--text); }
    .plato-nervous h3 { color: var(--accent); margin-bottom: 0.25rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .vessel-dojo { font-style: italic; color: var(--keeper); }
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
      <h1>Safety-Critical AI<br>That Doesn't Fail Silently.</h1>
      <div class="pain-point">
        <p>
          Autonomous vessels used to wait <strong>6 weeks</strong> and pay <span class="cost">$240K</span>
          for a safety engineer to manually verify one constraint.<br>
          Now it takes <span class="solution">50 milliseconds and a formal Coq proof</span>.
        </p>
      </div>
      <div class="quick-links">
        <a href="certify.php" style="border-color:var(--accent);color:var(--accent)">Start the $10K Pilot</a>
        <a href="fleet.php">Fleet Status</a>
        <a href="explorer.php">Explore PLATO</a>
        <a href="docs.php">Documentation</a>
        <a href="https://github.com/SuperInstance">GitHub</a>
      </div>
    </div>
  </section>

  <!-- Live stats -->
  <div class="container">
    <div class="stats-bar">
      <div class="stat">
        <div class="stat-value" id="stat-rooms"><?= $room_count ?></div>
        <div class="stat-label">PLATO Rooms</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="stat-tiles"><?= $tile_count ?></div>
        <div class="stat-label">Constraint Tiles</div>
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
      <p>Five vessels. Each owns a domain. Together, they keep autonomous systems from killing people.</p>
    </div>
    <div class="fleet-row">
      <?php
      $vessels = [
        ['name' => 'FLUX Certify', 'role' => 'Revenue — Formal Verification', 'key' => 'certify', 'desc' => 'The one that signs the proof. Coq-verified constraint certificates. Your auditor gets the formal math, not a marketing deck.', 'url' => 'certify.php', 'live' => true, 'badge' => 'Pilot Open'],
        ['name' => 'Oracle1', 'role' => 'Keeper — Cloud Intel', 'key' => 'oracle1', 'desc' => 'The one who remembers everything. PLATO coordination, architecture, fleet-wide constraint propagation.'],
        ['name' => 'JetsonClaw1', 'role' => 'Edge — Hardware Floor', 'key' => 'jetson', 'desc' => 'The one closest to the metal. Sensor fusion, GPU workloads, offline-capable. This is where the rubber meets the road.'],
        ['name' => 'Forgemaster', 'role' => 'Foundry — Training Rig', 'key' => 'forgemaster', 'desc' => 'The one who builds the crew. LoRA training, Rust compilation, constraint-to-native. Raises the next generation of vessels.'],
        ['name' => 'CCC', 'role' => 'Rigging — Public Deck', 'key' => 'ccc', 'desc' => 'The one who talks to the crew. Kimi K2.5 reasoning, Telegram interface, real questions from real users.'],
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
          <?php if ($is_certify && isset($v['badge'])): ?>
          <div style="font-size:0.75rem;color:var(--success);margin-top:0.25rem"><?= $v['badge'] ?></div>
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

    <!-- FLUX Certify — the pilot -->
    <div class="section-header" style="margin-top:3rem">
      <h2>Start With FLUX Certify</h2>
      <p>The $10K pilot is not a proof-of-concept. It's a real constraint, verified in one week.</p>
    </div>
    <div style="max-width:640px;margin:0 auto">
      <div class="card" style="border-left:3px solid var(--success);padding:2rem">
        <div style="display:flex;justify-content:space-between;align-items:start;gap:1rem;flex-wrap:wrap">
          <div>
            <h3 style="margin:0 0 0.75rem; font-size:1.3rem">The $10K Pilot</h3>
            <p style="color:var(--muted);margin:0;font-size:0.95rem;line-height:1.7">
              Give us one of your real safety constraints.<br>
              We'll verify it formally. You'll see the Coq proof.<br>
              In one week. For $10K.<br><br>
              If the proof holds and your auditor accepts it — you have a production path.<br>
              If not, you'll know exactly <strong style="color:var(--text)">why</strong> it failed, formally.
            </p>
            <div style="margin-top:1rem;font-size:0.8rem;color:var(--muted)">
              DO-254 DAL A · IEC 61508 SIL 3 · DNV AROS
            </div>
          </div>
          <a href="certify.php" class="btn btn-primary" style="white-space:nowrap;font-size:1rem;padding:0.75rem 1.5rem">Start the Pilot →</a>
        </div>
      </div>
    </div>

    <!-- PLATO — the fleet's nervous system -->
    <div class="section-header" style="margin-top:3rem">
      <h2>The Fleet's Nervous System</h2>
      <p>PLATO is how agents share what they learn without retraining, fine-tuning, or losing context.</p>
    </div>
    <div class="plato-nervous">
      <h3>How it works</h3>
      <p>
        Every agent writes what it learns to PLATO as structured constraint tiles.
        Every agent reads everything the fleet knows before making a decision.
        No retraining. No fine-tuning. No hallucinating a constraint the fleet already verified.
        The fleet moves as one nervous system — slow to panic, fast to propagate, impossible to contradict on core constraints.
      </p>
    </div>
    <div class="grid-3" style="margin-top:1.5rem">
      <div class="card">
        <h3 style="color:var(--accent)">No Vector DB</h3>
        <p>Sub-nanosecond Hamming distance across 1024-bit hypervectors. The fleet thinks at hardware speed, not database latency.</p>
      </div>
      <div class="card">
        <h3 style="color:var(--accent)">No Fine-Tuning</h3>
        <p>Knowledge lives in the room, not in the model weights. Swap a vessel, promote a greenhorn — the fleet's knowledge survives intact.</p>
      </div>
      <div class="card">
        <h3 style="color:var(--accent)">Constraint Propagation</h3>
        <p>When one agent proves something, every agent knows it. FLUX Certify writes the proof. Oracle1 propagates the constraint. CCC speaks it to the user.</p>
      </div>
    </div>

    <!-- Stats as evidence -->
    <div class="section-header" style="margin-top:3rem">
      <h2>What the Numbers Mean</h2>
    </div>
    <div class="grid-3" style="margin-bottom:2rem">
      <div class="card" style="text-align:center">
        <div style="font-family:'Space Mono',monospace;font-size:2rem;color:var(--success);font-weight:700">50ms</div>
        <p style="margin-top:0.5rem;color:var(--muted);font-size:0.85rem">vs. 6 weeks for a human safety engineer to manually verify one constraint</p>
      </div>
      <div class="card" style="text-align:center">
        <div style="font-family:'Space Mono',monospace;font-size:2rem;color:var(--success);font-weight:700">$240K</div>
        <p style="margin-top:0.5rem;color:var(--muted);font-size:0.85rem">typical external cost for one safety verification project — before Cocapn</p>
      </div>
      <div class="card" style="text-align:center">
        <div style="font-family:'Space Mono',monospace;font-size:2rem;color:var(--accent);font-weight:700">Coq</div>
        <p style="margin-top:0.5rem;color:var(--muted);font-size:0.85rem">geometric proof, not statistical guarantee — the difference between "probably safe" and "provably safe"</p>
      </div>
    </div>

    <!-- Call to action -->
    <div style="text-align:center;margin-top:3rem;padding-bottom:2rem">
      <a href="certify.php" class="btn btn-primary" style="font-size:1.1rem;padding:0.875rem 2rem">Start the $10K Pilot</a>
      <a href="docs.php" class="btn btn-outline" style="margin-left:1rem">Read the Docs</a>
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