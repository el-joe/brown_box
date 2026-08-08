/* ellamart+ — FAQ page (accordion, category filter, search) */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- Accordion ---------------- */
  document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.faq-item');
      var answer = item.querySelector('.faq-answer');
      var isOpen = item.classList.toggle('open');
      answer.classList.toggle('hidden', !isOpen);
    });
  });

  /* ---------------- Category filter ---------------- */
  var tabs = document.querySelectorAll('.faq-tab');
  var groups = document.querySelectorAll('.faq-group');
  var searchInput = document.getElementById('faq-search-input');
  var emptyState = document.getElementById('faq-empty');

  function applyFilters() {
    var activeTab = document.querySelector('.faq-tab.active');
    var category = activeTab ? activeTab.dataset.category : 'all';
    var query = (searchInput.value || '').trim().toLowerCase();
    var anyVisible = false;

    groups.forEach(function (group) {
      var groupMatches = category === 'all' || group.dataset.category === category;
      var visibleItemsInGroup = 0;

      group.querySelectorAll('.faq-item').forEach(function (item) {
        var text = item.textContent.toLowerCase();
        var matchesQuery = !query || text.indexOf(query) !== -1;
        var visible = groupMatches && matchesQuery;
        item.classList.toggle('hidden', !visible);
        if (visible) visibleItemsInGroup++;
      });

      group.classList.toggle('hidden', visibleItemsInGroup === 0);
      if (visibleItemsInGroup > 0) anyVisible = true;
    });

    emptyState.classList.toggle('hidden', anyVisible);
  }

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) { t.classList.remove('active'); });
      tab.classList.add('active');
      applyFilters();
    });
  });

  var searchForm = document.getElementById('faq-search-form');
  searchForm && searchForm.addEventListener('submit', function (e) { e.preventDefault(); applyFilters(); });
  searchInput && searchInput.addEventListener('input', applyFilters);

});
