document.addEventListener('DOMContentLoaded', () => {
    const questions = Array.from(document.querySelectorAll('.web-faq-question'));
    const groups = Array.from(document.querySelectorAll('.web-faq-group'));
    const tabs = Array.from(document.querySelectorAll('.web-faq-tab'));
    const searchForm = document.getElementById('faq-search-form');
    const searchInput = document.getElementById('faq-search-input');
    const emptyState = document.getElementById('faq-empty');

    // Accordion expand/collapse.
    questions.forEach((btn) => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.web-faq-item');
            const answer = item.querySelector('.web-faq-answer');
            const isOpen = item.classList.contains('open');

            item.classList.toggle('open', !isOpen);
            answer.classList.toggle('hidden', isOpen);
        });
    });

    let activeCategory = 'all';
    let activeQuery = '';

    function applyFilters() {
        let visibleCount = 0;

        groups.forEach((group) => {
            const matchesCategory = activeCategory === 'all' || group.dataset.category === activeCategory;
            let groupHasVisibleItem = false;

            group.querySelectorAll('.web-faq-item').forEach((item) => {
                const text = item.textContent.toLowerCase();
                const matchesQuery = !activeQuery || text.includes(activeQuery);
                const visible = matchesCategory && matchesQuery;

                item.classList.toggle('hidden', !visible);
                if (visible) {
                    groupHasVisibleItem = true;
                    visibleCount += 1;
                }
            });

            group.classList.toggle('hidden', !groupHasVisibleItem);
        });

        emptyState?.classList.toggle('hidden', visibleCount > 0);
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((t) => t.classList.remove('active'));
            tab.classList.add('active');
            activeCategory = tab.dataset.category;
            applyFilters();
        });
    });

    searchForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        activeQuery = (searchInput?.value || '').trim().toLowerCase();
        applyFilters();
    });

    searchInput?.addEventListener('input', () => {
        activeQuery = searchInput.value.trim().toLowerCase();
        applyFilters();
    });
});
