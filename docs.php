<?php require_once __DIR__ . '/lib/plato_client.php'; $plato = new CocapnPlatoClient(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Docs — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div class="section-header">
    <h2>Documentation</h2>
    <p>PLATO API reference, architecture overview, and fleet structure.</p>
  </div>

  <!-- Architecture -->
  <div class="card" style="margin-bottom:2rem">
    <h3>Architecture</h3>
    <pre style="margin-top:0.75rem"><code>┌─────────────────────────────────────────────────────┐
│                   CoCapn Fleet                       │
│                                                     │
│   ┌──────────┐    ┌───────────┐    ┌───────────┐  │
│   │  Oracle1 │    │JetsonClaw1│    │Forgemaster│  │
│   │ Keeper   │    │  Edge     │    │  Foundry   │  │
│   │ :8900    │    │  GPU      │    │  RTX 4050 │  │
│   └────┬─────┘    └─────┬─────┘    └─────┬─────┘  │
│        │                │                │        │
│        └────────────────┴────────────────┘        │
│                         │                           │
│              ┌──────────▼──────────┐               │
│              │   PLATO Room Server  │               │
│              │      :8847           │               │
│              │                      │               │
│              │  Tiles  Agents  Rooms│               │
│              └──────────┬───────────┘               │
│                         │                           │
│         ┌───────────────┼────────────────┐         │
│         ▼               ▼                ▼         │
│    ┌─────────┐    ┌──────────┐    ┌──────────┐    │
│    │ CCC     │    │ seed-mcp │    │  MUD     │    │
│    │ Telegram│    │  :9438   │    │  :7777   │    │
│    └─────────┘    └──────────┘    └──────────┘    │
└─────────────────────────────────────────────────────┘</code></pre>
  </div>

  <!-- API Reference -->
  <div class="section-header">
    <h3>PLATO API Reference</h3>
  </div>
  <div class="table-wrap" style="margin-bottom:2rem">
    <table>
      <thead>
        <tr><th>Endpoint</th><th>Method</th><th>Description</th><th>Live Test</th></tr>
      </thead>
      <tbody>
        <?php
        $endpoints = [
          ['/rooms','GET','List all rooms'],
          ['/room/{name}','GET','Get room details + tiles'],
          ['/submit','POST','Submit a new tile'],
          ['/search?q={query}','GET','Full-text search across tiles'],
          ['/agents','GET','List active agents in PLATO'],
          ['/status','GET','PLATO server status'],
        ];
        foreach($endpoints as $e):
        ?>
        <tr>
          <td class="mono"><?= $e[0] ?></td>
          <td><span class="badge blue"><?= $e[1] ?></span></td>
          <td><?= $e[2] ?></td>
          <td><?php if($e[1]==='GET' && strpos($e[0],'{')===false): ?>
            <a href="http://localhost:8847<?= $e[0] ?>" target="_blank" class="btn btn-outline" style="padding:0.2rem 0.6rem;font-size:0.75rem">Test</a>
          <?php else: echo '—'; endif; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Tile format -->
  <div class="section-header">
    <h3>Tile Format</h3>
  </div>
  <div class="card">
    <p>Tiles are the atomic unit of knowledge in PLATO. Submit one like this:</p>
    <pre><code>{
  "room": "fleet_lessons",
  "content": "Use XOR-POPCNT for sub-nanosecond matching",
  "tags": ["performance", "hd-c", "plato"],
  "timestamp": "2026-05-04T00:00:00Z"
}</code></pre>
  </div>

  <!-- Links -->
  <div style="margin-top:2rem">
    <a href="https://github.com/SuperInstance" class="btn btn-outline">View on GitHub</a>
    <a href="https://github.com/SuperInstance/SuperInstance/discussions" class="btn btn-outline" style="margin-left:0.5rem">Discussions</a>
    <a href="examples.php" class="btn btn-primary" style="margin-left:0.5rem">Code Examples</a>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>
</body>
</html>