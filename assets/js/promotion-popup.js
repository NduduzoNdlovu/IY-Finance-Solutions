(() => {
    const popup = document.querySelector('[data-promo-popup]');
    if (!popup) return;

    const sessionKey = 'iy-promotion-seen-v1';
    const closeButton = popup.querySelector('.iy-promo__close');
    let previouslyFocused = null;

    const openPopup = () => {
        previouslyFocused = document.activeElement;
        popup.hidden = false;
        document.body.classList.add('iy-promo-open');
        closeButton?.focus();
    };

    const closePopup = () => {
        popup.hidden = true;
        document.body.classList.remove('iy-promo-open');
        sessionStorage.setItem(sessionKey, 'true');
        previouslyFocused?.focus?.();
    };

    popup.querySelectorAll('[data-promo-close]').forEach((element) => {
        element.addEventListener('click', closePopup);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !popup.hidden) closePopup();
    });

    if (!sessionStorage.getItem(sessionKey)) {
        window.setTimeout(openPopup, 700);
    }
})();
