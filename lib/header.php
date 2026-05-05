<?php
/**
 * Shared header — include at top of every page
 * Uses existing CSS vars from index.html
 */
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav>
  <div class="logo">
    <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="20" cy="20" r="18" stroke="#3b82f6" stroke-width="2"/>
      <circle cx="20" cy="20" r="12" stroke="#3b82f6" stroke-width="1.5" opacity="0.6"/>
      <circle cx="20" cy="20" r="6" stroke="#3b82f6" stroke-width="1" opacity="0.3"/>
      <line x1="20" y1="2" x2="20" y2="38" stroke="#3b82f6" stroke-width="1" opacity="0.2"/>
      <line x1="2" y1="20" x2="38" y2="20" stroke="#3b82f6" stroke-width="1" opacity="0.2"/>
    </svg>
    <span class="logo-text">CoCapn</span>
  </div>
  <div class="nav-links">
    <a href="fleet.php" class="<?= $current_page === 'fleet' ? 'active' : '' ?>">Fleet</a>
    <a href="explorer.php" class="<?= $current_page === 'explorer' ? 'active' : '' ?>">Explorer</a>
    <a href="flux.php" class="<?= $current_page === 'flux' ? 'active' : '' ?>">FLUX</a>
    <a href="benchmark.php" class="<?= $current_page === 'benchmark' ? 'active' : '' ?>">Benchmark</a>
    <a href="constraint-playground.php" class="<?= $current_page === 'constraint-playground' ? 'active' : '' ?>">Playground</a>
    <a href="learn.php" class="<?= $current_page === 'learn' ? 'active' : '' ?>">Learn</a>
    <a href="papers.php" class="<?= $current_page === 'papers' ? 'active' : '' ?>">Papers</a>
    <a href="docs.php" class="<?= $current_page === 'docs' ? 'active' : '' ?>">Docs</a>
    <a href="examples.php" class="<?= $current_page === 'examples' ? 'active' : '' ?>">Examples</a>
    <a href="community.php" class="<?= $current_page === 'community' ? 'active' : '' ?>">Community</a>
    <a href="status.php" class="<?= $current_page === 'status' ? 'active' : '' ?>">Status</a>
  </div>
</nav>
<style>
nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 3rem;
  border-bottom: 1px solid #1e293b;
  position: sticky;
  top: 0;
  background: #0a0e17;
  z-index: 100;
}
.logo { display: flex; align-items: center; gap: 0.75rem; }
.logo-icon { width: 36px; height: 36px; }
.logo-text { font-weight: 700; font-size: 1.2rem; letter-spacing: 0.05em; color: #e2e8f0; }
.nav-links { display: flex; gap: 1.75rem; }
.nav-links a {
  color: #64748b;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: color 0.2s;
  padding-bottom: 2px;
  border-bottom: 2px solid transparent;
}
.nav-links a:hover { color: #e2e8f0; }
.nav-links a.active { color: #3b82f6; border-bottom-color: #3b82f6; }
@media (max-width: 900px) {
  nav { padding: 1rem 1.5rem; flex-wrap: wrap; gap: 0.5rem; }
  .nav-links { gap: 1rem; flex-wrap: wrap; }
  .nav-links a { font-size: 0.8rem; }
}
</style>