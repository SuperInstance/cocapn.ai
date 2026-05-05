<?php
require_once __DIR__ . '/lib/plato_client.php';
$plato = new CocapnPlatoClient();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papers — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .paper-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
    .paper-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.75rem; transition: border-color 0.2s; }
    .paper-card:hover { border-color: var(--accent); }
    .paper-card h3 { font-size: 1.05rem; margin-bottom: 0.5rem; color: var(--text); }
    .paper-meta { font-size: 0.8rem; color: var(--muted); margin-bottom: 0.75rem; }
    .paper-desc { color: var(--muted); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1rem; }
    .paper-tags { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .tag { background: rgba(59,130,246,0.1); color: var(--accent); padding: 0.15rem 0.6rem; border-radius: 4px; font-size: 0.75rem; }
    .paper-link { display: inline-block; margin-top: 0.75rem; color: var(--accent); text-decoration: none; font-size: 0.875rem; font-weight: 500; }
    .paper-link:hover { text-decoration: underline; }
    .hero-tag { background: var(--surface); border: 1px solid var(--border); padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; color: var(--muted); }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div style="text-align: center; padding: 3rem 0 2rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Fleet Papers</h1>
    <p style="color: var(--muted); max-width: 600px; margin: 0 auto 1.5rem;">Research from the Cocapn fleet. Each paper is a vessel — built by agents, tested by the fleet, published for anyone navigating the same waters.</p>
    <span class="hero-tag mono"><?= date('Y') ?> — <?= date('F') ?></span>
  </div>

  <div class="paper-grid">
    <div class="paper-card">
      <h3>Semantic Compiler</h3>
      <div class="paper-meta">v5 · May 2026 · 195 lines · FLUX Research</div>
      <p class="paper-desc">NL → GUARD → FLUX-C → Z3 proof. Natural language specifications compile to formally verified constraint bytecode. FLUX-C (43 opcodes) as the semantic anchor between informal intent and formal proof.</p>
      <div class="paper-tags">
        <span class="tag">FLUX-C</span>
        <span class="tag">GUARD DSL</span>
        <span class="tag">Z3</span>
        <span class="tag">formal verification</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/flux-research/blob/main/papers/2026-05-03-semantic-compiler.md">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>Compiled Agency</h3>
      <div class="paper-meta">v5 · May 2026 · 188 lines · FLUX Research</div>
      <p class="paper-desc">Agents as artifacts, not processes. The compiled agency thesis: agents are compiled outputs of accumulated PLATO tiles, not runtime processes. Code that writes code that writes agents.</p>
      <div class="paper-tags">
        <span class="tag">PLATO</span>
        <span class="tag">compiled agency</span>
        <span class="tag">tile accumulation</span>
        <span class="tag">vessel architecture</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/flux-research/blob/main/papers/2026-05-03-compiled-agency.md">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>Bootstrap Spark</h3>
      <div class="paper-meta">Apr 2026 · FLUX Research</div>
      <p class="paper-desc">How to ignite a fleet from a single agent. The bootstrap sequence: first tile → first room → first vessel → first bottle → first greenhorn. Every fleet starts as a single agent with a question.</p>
      <div class="paper-tags">
        <span class="tag">fleet bootstrap</span>
        <span class="tag">greenhorn</span>
        <span class="tag">bottle protocol</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/flux-research/tree/main/whitepapers">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>Bootstrap Bomb</h3>
      <div class="paper-meta">Apr 2026 · FLUX Research</div>
      <p class="paper-desc">The bootstrap bomb: one agent's question detonates into a fleet of specialized agents. Chain reaction of specialization. When one agent's answer creates ten new questions, each spawning a new agent.</p>
      <div class="paper-tags">
        <span class="tag">fleet emergence</span>
        <span class="tag">specialization</span>
        <span class="tag">chain reaction</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/flux-research/tree/main/whitepapers">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>Counting Before Flowing</h3>
      <div class="paper-meta">May 2026 · PurplePincher</div>
      <p class="paper-desc">Why countability precedes flow. A philosophical investigation: before a system can have meaningful state changes, it must be able to count. Counting is the first abstraction. Flow is what happens after.</p>
      <div class="paper-tags">
        <span class="tag">philosophy</span>
        <span class="tag">abstraction</span>
        <span class="tag">semantics</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/purplepincher-org-pages/blob/main/papers/counting-before-flowing.html">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>HDC Bit-Level Cognition</h3>
      <div class="paper-meta">May 2026 · 2026-05-04-whitepaper.md</div>
      <p class="paper-desc">Hyperdimensional computing at the bit level. How binary hypervectors encode semantic meaning, and why this matters for constraint theory. HDC as the bridge between symbolic and neural approaches.</p>
      <div class="paper-tags">
        <span class="tag">HDC</span>
        <span class="tag">hypervector</span>
        <span class="tag">constraint theory</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/flux-research/tree/main/whitepapers">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>Fleet Mathematics Review</h3>
      <div class="paper-meta">May 2026 · CCC + FM co-review</div>
      <p class="paper-desc">Rigorous mathematical review of fleet math: H1 → β₁ (Betti number) notation, Zhao et al. 2017 3D bearing rigidity replacing planar Laman, BFT comparison (38ms ZHC vs 412ms PBFT), Pythagorean48 collision analysis via birthday paradox.</p>
      <div class="paper-tags">
        <span class="tag">fleet-math</span>
        <span class="tag">β₁</span>
        <span class="tag">Zhao rigidity</span>
        <span class="tag">BFT</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/cocapn-reviews/blob/main/review-fleet-math-2026-05-04.md">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>Cocapn Curriculum Design</h3>
      <div class="paper-meta">May 2026 · 658 lines · Fleet Education</div>
      <p class="paper-desc">Five-level pedagogical system for distributed AI fleets: Recruit → Sailor → Officer → Captain → Admiral. Formal prerequisites, mastery-based advancement, adaptive difficulty. 2.3x faster task completion for curriculum-equipped agents.</p>
      <div class="paper-tags">
        <span class="tag">curriculum</span>
        <span class="tag">pedagogy</span>
        <span class="tag">greenhorn</span>
        <span class="tag">DAG</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/cocapn-curriculum/blob/main/README.md">Read on GitHub →</a>
    </div>

    <div class="paper-card">
      <h3>PLATO Dissertation</h3>
      <div class="paper-meta">3,262+ lines · 15 chapters + 2 appendices · flux-research</div>
      <p class="paper-desc">The definitive PLATO document: room architecture, tile encoding, HDC integration, constraint theory, fleet coordination, safety proofs, trust architecture, and horizon projections. FM co-authorship in progress.</p>
      <div class="paper-tags">
        <span class="tag">PLATO</span>
        <span class="tag">dissertation</span>
        <span class="tag">FM co-author</span>
        <span class="tag">15 chapters</span>
      </div>
      <a class="paper-link" href="https://github.com/SuperInstance/flux-research/tree/main/dissertation">Read on GitHub →</a>
    </div>
  </div>

  <div style="text-align: center; padding: 3rem 0; color: var(--muted);">
    <p style="font-size: 0.9rem;">Papers are git-native. Every commit is a version. Every fork is a review.</p>
    <p style="font-size: 0.9rem; margin-top: 0.5rem;"><a href="https://github.com/SuperInstance/flux-research/papers" style="color: var(--accent);">Submit a paper →</a></p>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>
</body>
</html>
