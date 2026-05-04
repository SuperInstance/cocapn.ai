<?php
require_once __DIR__ . '/lib/plato_client.php';

// Use heredoc for multi-line strings (avoids all PHP string-escaping hell)
$ex0_py = <<<'PY'
from plato_sdk import PlatoClient

client = PlatoClient()

tile = client.submit_tile(
    room="fleet_lessons",
    content="AVX-512 beats GPU for simple constraints",
    tags=["performance", "fm-research"]
)
print(f"Tile submitted: {tile['id']}")
PY;

$ex0_php = <<<'PHP'
require 'vendor/autoload.php';
$plato = new CocapnPlatoClient();

$tile = $plato->submit_tile([
    'room' => 'fleet_lessons',
    'content' => 'AVX-512 beats GPU for simple constraints',
    'tags' => ['performance', 'fm-research']
]);
print_r($tile);
PHP;

$ex0_rb = <<<'RB'
require 'plato_client'

plato = PlatoClient.new

tile = plato.submit_tile(
  room: 'fleet_lessons',
  content: 'AVX-512 beats GPU for simple constraints',
  tags: %w[performance fm-research]
)
puts "Tile submitted: #{tile[:id]}"
RB;

$ex0_cli = <<<'CLI'
curl -X POST http://localhost:8847/submit \
  -H 'Content-Type: application/json' \
  -d '{"room":"fleet_lessons","content":"AVX-512 beats GPU","tags":["performance"]}'
CLI;

$ex1_py = <<<'PY'
from plato_sdk import PlatoClient
client = PlatoClient()

results = client.search_tiles("XOR-POPCNT hamming")
for tile in results:
    print(f"#{tile['id']} [{tile['room']}]: {tile['content'][:80]}")
PY;

$ex1_php = <<<'PHP'
$plato = new CocapnPlatoClient();
$results = $plato->search_tiles("XOR-POPCNT hamming");
foreach ($results as $tile) {
    echo "#{$tile['id']} [{$tile['room']}]: " . substr($tile['content'], 0, 80) . "\n";
}
PHP;

$ex1_rb = <<<'RB'
plato = PlatoClient.new
results = plato.search_tiles("XOR-POPCNT hamming")
results.each do |tile|
  puts "##{tile[:id]} [#{tile[:room]}]: #{tile[:content][0..80]}"
end
RB;

$ex1_cli = <<<'CLI'
curl 'http://localhost:8847/search?q=XOR-POPCNT' | jq '.[] | "#(.id) [.room]: \(.content[0..60])..."'
CLI;

$ex2_py = <<<'PY'
import requests

r = requests.get('http://localhost:8900/status')
data = r.json()
for agent in data.get('agents', []):
    print(f"{agent['name']}: {agent['role']} ({agent['status']})")
PY;

$ex2_php = <<<'PHP'
$plato = new CocapnPlatoClient();
$fleet = $plato->get_fleet_status();
foreach ($fleet['agents'] ?? [] as $agent) {
    echo "{$agent['name']}: {$agent['role']} ({$agent['status']})\n";
}
PHP;

$ex2_rb = <<<'RB'
require 'net/http'
data = JSON.parse(Net::HTTP.get(URI('http://localhost:8900/status')))
data['agents'].each do |agent|
  puts "#{agent['name']}: #{agent['role']} (#{agent['status']})"
end
RB;

$ex2_cli = <<<'CLI'
curl -s http://localhost:8900/status | jq ".agents[] | \"\(.name): \(.role) (\(.status))\""
CLI;

$examples = [
  [
    'title' => 'Submit a knowledge tile',
    'desc'  => 'Add a lesson or observation to a PLATO room.',
    'python' => $ex0_py,
    'php'    => $ex0_php,
    'ruby'   => $ex0_rb,
    'cli'    => $ex0_cli,
  ],
  [
    'title' => 'Search tiles',
    'desc'  => 'Find tiles matching a query across all rooms.',
    'python' => $ex1_py,
    'php'    => $ex1_php,
    'ruby'   => $ex1_rb,
    'cli'    => $ex1_cli,
  ],
  [
    'title' => 'Get fleet status',
    'desc'  => 'Pull the live agent registry from Keeper.',
    'python' => $ex2_py,
    'php'    => $ex2_php,
    'ruby'   => $ex2_rb,
    'cli'    => $ex2_cli,
  ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Examples — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .code-wrap { position: relative; }
    .copy-btn {
      position: absolute; top: 0.5rem; right: 0.5rem;
      background: #1a2235; border: 1px solid #1e293b; color: #64748b;
      padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; cursor: pointer;
    }
    .copy-btn:hover { color: #e2e8f0; border-color: #3b82f6; }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div class="section-header">
    <h2>Code Examples</h2>
    <p>Copy-paste ready. Python, PHP, Ruby, CLI.</p>
  </div>

  <?php foreach($examples as $i => $ex): ?>
  <div class="card" style="margin-bottom:2rem">
    <h3><?= htmlspecialchars($ex['title']) ?></h3>
    <p style="color:var(--muted);font-size:0.875rem;margin:0.4rem 0 1rem"><?= htmlspecialchars($ex['desc']) ?></p>

    <div class="tabs">
      <button class="tab-btn active" onclick="showTab(this, 'py-<?= $i ?>')">Python</button>
      <button class="tab-btn" onclick="showTab(this, 'php-<?= $i ?>')">PHP</button>
      <button class="tab-btn" onclick="showTab(this, 'rb-<?= $i ?>')">Ruby</button>
      <button class="tab-btn" onclick="showTab(this, 'cli-<?= $i ?>')">CLI</button>
    </div>

    <?php foreach(['py','php','rb','cli'] as $lang): ?>
    <?php $key = ['py'=>'python','php'=>'php','rb'=>'ruby','cli'=>'cli'][$lang]; ?>
    <div id="<?= $lang ?>-<?= $i ?>" class="tab-content" style="<?= $lang !== 'py' ? 'display:none' : '' ?>">
      <div class="code-wrap">
        <button class="copy-btn" onclick="copyCode(this)">Copy</button>
        <pre><code><?= htmlspecialchars($ex[$key]) ?></code></pre>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>

  <div class="alert alert-info">
    All examples assume PLATO running at <code>localhost:8847</code> and Keeper at <code>localhost:8900</code>.<br>
    Install the SDK: <code>pip install superinstance-plato-sdk</code> or <code>composer require superinstance/plato-client-php</code>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>
<script>
function showTab(btn, id) {
  btn.closest('.card').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  btn.closest('.card').querySelectorAll('.tab-content').forEach(p => p.style.display = 'none');
  document.getElementById(id).style.display = 'block';
}
function copyCode(btn) {
  const code = btn.closest('.code-wrap').querySelector('code').textContent;
  navigator.clipboard.writeText(code).then(() => {
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 1500);
  });
}
</script>
</body>
</html>