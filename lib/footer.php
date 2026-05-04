<footer>
  <div class="footer-inner">
    <div class="footer-brand">
      <span class="mono">CoCapn</span> — SuperInstance fleet infrastructure
    </div>
    <div class="footer-links">
      <a href="https://github.com/SuperInstance" target="_blank">GitHub</a>
      <a href="https://github.com/SuperInstance/SuperInstance/discussions" target="_blank">Discussions</a>
      <a href="https://github.com/SuperInstance/cocapn.ai/issues" target="_blank">Issues</a>
    </div>
  </div>
</footer>
<style>
footer {
  border-top: 1px solid #1e293b;
  padding: 2rem 3rem;
  margin-top: 4rem;
}
.footer-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  color: #64748b;
  font-size: 0.875rem;
}
.footer-links { display: flex; gap: 1.5rem; }
.footer-links a { color: #64748b; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #3b82f6; }
@media (max-width: 600px) {
  footer { padding: 1.5rem; }
  .footer-inner { flex-direction: column; gap: 1rem; text-align: center; }
}
</style>
<script>
  // Auto-refresh for status page (checks data-refresh attribute)
  document.querySelectorAll('[data-refresh]').forEach(el => {
    const interval = parseInt(el.dataset.refresh) * 1000;
    setInterval(() => el.refresh && el.refresh(), interval);
  });
</script>