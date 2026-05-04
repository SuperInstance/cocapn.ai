<?php
require_once __DIR__ . '/lib/plato_client.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FLUX Playground — CoCapn</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .opcode-table { font-size: 0.82rem; }
    .opcode-table th { font-size: 0.7rem; }
    .opcode-table td { padding: 0.45rem 0.75rem; }
    .opcode-table td:first-child { font-family: 'Space Mono', monospace; color: var(--accent); }
    .sandbox-output { min-height: 120px; background: #0d1117; border: 1px solid var(--border); border-radius: 8px; padding: 1rem; font-family: 'Space Mono', monospace; font-size: 0.85rem; color: #22c55e; }
  </style>
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>

<main class="container page">
  <div class="section-header">
    <h2>FLUX Playground</h2>
    <p>Two-layer ISA: <strong>FLUX-C</strong> (constraint layer, 43 opcodes) + <strong>FLUX-X</strong> (general ops, 247 opcodes)</p>
  </div>

  <div class="grid-2" style="gap:2rem">
    <!-- Opcode reference -->
    <div>
      <h3 style="margin-bottom:1rem">FLUX-C Opcodes (Constraint Layer)</h3>
      <div class="table-wrap" style="max-height:500px;overflow-y:auto">
        <table class="opcode-table">
          <thead>
            <tr><th>Opcode</th><th>Args</th><th>Description</th></tr>
          </thead>
          <tbody>
            <?php
            $flux_c = [
              ['HALT','0','Halt execution'],['NOP','0','No operation'],['PUSH','1','Push immediate to stack'],
              ['POP','0','Pop from stack'],['DUP','0','Duplicate top of stack'],
              ['SWAP','0','Swap top two stack values'],['ADD','0','Integer add'],['SUB','0','Integer subtract'],
              ['MUL','0','Integer multiply'],['DIV','0','Integer divide (floor)'],
              ['AND','0','Bitwise AND'],['OR','0','Bitwise OR'],['XOR','0','Bitwise XOR'],['NOT','0','Bitwise NOT'],
              ['SHL','0','Shift left'],['SHR','0','Shift right'],
              ['EQ','0','Equality check → 1 or 0'],['NEQ','0','Not equal'],['LT','0','Less than'],
              ['GT','0','Greater than'],['LTE','0','Less than or equal'],['GTE','0','Greater than or equal'],
              ['JZ','1','Jump if zero'],['JNZ','1','Jump if not zero'],['JMP','1','Unconditional jump'],
              ['LOAD','1','Load from address'],['STORE','1','Store to address'],
              ['CONSTRAINT','1','Begin constraint block'],['ASSERT','0','Assert constraint'],
              ['VERIFY','0','Verify all constraints in block'],['SNAP','1','Snap value to nearest valid'],
              ['DEADBAND','1','Set deadband threshold'],['MASK','0','Apply bitmask constraint'],
              ['BOUND','0','Apply min/max bounds'],
              ['CALL','1','Call function'],['RET','0','Return from function'],
              ['ENTER','1','Enter scope with n locals'],['LEAVE','0','Leave scope'],
              ['SYSCALL','1','System call'],['INT','1','Interrupt'],
              ['MOV','2','Move register'],['CLR','1','Clear register'],
              ['RESERVE','1','Reserve stack space'],['FREE','0','Free stack space'],
            ];
            foreach($flux_c as $op): ?>
            <tr>
              <td><?= $op[0] ?></td>
              <td style="color:var(--muted)"><?= $op[1] ?></td>
              <td><?= $op[2] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Sandbox -->
    <div>
      <h3 style="margin-bottom:1rem">Sandbox</h3>
      <div class="form-group">
        <label>Assembly</label>
        <textarea id="asm-input" rows="12" style="font-family:'Space Mono',monospace" placeholder="PUSH 42
PUSH 6
ADD
HALT">PUSH 42
PUSH 6
ADD
HALT</textarea>
      </div>
      <button onclick="runFlux()" class="btn btn-primary">▶ Run</button>
      <div style="margin-top:1rem">
        <label>Output</label>
        <div class="sandbox-output" id="asm-output">Ready.</div>
      </div>
      <div class="card" style="margin-top:1.5rem">
        <h4>FLUX-C Constraints</h4>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:0.4rem">
          FLUX-C enforces constraints <em>before</em> operations execute. Use <code>CONSTRAINT</code> to open a block, <code>VERIFY</code> to close it.
          The bridge to FLUX-X is one-way and gas-bounded.
        </p>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/lib/footer.php'; ?>
<script>
const STACK_SIZE = 256;
function halt() { return 'HALT'; }

function runFlux() {
  const lines = document.getElementById('asm-input').value.split('\n');
  const stack = [];
  const output = [];
  let pc = 0, halted = false;

  const ops = {
    PUSH: (a) => { stack.push(parseInt(a)); return null; },
    POP: () => { stack.pop(); return null; },
    DUP: () => { stack.push(stack[stack.length-1]); return null; },
    ADD: () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)+(b||0)); return null; },
    SUB: () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)-(b||0)); return null; },
    MUL: () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)*(b||0)); return null; },
    DIV: () => { const b=stack.pop(),a=stack.pop(); stack.push(Math.floor((a||0)/(b||1))); return null; },
    AND: () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)&(b||0)); return null; },
    OR:  () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)|(b||0)); return null; },
    XOR: () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)^(b||0)); return null; },
    NOT: () => { stack.push(~(stack.pop()||0)); return null; },
    EQ:  () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)===(b||0)?1:0); return null; },
    NEQ: () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)!==(b||0)?1:0); return null; },
    LT:  () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)<(b||0)?1:0); return null; },
    GT:  () => { const b=stack.pop(),a=stack.pop(); stack.push((a||0)>(b||0)?1:0); return null; },
    HALT: () => 'HALT',
  };

  while (pc < lines.length && !halted) {
    const line = lines[pc].trim().split(/\s+/);
    if (!line[0]) { pc++; continue; }
    const op = line[0].toUpperCase();
    if (ops[op]) {
      const result = ops[op](line[1]);
      if (result === 'HALT') { halted = true; break; }
    }
    pc++;
  }

  const out = document.getElementById('asm-output');
  out.textContent = 'Stack: [' + stack.join(', ') + ']\npc: ' + pc + (halted ? '\n(Halted)' : '\n(End of code)');
}
</script>
</body>
</html>