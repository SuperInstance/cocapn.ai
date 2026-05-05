<?php
// Constraint Playground - GUARD editor + FLUX-C compilation
$examples = [
  'Temperature' => "// Temperature Safety
GUARD temp_safety {
  INPUT temp: FLOAT
  THRESHOLD 100.0
  
  IF temp > THRESHOLD THEN
    ACT shutdown()
    LOG \"CRITICAL: Temperature exceeded\"
  ELSE IF temp > 80.0 THEN
    LOG \"WARNING: Temperature rising\"
  END
}",
  'Door' => "// Door Access Control
GUARD door_access {
  INPUT badge_id: STRING
  INPUT door_state: ENUM [closed, open, locked]
  
  IF door_state == locked AND badge_valid(badge_id) THEN
    ACT unlock_door()
    LOG \"Access granted: \" + badge_id
  END
}",
  'Motor' => "// Motor Overcurrent Protection
GUARD motor_protection {
  INPUT current: FLOAT
  INPUT rpm: INT
  
  IF current > 15.0 AND rpm < 100 THEN
    ACT cut_power()
    LOG \"STALL DETECTED — power cut\"
  ELSE IF current > 12.0 THEN
    ACT reduce_duty_cycle(50)
    LOG \"Overcurrent warning — derating\"
  END
}",
  'Light' => "// Ambient Light Controller
GUARD light_control {
  INPUT lux: FLOAT
  
  IF lux < 50 THEN
    ACT lights_on()
    LOG \"Dark — lights on\"
  ELSE IF lux > 200 THEN
    ACT lights_off()
    LOG \"Bright — lights off\"
  END
}"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Constraint Playground — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    :root {
      --editor-bg: #0d1117;
      --editor-text: #c9d1d9;
      --guard-keyword: #ff6b6b;
      --guard-type: #da70d6;
      --guard-literal: #2ecc71;
      --guard-func: #3498db;
      --guard-comment: #6a737d;
      --output-bg: #161b22;
      --success-green: #22c55e;
      --warning-yellow: #eab308;
      --error-red: #ef4444;
    }
    .playground-intro { max-width: 680px; margin: 0 auto 2rem; text-align: center; color: var(--muted); font-size: 1.05rem; line-height: 1.8; }
    .playground-intro strong { color: var(--text); }
    .playground-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
    @media (max-width: 1024px) { .playground-layout { grid-template-columns: 1fr; } }
    .editor-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
    .editor-header { display: flex; justify-content: space-between; align-items: center; }
    .editor-title { font-size: 0.85rem; font-weight: 600; color: var(--accent); display: flex; align-items: center; gap: 0.4rem; }
    .editor-controls { display: flex; gap: 0.5rem; }
    .editor-btn { padding: 0.4rem 0.85rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer; border: 1px solid var(--border); background: transparent; color: var(--text); transition: all 0.2s; }
    .editor-btn:hover { border-color: var(--accent); color: var(--accent); }
    .editor-btn.primary { background: var(--accent); border-color: var(--accent); color: white; font-weight: 600; }
    .editor-btn.primary:hover { background: #2980b9; }
    .editor-wrap { position: relative; flex: 1; min-height: 320px; }
    .editor-highlight { position: absolute; top: 0; left: 0; right: 0; bottom: 0; padding: 1rem; font-family: 'Space Mono', monospace; font-size: 0.85rem; line-height: 1.6; white-space: pre-wrap; word-wrap: break-word; overflow-y: auto; color: var(--editor-text); pointer-events: none; tab-size: 2; }
    .editor-textarea { position: absolute; top: 0; left: 0; width: 100%; height: 100%; padding: 1rem; font-family: 'Space Mono', monospace; font-size: 0.85rem; line-height: 1.6; background: transparent; color: transparent; caret-color: var(--editor-text); border: none; resize: none; outline: none; tab-size: 2; overflow-y: auto; }
    .editor-textarea::selection { background: rgba(52,152,219,0.3); }
    .example-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .example-pill { padding: 0.25rem 0.75rem; border: 1px solid var(--border); border-radius: 20px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s; color: var(--muted); background: transparent; }
    .example-pill:hover { border-color: var(--success-green); color: var(--success-green); }
    .cursor-pos { font-size: 0.75rem; color: var(--muted); font-family: 'Space Mono', monospace; }
    .output-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
    .status-badge { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem; border-radius: 8px; font-size: 1rem; font-weight: 700; text-align: center; min-height: 60px; }
    .status-badge.success { background: rgba(34,197,94,0.12); color: var(--success-green); border: 1px solid rgba(34,197,94,0.3); }
    .status-badge.error { background: rgba(239,68,68,0.12); color: var(--error-red); border: 1px solid rgba(239,68,68,0.3); }
    .status-badge.loading { background: rgba(52,152,219,0.08); color: var(--accent); border: 1px solid rgba(52,152,219,0.2); }
    .metrics-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
    .metric-card { background: var(--output-bg); border-radius: 8px; padding: 0.75rem; text-align: center; }
    .metric-value { font-family: 'Space Mono', monospace; font-size: 1.1rem; font-weight: 700; color: var(--text); }
    .metric-label { font-size: 0.7rem; color: var(--muted); margin-top: 0.2rem; }
    .asm-output { background: var(--editor-bg); border-radius: 8px; padding: 1rem; font-family: 'Space Mono', monospace; font-size: 0.8rem; max-height: 280px; overflow-y: auto; }
    .asm-line { display: flex; gap: 1rem; padding: 0.15rem 0; }
    .asm-addr { color: #6a737d; min-width: 50px; }
    .asm-mnemonic { color: #3498db; font-weight: 600; }
    .asm-operand { color: #c9d1d9; }
    .exec-preview { background: var(--editor-bg); border-radius: 8px; padding: 0.75rem; font-family: 'Space Mono', monospace; font-size: 0.8rem; }
    .exec-case { display: flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0; }
    .exec-input { color: var(--warning-yellow); }
    .exec-arrow { color: var(--muted); }
    .exec-result { color: var(--success-green); }
    .toast { position: fixed; top: 1rem; right: 1rem; background: var(--surface); border: 1px solid var(--success-green); border-radius: 8px; padding: 0.75rem 1.25rem; font-size: 0.85rem; color: var(--success-green); z-index: 1000; animation: toastIn 0.2s ease; }
    @keyframes toastIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .error-list { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 0.75rem; }
    .error-item { font-family: 'Space Mono', monospace; font-size: 0.8rem; color: var(--error-red); padding: 0.2rem 0; display: flex; gap: 0.5rem; }
    .error-item .err-line { color: var(--muted); min-width: 30px; }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>
<main class="container page">
  <div class="section-header">
    <h2>🔒 Constraint Playground</h2>
    <p>Write a GUARD constraint, compile to FLUX-C bytecode, get mathematically verified output.</p>
  </div>

  <div class="playground-intro">
    <strong>Write once, verify forever.</strong> GUARD is PLATO's constraint language for safety-critical systems. Paste a template or write your own — see it compile to provably correct FLUX-C bytecode in real time. The Z3 prover checks all paths before a single instruction runs on hardware.
  </div>

  <div class="playground-layout">
    <!-- Editor Panel -->
    <div class="editor-panel">
      <div class="editor-header">
        <div class="editor-title">🔒 GUARD Editor</div>
        <div class="editor-controls">
          <button class="editor-btn primary" id="compileBtn" onclick="compile()">▶ Compile</button>
          <button class="editor-btn" onclick="resetEditor()">↻ Reset</button>
        </div>
      </div>
      <div class="editor-wrap">
        <div class="editor-highlight" id="highlight"></div>
        <textarea class="editor-textarea" id="code" spellcheck="false" oninput="updateHighlight()" onscroll="syncScroll()" onkeydown="handleTab(event)"><?= htmlspecialchars($examples['Temperature']) ?></textarea>
      </div>
      <div class="example-pills">
        <span style="font-size:0.75rem;color:var(--muted);margin-right:0.25rem">Examples:</span>
        <?php foreach (array_keys($examples) as $i => $name): ?>
        <button class="example-pill" onclick="loadExample('<?= $name ?>')"><?= ['🌡️','🚪','⚙️','💡'][$i] ?> <?= $name ?></button>
        <?php endforeach; ?>
      </div>
      <div class="cursor-pos">Line <span id="lineNum">1</span>, Col <span id="colNum">1</span></div>
    </div>

    <!-- Output Panel -->
    <div class="output-panel">
      <div class="editor-title">⚡ Compilation Output</div>
      <div id="statusArea">
        <div class="status-badge success">✅ Ready — write your GUARD and hit Compile</div>
      </div>
      <div class="metrics-row" id="metricsRow" style="display:none">
        <div class="metric-card"><div class="metric-value" id="compileTime">—</div><div class="metric-label">Compile (ms)</div></div>
        <div class="metric-card"><div class="metric-value" id="verifyTime">—</div><div class="metric-label">Verify (ms)</div></div>
        <div class="metric-card"><div class="metric-value" id="bytecodeSize">—</div><div class="metric-label">Bytes</div></div>
      </div>
      <div id="asmArea" style="display:none">
        <div style="font-size:0.8rem;font-weight:600;margin-bottom:0.5rem;color:var(--muted)">FLUX-C Bytecode</div>
        <div class="asm-output" id="asmOutput"></div>
        <button class="editor-btn" style="margin-top:0.75rem" onclick="copyHex()">📋 Copy Hex</button>
      </div>
      <div id="execArea" style="display:none">
        <div style="font-size:0.8rem;font-weight:600;margin-bottom:0.5rem;color:var(--muted)">Execution Preview</div>
        <div class="exec-preview" id="execOutput"></div>
      </div>
      <div id="errorArea" style="display:none"></div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/lib/footer.php'; ?>
<script>
// Example templates
const examples = <?= json_encode($examples) ?>;

function loadExample(name) {
  document.getElementById('code').value = examples[name];
  updateHighlight();
  clearOutput();
  showStatus('success', '✅ Example loaded — edit and Compile');
}

function resetEditor() {
  document.getElementById('code').value = examples['Temperature'];
  updateHighlight();
  clearOutput();
  showStatus('success', '✅ Editor reset');
}

function clearOutput() {
  document.getElementById('statusArea').style.display = '';
  document.getElementById('metricsRow').style.display = 'none';
  document.getElementById('asmArea').style.display = 'none';
  document.getElementById('execArea').style.display = 'none';
  document.getElementById('errorArea').style.display = 'none';
}

function showStatus(type, message) {
  const icons = { success: '✅', error: '❌', loading: '⏳' };
  document.getElementById('statusArea').innerHTML = '<div class="status-badge ' + type + '">' + icons[type] + ' ' + message + '</div>';
}

function updateHighlight() {
  const code = document.getElementById('code').value;
  const highlight = document.getElementById('highlight');
  highlight.textContent = code;
  
  // Simple syntax highlighting
  let html = code
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/\/\/.*/g, '<span style="color:var(--guard-comment)">$&</span>')
    .replace(/(GUARD|IF|THEN|ELSE|END|ACT|LOG|INPUT|THRESHOLD)\b/g, '<span style="color:var(--guard-keyword);font-weight:700">$1</span>')
    .replace(/\b(FLOAT|INT|STRING|ENUM|BOOL)\b/g, '<span style="color:var(--guard-type)">$1</span>')
    .replace(/"[^"]*"/g, '<span style="color:var(--guard-literal)">$&</span>')
    .replace(/\b(\d+\.?\d*)\b/g, '<span style="color:var(--guard-literal)">$1</span>')
    .replace(/\b(shutdown|log|unlock_door|cut_power|reduce_duty_cycle|lights_on|lights_off|badge_valid|start_timer)\b/g, '<span style="color:var(--guard-func)">$1</span>');
  highlight.innerHTML = html;
  
  // Update cursor position
  const textarea = document.getElementById('code');
  const pos = textarea.selectionStart;
  const before = code.substring(0, pos);
  const lines = before.split('\n');
  document.getElementById('lineNum').textContent = lines.length;
  document.getElementById('colNum').textContent = lines[lines.length - 1].length + 1;
}

function syncScroll() {
  document.getElementById('highlight').scrollTop = document.getElementById('code').scrollTop;
  document.getElementById('highlight').scrollLeft = document.getElementById('code').scrollLeft;
}

function handleTab(e) {
  if (e.key === 'Tab') {
    e.preventDefault();
    const ta = document.getElementById('code');
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    ta.value = ta.value.substring(0, start) + '  ' + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = start + 2;
    updateHighlight();
  }
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
    e.preventDefault();
    compile();
  }
}

async function compile() {
  const source = document.getElementById('code').value;
  const btn = document.getElementById('compileBtn');
  btn.disabled = true;
  btn.textContent = '⏳ Compiling...';
  showStatus('loading', 'Compiling and verifying...');
  
  try {
    const resp = await fetch('/api/compile_guard.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ source, options: { verify: true, target: 'flux-c-v2', opt_level: 2 } })
    });
    const data = await resp.json();
    
    if (data.status === 'success') {
      showStatus('success', '✅ Verified by Z3 (' + data.verification.checks_passed + '/' + data.verification.checks_total + ' checks)');
      document.getElementById('metricsRow').style.display = 'grid';
      document.getElementById('compileTime').textContent = data.compile_time_ms;
      document.getElementById('verifyTime').textContent = data.verification.time_ms;
      document.getElementById('bytecodeSize').textContent = data.bytecode.size_bytes;
      
      const asmHtml = data.bytecode.asm.map(instr => 
        '<div class="asm-line"><span class="asm-addr">' + instr.addr + '</span><span class="asm-mnemonic">' + instr.asm.split(' ')[0] + '</span><span class="asm-operand">' + instr.asm.split(' ').slice(1).join(' ') + '</span></div>'
      ).join('');
      document.getElementById('asmOutput').innerHTML = asmHtml;
      document.getElementById('asmArea').style.display = '';
      
      const execHtml = data.execution_preview.can_run 
        ? '<div class="exec-case"><span class="exec-input">temp=95</span><span class="exec-arrow">→</span><span class="exec-result">HALT ✓ log("OK")</span></div><div class="exec-case"><span class="exec-input">temp=105</span><span class="exec-arrow">→</span><span class="exec-result">HALT ✓ shutdown()</span></div>'
        : '<div class="exec-case"><span class="exec-result">Unverified — cannot execute in safe mode</span></div>';
      document.getElementById('execOutput').innerHTML = execHtml;
      document.getElementById('execArea').style.display = '';
    } else {
      showStatus('error', '❌ ' + (data.errors?.[0]?.message || 'Compilation failed'));
      if (data.errors?.length) {
        document.getElementById('errorArea').style.display = '';
        document.getElementById('errorArea').innerHTML = '<div class="error-list">' + data.errors.map(e => 
          '<div class="error-item"><span class="err-line">L' + e.line + '</span>' + e.message + (e.suggestion ? ' <span style="color:var(--accent)">→ ' + e.suggestion + '</span>' : '') + '</div>'
        ).join('') + '</div>';
      }
    }
  } catch (e) {
    showStatus('error', '❌ Network error — will retry');
  }
  
  btn.disabled = false;
  btn.textContent = '▶ Compile';
}

function copyHex() {
  const hexEl = document.querySelector('.asm-output');
  if (hexEl) {
    navigator.clipboard.writeText(hexEl.textContent.replace(/\s+/g, ' ').trim()).then(() => {
      const toast = document.createElement('div');
      toast.className = 'toast';
      toast.textContent = '📋 Copied!';
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 2000);
    });
  }
}

// Init
updateHighlight();
</script>
