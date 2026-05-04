<?php
require_once __DIR__ . '/lib/plato_client.php';
$plato = new CocapnPlatoClient();
$rooms = $plato->get_all_rooms() ?: [];
$selected = $_GET['room'] ?? ($rooms[0]['name'] ?? '');
$room_data = $selected ? $plato->get_room($selected) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PLATO Explorer — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div class="section-header">
    <h2>PLATO Explorer</h2>
    <p>Browse rooms, view tiles, submit knowledge. Live from the room server at :8847</p>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:1rem">
    <div style="display:flex;gap:0.75rem;align-items:center">
      <label style="margin:0;color:var(--muted);font-size:0.875rem">Room:</label>
      <form method="GET" style="display:flex">
        <select name="room" onchange="this.form.submit()" style="width:auto">
          <?php foreach($rooms as $r): ?>
          <?php $rname = is_array($r) ? ($r['name'] ?? $r) : $r; ?>
          <option value="<?= htmlspecialchars($rname) ?>" <?= $rname === $selected ? 'selected' : '' ?>><?= htmlspecialchars($rname) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
    <form method="GET" style="display:flex;gap:0.5rem">
      <input name="q" placeholder="Search tiles..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="width:220px">
      <button type="submit" class="btn btn-outline" style="padding:0.5rem 1rem">Search</button>
    </form>
  </div>

  <div class="explorer-layout">
    <!-- Sidebar: room list -->
    <div class="room-sidebar">
      <h4>Rooms (<?= count($rooms) ?>)</h4>
      <?php foreach($rooms as $r): ?>
      <?php $rname = is_array($r) ? ($r['name'] ?? $r) : $r; ?>
      <div class="room-item <?= $rname === $selected ? 'active' : '' ?>">
        <a href="?room=<?= urlencode($rname) ?>" style="color:inherit;text-decoration:none">
          <?= htmlspecialchars($rname) ?>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Main: room detail -->
    <div class="room-detail">
      <?php if (!$selected): ?>
      <div class="alert alert-info">Select a room to view its tiles.</div>
      <?php elseif (empty($room_data)): ?>
      <div class="alert alert-error">Room "<?= htmlspecialchars($selected) ?>" returned no data.</div>
      <?php else: ?>
      <h3 style="margin-bottom:1rem"><?= htmlspecialchars($selected) ?></h3>

      <?php
      $tiles = $room_data['tiles'] ?? [];
      if (!is_array($tiles)) $tiles = [];
      ?>

      <?php if (!empty($room_data['agents'])): ?>
      <div style="margin-bottom:1rem">
        <span class="badge blue">Agents: <?= count($room_data['agents']) ?></span>
        <span class="badge gray" style="margin-left:0.3rem">Tiles: <?= count($tiles) ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($tiles)): ?>
      <div class="tile-list">
        <?php foreach(array_slice($tiles, -10) as $tile): ?>
        <div class="tile-item">
          <div class="tile-meta">
            <?php if (isset($tile['id'])): ?><span class="mono">#<?= htmlspecialchars($tile['id']) ?></span><?php endif; ?>
            <?php if (isset($tile['timestamp'])): ?><span style="margin-left:0.5rem;color:var(--muted)"><?= htmlspecialchars($tile['timestamp']) ?></span><?php endif; ?>
          </div>
          <div class="tile-content"><?= htmlspecialchars(substr($tile['question'] ?? $tile['answer'] ?? '', 0, 200) ?? json_encode($tile)) ?></div>
          <?php if (!empty($tile['tags'])): ?>
          <div style="margin-top:0.4rem">
            <?php foreach((array)$tile['tags'] as $tag): ?>
            <span class="badge gray" style="margin-right:0.25rem"><?= htmlspecialchars($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-info">No tiles in this room yet.</div>
      <?php endif; ?>
      <?php endif; ?>

      <!-- Submit tile form -->
      <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border)">
        <h4 style="margin-bottom:1rem">Submit a Tile</h4>
        <form action="api/submit_tile.php" method="POST">
          <input type="hidden" name="room" value="<?= htmlspecialchars($selected) ?>">
          <div class="form-group">
            <label>Content</label>
            <textarea name="content" rows="4" placeholder="Knowledge, lesson, or observation to add to this room..." required></textarea>
          </div>
          <div class="form-group">
            <label>Tags (comma-separated)</label>
            <input name="tags" placeholder="concept, fleet, example">
          </div>
          <button type="submit" class="btn btn-primary">Submit Tile</button>
        </form>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>
<script>
// Auto-refresh room every 10s
setTimeout(() => location.reload(), 10000);
</script>
</body>
</html>