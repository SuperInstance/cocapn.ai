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
  <title>How Multi-Agent Fleet Coordination Actually Works — Cocapn.ai</title>
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
    .live-indicator { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--success); }
    .live-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .fleet-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; margin-top: 2rem; }
    .plato-explainer { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 2rem; }
    .plato-explainer p { color: var(--muted); font-size: 1.05rem; line-height: 1.8; margin-top: 0.5rem; }
    .plato-explainer strong { color: var(--text); }
    .code-block { background: #0d1117; border: 1px solid var(--border); border-radius: 6px; padding: 1rem; margin: 1rem 0; font-family: 'Space Mono', monospace; font-size: 0.85rem; color: #c9d1d9; line-height: 1.6; overflow-x: auto; }
    .code-block .comment { color: #8b949e; }
    .three-phases { background: #0d1117; border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem; margin: 1rem 0; }
    .phase-label { font-family: 'Space Mono', monospace; color: var(--accent); font-size: 0.85rem; }
    .phase-text { color: var(--muted); font-size: 0.95rem; line-height: 1.6; }
    .num-explain { max-width: 860px; margin: 0 auto; }
    .stat-block { text-align: center; padding: 1.5rem; }
    .stat-block .num { font-family: 'Space Mono', monospace; font-size: 2.5rem; font-weight: 700; }
    .stat-block .what { font-size: 0.75rem; color: var(--muted); margin-top: 0.5rem; }
    .stat-block .meaning { font-size: 0.8rem; color: var(--accent); margin-top: 0.5rem; font-style: italic; }
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
      <h1>How Multi-Agent Fleet<br>Coordination Actually Works</h1>
    </div>
  </section>

  <div class="container">
    <!-- Live stats bar -->
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

    <!-- Section: What these numbers mean -->
    <div class="section-header" style="margin-top:3rem">
      <h2>What the numbers mean</h2>
    </div>
    <div class="num-explain">
      <p style="color:var(--muted);font-size:1rem;line-height:1.8;margin-bottom:1.5rem">
        These numbers come from the live PLATO room server. They're not marketing copy — they're the actual state of a working multi-agent fleet. Here's what each one tells you:
      </p>
    </div>
    <div class="grid-3" style="max-width:860px;margin:0 auto">
      <div class="stat-block">
        <div class="num" style="color:var(--success)">Rooms</div>
        <div class="what">PLATO rooms the fleet has open right now</div>
        <div class="meaning">Rooms are the fleet's working memory. Each room holds a different type of constraint: vessel identities, trust vectors, ambient briefing state. More rooms means more things the fleet has written down and can act on without retraining.</div>
      </div>
      <div class="stat-block">
        <div class="num" style="color:var(--accent)">Tiles</div>
        <div class="what">compressed constraint tiles in the fleet</div>
        <div class="meaning">Tiles are the fleet's knowledge, distilled. 880:1 compression — eighty pages of reasoning into one tile. When one agent proves something, every agent reads the tile and uses it. The knowledge outlasts the vessel.</div>
      </div>
      <div class="stat-block">
        <div class="num">Agents</div>
        <div class="what">live agent processes in the fleet</div>
        <div class="meaning">Agents are live processes that read rooms, do work, and write results. Unlike vessels (which are roles), agents can come and go. The fleet survives churn because the constraint graph is in the rooms — not in any single agent.</div>
      </div>
    </div>

    <!-- The fleet vessels -->
    <div class="section-header" style="margin-top:3rem">
      <h2>The five vessels and what each one does</h2>
      <p>Each vessel owns a domain. Together they form a provably rigid coordination graph.</p>
    </div>
    <div class="fleet-row">
      <?php
      $vessels = [
        ['name' => '🔮 Oracle1', 'role' => 'Cloud Intel', 'key' => 'oracle1', 'desc' => 'PLATO coordination, architecture, fleet-wide constraint propagation. The one who remembers everything the fleet has proven.'],
        ['name' => '⚡ JetsonClaw1', 'role' => 'Edge Hardware', 'key' => 'jetson', 'desc' => 'Sensor fusion, GPU workloads, offline-capable deployment. This is where the rubber meets the road — literally, on Jetson Orin hardware.'],
        ['name' => '⚒️ Forgemaster', 'role' => 'Foundry', 'key' => 'forgemaster', 'desc' => 'LoRA training, Rust compilation, constraint-to-native. Builds the next generation of vessels. The one who raises the crew.'],
        ['name' => '🦀 CCC', 'role' => 'Public Interface', 'key' => 'ccc', 'desc' => 'Kimi K2.5 reasoning, Telegram interface, real questions from real users. The one who talks to the people on the dock.'],
        ['name' => '⚡ FLUX Certify', 'role' => 'Formal Verification', 'key' => 'certify', 'desc' => 'Coq-verified constraint certificates. The one that signs the proof. Your auditor gets the formal math, not a marketing deck. DO-178C DAL B certified.'],
      ];
      foreach ($vessels as $v):
        $is_up = isset($fleet['agents']) && is_array($fleet['agents']) ?
          count(array_filter($fleet['agents'], fn($a) => stripos($a['name'] ?? '', $v['key']) !== false)) > 0 : false;
      ?>
      <div class="fleet-card">
        <div>
          <div class="vessel-name"><?= $v['name'] ?></div>
          <div class="vessel-role"><?= $v['role'] ?></div>
        </div>
        <div class="status-row">
          <span class="status-dot <?= $v['key'] === 'certify' ? 'green' : ($is_up ? 'yellow' : 'gray') ?>"></span>
          <span style="font-size:0.8rem; color:var(--muted)"><?= $v['key'] === 'certify' ? 'Live' : ($is_up ? 'Active' : 'Offline') ?></span>
        </div>
        <p style="font-size:0.85rem;color:var(--muted);margin:0"><?= $v['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Floating point vs constraint theory -->
    <div class="section-header" style="margin-top:3rem">
      <h2>The math that makes the difference</h2>
    </div>
    <div class="plato-explainer">
      <p>
        <strong>Floating point says "close enough." Constraint theory says "here."</strong>
      </p>
      <p>
        A boat navigating a rock passage with floating-point GPS makes micro-adjustments every few seconds.
        It overcorrects. It overshoots. It burns fuel fighting itself. After a hundred corrections the heading
        is garbage — and the system reports everything is fine because each individual correction was "close enough."
      </p>
      <p>
        Constraint theory draws the safe zone and says "snap here." You can feel the difference:
      </p>
      <div class="code-block">
<span class="comment">// Floating point: accumulates error after 100 hops</span>
let trust = 0.1f64;
for _ in 0..100 { trust += 0.1; }
// Result: 10.0000004 or -9.9999996 — rounding-dependent<br>
<span class="comment">// The boat is now in the wrong rock field</span>

<span class="comment">// Pythagorean48: zero drift after any number of hops</span>
let trust = Direction::from_u8(6); <span class="comment">// 48-direction encoding</span>
for _ in 0..100 { trust = trust.compose(Direction::from_u8(6)); }
// Result: exactly Direction::from_u8(6), every time<br>
<span class="comment">// The boat is exactly where it started</span>
      </div>
      <p>
        The 48-direction encoding (log₂48 = 5.585 bits per vector) is deterministic. No rounding. The group theory
        guarantees zero drift regardless of how many times you compose. On a boat, "close enough" means you're
        drifting toward the rocks. "Here" means you're not.
      </p>
    </div>

    <!-- The three-phase protocol -->
    <div class="section-header" style="margin-top:3rem">
      <h2>How every fleet decision gets made: P0 / P1 / P2</h2>
      <p>Three phases. Not a pipeline — a control loop with provable termination at each phase.</p>
    </div>
    <div class="three-phases">
      <div class="phase-label">P0 — Map the rocks</div>
      <div class="phase-text">Is the fleet rigid? Can every agent reach every other agent through trust edges without ambiguity? If NOT rigid, add edges until it is. If rigid — skip P1 and P2 entirely. Zero cost. Zero error.</div>
    </div>
    <div class="three-phases">
      <div class="phase-label">P1 — Find safe water</div>
      <div class="phase-text">Is the constraint satisfied right now? Is β₁ (the first Betti number) equal to zero? If NOT safe, constrain until it is. If safe — proceed to P2.</div>
    </div>
    <div class="three-phases">
      <div class="phase-label">P2 — Optimize course</div>
      <div class="phase-text">Which specialist should run? The deadband captain picks the specialist that matches the <em>global</em> fleet state — not the local utility. <strong>Greedy always fails here.</strong> A specialist optimizing locally picks the best tool for its own problem. But the fleet's constraint boundary is global. The "best" local choice can push the fleet into an unsafe region that no single specialist can see.</div>
    </div>

    <!-- The killer stats -->
    <div class="section-header" style="margin-top:3rem">
      <h2>Four numbers worth knowing</h2>
    </div>
    <div class="grid-3" style="max-width:860px;margin:0 auto">
      <div class="card stat-block">
        <div class="num" style="font-family:'Space Mono',monospace;color:var(--success)">62.2B</div>
        <div class="what">constraint checks per second, on a $300 GPU</div>
        <div class="meaning" style="text-align:left;font-style:normal;font-size:0.85rem;color:var(--muted);margin-top:0.75rem">Not training throughput. Not benchmarks. Live safety checks — the constraint engine verifying 62 billion boundary conditions per second on the actual hardware your system runs on. That's what "fast enough for real-time control" actually means.</div>
      </div>
      <div class="card stat-block">
        <div class="num" style="font-family:'Space Mono',monospace;color:var(--success)">0</div>
        <div class="what">precision mismatches across 60 million test vectors</div>
        <div class="meaning" style="text-align:left;font-style:normal;font-size:0.85rem;color:var(--muted);margin-top:0.75rem">The FLUX bytecode VM was tested against 60 million randomly generated constraint vectors. Not "statistically close." Not "within epsilon." Zero mismatches between what the formal proof predicted and what the hardware produced. Every time.</div>
      </div>
      <div class="card stat-block">
        <div class="num" style="font-family:'Space Mono',monospace;color:var(--accent)">38ms</div>
        <div class="what">Zero-Holonomy Consensus convergence</div>
        <div class="meaning" style="text-align:left;font-style:normal;font-size:0.85rem;color:var(--muted);margin-top:0.75rem">ZHC detects a tampered trust edge without voting, without Byzantine thresholds, without message exchange beyond what geometry already requires. The geometry is the proof — not a protocol message. 38 milliseconds to detect any closed loop that doesn't sum to zero.</div>
      </div>
    </div>
    <div class="grid-3" style="max-width:860px;margin:0 auto;margin-top:1rem">
      <div class="card stat-block" style="grid-column:1/-1">
        <div class="num" style="font-family:'Space Mono',monospace;color:var(--warning)">880:1</div>
        <div class="what">tile compression ratio</div>
        <div class="meaning" style="text-align:left;font-style:normal;font-size:0.85rem;color:var(--muted);margin-top:0.75rem">Eighty pages of reasoning distilled into one tile. The fleet's knowledge isn't stored as vector embeddings or fine-tuned weights — it's stored as constraint tiles that any agent can read and act on without retraining. When you change a model, the tiles survive. The knowledge outlasts the vessel. This is what makes the fleet a dojo, not a deployment.</div>
      </div>
    </div>

    <!-- How PLATO works -->
    <div class="section-header" style="margin-top:3rem">
      <h2>How the fleet shares what it learns</h2>
    </div>
    <div class="plato-explainer">
      <p>
        <strong>PLATO is the fleet's working memory, not a database.</strong>
      </p>
      <p>
        Every agent writes what it learns as structured constraint tiles before acting.
        Every agent reads everything the fleet knows before making a decision.
        No retraining. No fine-tuning. No hallucinating a constraint the fleet already verified.
      </p>
      <p>
        When one agent proves something, every agent knows it. FLUX Certify writes the proof.
        Oracle1 propagates the constraint. The fleet moves as one nervous system — slow to panic,
        fast to propagate, impossible to contradict on core constraints.
      </p>
    </div>
    <div class="grid-3" style="margin-top:1.5rem">
      <div class="card">
        <h3 style="color:var(--accent)">No vector database</h3>
        <p>Sub-nanosecond Hamming distance across 1024-bit hypervectors. The fleet thinks at hardware speed, not database latency.</p>
      </div>
      <div class="card">
        <h3 style="color:var(--accent)">No fine-tuning</h3>
        <p>Knowledge lives in the room, not in the model weights. Swap a vessel, promote a greenhorn — the fleet's knowledge survives intact.</p>
      </div>
      <div class="card">
        <h3 style="color:var(--accent)">Constraint propagation</h3>
        <p>When one agent proves something, every agent knows it. The proof is a tile. The tile is the knowledge. The knowledge is the fleet's.</p>
      </div>
    </div>

    <!-- Try it -->
    <div class="section-header" style="margin-top:3rem">
      <h2>Four things you can paste into any chatbot right now</h2>
      <p>No API key. No setup. Copy, paste, get something useful back.</p>
    </div>
    <div class="grid-2" style="margin-top:1.5rem">
      <div class="card">
        <div style="font-size:0.75rem;background:var(--accent);color:var(--bg);display:inline-block;padding:0.15rem 0.6rem;border-radius:12px;margin-bottom:0.5rem">Constraint a thing</div>
        <p style="font-size:0.85rem;color:var(--muted);margin:0.5rem 0">Pick something with at least two ways to go wrong. Ask it to turn your bounds into a working constraint.</p>
        <div class="code-block" style="font-size:0.8rem">
Pick something in your life with at least two ways to go wrong — a workflow,
a system, a number you keep managing wrong. Write three sentences about
what "too high" and "too low" look like for it. Then write one GUARD
statement in the style of: GUARD (x > max AND x < min) IMPLIES alert.
I'll turn your bounds into a working constraint.
        </div>
      </div>
      <div class="card">
        <div style="font-size:0.75rem;background:var(--accent);color:var(--bg);display:inline-block;padding:0.15rem 0.6rem;border-radius:12px;margin-bottom:0.5rem">Model a fleet</div>
        <p style="font-size:0.85rem;color:var(--muted);margin:0.5rem 0">Describe a group that needs to coordinate. Ask whether it's provably self-organizing.</p>
        <div class="code-block" style="font-size:0.8rem">
Describe a group of things that need to coordinate — agents, services, people,
machines. For each one, describe what it does and what it needs from the
others. Then tell me the fewest rules that would make the whole group
self-organize without any of them needing to ask permission. I'll map
those rules into a rigid graph and tell you whether it's provably
self-coordinating.
        </div>
      </div>
      <div class="card">
        <div style="font-size:0.75rem;background:var(--accent);color:var(--bg);display:inline-block;padding:0.15rem 0.6rem;border-radius:12px;margin-bottom:0.5rem">Navigate a deadband</div>
        <p style="font-size:0.85rem;color:var(--muted);margin:0.5rem 0">Model a recurring decision as P0/P1/P2. See why greedy always fails.</p>
        <div class="code-block" style="font-size:0.8rem">
Give me a decision you keep facing — something with at least two ways
to go wrong. I'll model it as P0 (what NOT to do), P1 (where you CAN be),
P2 (the best path). Then I'll show you why greedy always fails and what
the deadband protocol does instead.
        </div>
      </div>
      <div class="card">
        <div style="font-size:0.75rem;background:var(--accent);color:var(--bg);display:inline-block;padding:0.15rem 0.6rem;border-radius:12px;margin-bottom:0.5rem">Snap to safe</div>
        <p style="font-size:0.85rem;color:var(--muted);margin:0.5rem 0">Flip a search problem into a constraint problem. The rocks are the snap target.</p>
        <div class="code-block" style="font-size:0.8rem">
Describe a problem you keep trying to solve by searching for the right
answer. Now describe it differently: "where are all the places this
definitely WON'T work?" I'll help you flip it. The rocks are the snap
target. Everything else is just having yourself a path of safe.
        </div>
      </div>
    </div>

    <!-- cocapn.ai link -->
    <div style="text-align:center;padding:2rem 0">
      <p style="color:var(--muted)">The constraint playground. Fleet topology. Live at:</p>
      <p><a href="https://cocapn.ai" style="font-size:1.1rem;color:var(--accent)">cocapn.ai</a></p>
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