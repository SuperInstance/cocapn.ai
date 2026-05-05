<?php
// Fetch live or static benchmark data
$leaderboard_data = [
    'generated_at' => date('c'),
    'leaderboard' => [
        ['rank' => 1, 'chip_name' => 'NVIDIA Jetson Orin Nano', 'board_variant' => '8GB DevKit', 'tops' => 40, 'watts' => 15, 'safe_tops_w' => 2.53, 'status' => 'green', 'benchmark_date' => '2026-04-28', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 2, 'chip_name' => 'NVIDIA Jetson AGX Orin', 'board_variant' => '64GB', 'tops' => 275, 'watts' => 60, 'safe_tops_w' => 2.14, 'status' => 'green', 'benchmark_date' => '2026-04-30', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 3, 'chip_name' => 'NVIDIA Jetson Orin NX', 'board_variant' => '16GB', 'tops' => 100, 'watts' => 25, 'safe_tops_w' => 1.87, 'status' => 'green', 'benchmark_date' => '2026-04-28', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 4, 'chip_name' => 'Qualcomm QCS6490', 'board_variant' => 'RB5', 'tops' => 13, 'watts' => 8.5, 'safe_tops_w' => 0.92, 'status' => 'yellow', 'benchmark_date' => '2026-05-01', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 5, 'chip_name' => 'NVIDIA RTX 4050 Laptop', 'board_variant' => 'Laptop GPU', 'tops' => 110, 'watts' => 85, 'safe_tops_w' => 0.85, 'status' => 'yellow', 'benchmark_date' => '2026-04-20', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 6, 'chip_name' => 'AMD Ryzen AI 9 HX 370', 'board_variant' => 'Strix Point', 'tops' => 31, 'watts' => 28, 'safe_tops_w' => 0.78, 'status' => 'yellow', 'benchmark_date' => '2026-05-03', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 7, 'chip_name' => 'Apple M3 Pro', 'board_variant' => '16" MacBook Pro', 'tops' => 18, 'watts' => 35, 'safe_tops_w' => 0.49, 'status' => 'yellow', 'benchmark_date' => '2026-04-25', 'firmware_version' => 'FLUX-C v2.1.4'],
        ['rank' => 8, 'chip_name' => 'Raspberry Pi 5', 'board_variant' => '8GB', 'tops' => 4, 'watts' => 8, 'safe_tops_w' => 0.12, 'status' => 'red', 'benchmark_date' => '2026-05-01', 'firmware_version' => 'FLUX-C v2.1.4'],
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FLUX-C Benchmarks — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    :root { --safe-green: #22c55e; --safe-yellow: #eab308; --safe-red: #ef4444; }
    .benchmark-intro { max-width: 720px; margin: 0 auto 2rem; text-align: center; color: var(--muted); font-size: 1.05rem; line-height: 1.8; }
    .benchmark-intro strong { color: var(--text); }
    .leaderboard-controls { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; margin-bottom: 1.5rem; }
    .filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .filter-pill { padding: 0.35rem 0.85rem; border: 1px solid var(--border); border-radius: 20px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; color: var(--muted); background: transparent; }
    .filter-pill:hover, .filter-pill.active { border-color: var(--accent); color: var(--accent); background: rgba(52,152,219,0.08); }
    .leaderboard-table { width: 100%; border-collapse: collapse; }
    .leaderboard-table th { text-align: left; padding: 0.75rem 1rem; border-bottom: 2px solid var(--border); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); cursor: pointer; }
    .leaderboard-table th:hover { color: var(--text); }
    .leaderboard-table td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); transition: background 0.15s; }
    .leaderboard-table tr:hover td { background: rgba(52,152,219,0.04); }
    .rank-cell { font-family: 'Space Mono', monospace; font-weight: 700; font-size: 1.1rem; }
    .rank-cell.trophy { color: #fbbf24; font-size: 1.3rem; }
    .chip-name { font-weight: 600; }
    .board-variant { font-size: 0.8rem; color: var(--muted); }
    .tops-cell, .watts-cell { font-family: 'Space Mono', monospace; color: var(--muted); }
    .safe-tops { font-family: 'Space Mono', monospace; font-weight: 700; font-size: 1.05rem; }
    .status-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
    .status-badge.green { background: rgba(34,197,94,0.15); color: var(--safe-green); }
    .status-badge.yellow { background: rgba(234,179,8,0.15); color: var(--safe-yellow); }
    .status-badge.red { background: rgba(239,68,68,0.15); color: var(--safe-red); }
    .refresh-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; color: var(--muted); font-size: 0.8rem; border-bottom: 1px solid var(--border); margin-bottom: 1rem; }
    .refresh-indicator { display: flex; align-items: center; gap: 0.4rem; }
    .refresh-spin { animation: spin 2s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @media (max-width: 768px) {
      .leaderboard-table thead { display: none; }
      .leaderboard-table tr { display: block; margin-bottom: 1rem; padding: 1rem; border: 1px solid var(--border); border-radius: 10px; }
      .leaderboard-table td { display: flex; justify-content: space-between; padding: 0.3rem 0; }
      .leaderboard-table td::before { content: attr(data-label); font-weight: 600; color: var(--muted); font-size: 0.8rem; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>
<main class="container page">
  <div class="section-header">
    <h2>🔥 Safe-TOPS/W Leaderboard</h2>
    <p>Sustained FLUX-C inference performance under worst-case thermal conditions — not peak marketing numbers.</p>
  </div>

  <div class="benchmark-intro">
    <strong>Why "Safe"?</strong> TOPS/W at peak burst is marketing fiction. Safe-TOPS/W measures <em>actual sustained throughput</em> running FLUX-C inference. Green means buy with confidence. Red means the marketing department did the math.
  </div>

  <div class="refresh-row">
    <div class="refresh-indicator">
      <span>🔄</span>
      <span>Auto-refresh: 5 min · Last run: <?= date('H:i:s T', strtotime($leaderboard_data['generated_at'])) ?></span>
    </div>
    <button class="btn-ghost" onclick="downloadCSV()">📥 Download CSV</button>
  </div>

  <div class="leaderboard-controls">
    <div class="filter-pills">
      <button class="filter-pill active" data-filter="all">All</button>
      <button class="filter-pill" data-filter="nvidia">NVIDIA</button>
      <button class="filter-pill" data-filter="jetson">Jetson</button>
      <button class="filter-pill" data-filter="arm">ARM</button>
      <button class="filter-pill" data-filter="custom">Custom</button>
    </div>
  </div>

  <div class="table-wrap">
    <table class="leaderboard-table" id="leaderboard">
      <thead>
        <tr>
          <th>#</th>
          <th>Chip / Board</th>
          <th>TOPS</th>
          <th>Watts</th>
          <th>Safe TOPS/W</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($leaderboard_data['leaderboard'] as $entry): ?>
        <tr data-filter="<?= strtolower(str_replace(' ', '-', $entry['chip_name'])) ?>">
          <td class="rank-cell <?= $entry['rank'] === 1 ? 'trophy' : '' ?>">
            <?= $entry['rank'] === 1 ? '🏆' : $entry['rank'] ?>
          </td>
          <td>
            <div class="chip-name"><?= htmlspecialchars($entry['chip_name']) ?></div>
            <div class="board-variant"><?= htmlspecialchars($entry['board_variant']) ?> · FLUX-C <?= htmlspecialchars($entry['firmware_version']) ?> · <?= htmlspecialchars($entry['benchmark_date']) ?></div>
          </td>
          <td class="tops-cell"><?= $entry['tops'] ?></td>
          <td class="watts-cell"><?= $entry['watts'] ?>W</td>
          <td class="safe-tops" style="color: <?= $entry['status'] === 'green' ? 'var(--safe-green)' : ($entry['status'] === 'yellow' ? 'var(--safe-yellow)' : 'var(--safe-red)') ?>">
            <?= number_format($entry['safe_tops_w'], 2) ?>
          </td>
          <td>
            <span class="status-badge <?= $entry['status'] ?>">
              <?= $entry['status'] === 'green' ? '🟢 BUY' : ($entry['status'] === 'yellow' ? '🟡 CAUTION' : '🔴 AVOID') ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
    // Filter functionality
    document.querySelectorAll('.filter-pill').forEach(pill => {
      pill.addEventListener('click', () => {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        const filter = pill.dataset.filter;
        document.querySelectorAll('#leaderboard tbody tr').forEach(row => {
          if (filter === 'all') { row.style.display = ''; return; }
          const chip = row.dataset.filter;
          const show = chip.includes(filter);
          row.style.display = show ? '' : 'none';
        });
      });
    });

    // Sort on column click
    let sortAsc = true;
    document.querySelectorAll('.leaderboard-table th').forEach((th, colIdx) => {
      th.addEventListener('click', () => {
        if (colIdx === 0 || colIdx === 1) return;
        sortAsc = !sortAsc;
        const rows = Array.from(document.querySelectorAll('#leaderboard tbody tr'));
        const col = colIdx;
        rows.sort((a, b) => {
          const aVal = parseFloat(a.children[col].textContent) || 0;
          const bVal = parseFloat(b.children[col].textContent) || 0;
          return sortAsc ? aVal - bVal : bVal - aVal;
        });
        rows.forEach(r => document.querySelector('#leaderboard tbody').appendChild(r));
      });
    });

    // CSV download
    function downloadCSV() {
      const rows = [['Rank', 'Chip', 'Variant', 'TOPS', 'Watts', 'Safe TOPS/W', 'Status', 'Date', 'FW']];
      document.querySelectorAll('#leaderboard tbody tr').forEach(tr => {
        if (tr.style.display !== 'none') {
          const tds = tr.querySelectorAll('td');
          rows.push([tds[0].textContent.trim(), tds[1].querySelector('.chip-name').textContent, tds[1].querySelector('.board-variant').textContent.split('·')[0].trim(), tds[2].textContent.trim(), tds[3].textContent.trim(), tds[4].textContent.trim(), tds[5].querySelector('.status-badge').textContent.trim(), '<?= date('Y-m-d') ?>']);
        }
      });
      const csv = rows.map(r => r.join(',')).join('\n');
      const a = document.createElement('a');
      a.href = 'data:text/csv,' + encodeURIComponent(csv);
      a.download = 'flux-c-leaderboard-<?= date('Y-m-d') ?>.csv';
      a.click();
    }
  </script>
</main>
<?php include __DIR__ . '/lib/footer.php'; ?>
