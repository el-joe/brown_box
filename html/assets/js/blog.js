/* ellamart+ — blog listing & blog details scripts */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- Toast helper ---------------- */
  var toast = document.getElementById('blog-toast');
  var toastTimer = null;
  function showToast(message) {
    if (!toast) return;
    toast.querySelector('span').textContent = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 3000);
  }

  /* ================= BLOG LISTING: category filter ================= */
  var filterTabs = document.querySelectorAll('.blog-filter-tab');
  var blogCards = document.querySelectorAll('[data-blog-card]');
  var blogEmpty = document.getElementById('blog-empty');
  if (filterTabs.length) {
    filterTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        filterTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        var filter = tab.dataset.filter;
        var visibleCount = 0;
        blogCards.forEach(function (card) {
          var match = filter === 'all' || card.dataset.category === filter;
          card.classList.toggle('hidden', !match);
          if (match) visibleCount++;
        });
        if (blogEmpty) blogEmpty.classList.toggle('hidden', visibleCount !== 0);
      });
    });
  }

  /* ================= Sidebar / inline newsletter mini forms ================= */
  document.querySelectorAll('.js-newsletter-mini').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = form.querySelector('input[type="email"]');
      if (input && input.value) {
        showToast('Thanks for subscribing!');
        input.value = '';
      }
    });
  });

  /* ================= Blog search (sidebar widget, cosmetic) ================= */
  document.querySelectorAll('.js-blog-search').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = form.querySelector('input');
      if (input && input.value.trim()) showToast('Searching for "' + input.value.trim() + '"...');
    });
  });

  /* ================= BLOG DETAILS: comment form ================= */
  var commentForm = document.getElementById('comment-form');
  var commentList = document.getElementById('comment-list');
  var commentCount = document.getElementById('comment-count');

  function bumpCommentCount(delta) {
    if (!commentCount) return;
    var current = parseInt(commentCount.textContent, 10) || 0;
    commentCount.textContent = current + delta;
  }

  commentForm && commentForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var name = document.getElementById('comment-name');
    var email = document.getElementById('comment-email');
    var message = document.getElementById('comment-message');

    if (!name.value.trim() || !email.value.trim() || !message.value.trim()) {
      document.getElementById('comment-form-error').classList.remove('hidden');
      return;
    }
    document.getElementById('comment-form-error').classList.add('hidden');

    var item = document.createElement('div');
    item.className = 'comment-item new';
    item.innerHTML =
      '<img class="comment-avatar" src="https://i.pravatar.cc/100?img=68" alt="' + name.value.trim() + '">' +
      '<div class="min-w-0">' +
        '<span class="comment-name"></span><span class="comment-date">Just now</span>' +
        '<p class="comment-text"></p>' +
        '<a href="#" class="comment-reply-btn">Reply</a>' +
      '</div>';
    item.querySelector('.comment-name').textContent = name.value.trim();
    item.querySelector('.comment-text').textContent = message.value.trim();
    commentList.insertBefore(item, commentList.firstChild);
    bumpCommentCount(1);

    commentForm.reset();
    showToast('Your comment has been posted.');
  });

});
