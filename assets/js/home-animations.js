(() => {
    "use strict";

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    document.querySelectorAll(".services-grid, .process-grid").forEach((grid) => {
        grid.querySelectorAll(":scope > .reveal").forEach((item, index) => {
            item.style.setProperty("--reveal-delay", `${Math.min(index, 5) * 85}ms`);
        });
    });

    const counters = document.querySelectorAll("[data-count-to]");
    if (reduceMotion || !("IntersectionObserver" in window)) {
        counters.forEach((counter) => {
            counter.textContent = counter.dataset.countTo ?? counter.textContent;
        });
        return;
    }

    const countObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const counter = entry.target;
            const target = Number.parseInt(counter.dataset.countTo ?? "0", 10);
            const duration = 2000; 
            const startedAt = performance.now();

            const updateCounter = (now) => {
                const progress = Math.min((now - startedAt) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                counter.textContent = String(Math.round(target * eased));

                if (progress < 1) requestAnimationFrame(updateCounter);
            };

            counter.textContent = "0";
            requestAnimationFrame(updateCounter);
            observer.unobserve(counter);
        });
    }, { threshold: .65 });

    counters.forEach((counter) => countObserver.observe(counter));
})();
