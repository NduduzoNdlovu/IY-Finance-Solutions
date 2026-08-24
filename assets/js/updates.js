(() => {
    'use strict';

    const buttons = [...document.querySelectorAll('[data-update-filter]')];
    const cards = [...document.querySelectorAll('[data-update-card]')];
    const result = document.querySelector('[data-filter-result]');
    if (!buttons.length || !cards.length) return;

    const applyFilter = (category) => {
        let visible = 0;
        cards.forEach((card) => {
            const show = category === 'all' || card.dataset.category === category;
            card.hidden = !show;
            if (show) visible += 1;
        });

        buttons.forEach((button) => {
            const active = button.dataset.updateFilter === category;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', String(active));
        });
        if (result) result.textContent = `${visible} ${visible === 1 ? 'update' : 'updates'} shown`;
    };

    buttons.forEach((button) => button.addEventListener('click', () => applyFilter(button.dataset.updateFilter || 'all')));
    applyFilter('all');
})();

