(function () {
  var KEY = 'valiente-theme';

  function boot() {
    try {
      if (localStorage.getItem(KEY) === 'light') {
        document.body.classList.add('light');
      }
    } catch (e) {}
  }

  function syncToggle(el) {
    if (!el) return;
    var light = document.body.classList.contains('light');
    el.textContent = light ? '☀️' : '🌙';
    el.setAttribute(
      'aria-label',
      light ? 'Switch to dark mode' : 'Switch to light mode'
    );
    try {
      localStorage.setItem(KEY, light ? 'light' : 'dark');
    } catch (e) {}
  }

  window.ValienteTheme = {
    key: KEY,
    boot: boot,
    syncToggle: syncToggle,
    toggle: function (el) {
      document.body.classList.toggle('light');
      syncToggle(el);
    },
  };
})();
