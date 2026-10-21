(() => {
    "use strict";

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    document.querySelectorAll(".services-grid, .process-grid").forEach((grid) => {
        grid.querySelectorAll(":scope > .reveal").forEach((item, index) => {
            item.style.setProperty("--reveal-delay", `${Math.min(index, 5) * 85}ms`);
        });
    });



    const heroDesigns = Array.from(document.querySelectorAll("[data-home-hero-design]"));
    const heroDots = Array.from(document.querySelectorAll("[data-home-hero-dot]"));
    const heroTimer = document.querySelector(".home-hero-timer");
    const heroSwitcher = document.querySelector("[data-home-hero-designs]");
    const heroInterval = 8000;
    let heroIndex = 0;
    let heroTimeout = null;

    const setHeroDesign = (index, restart = true) => {
        if (!heroDesigns.length) return;
        heroIndex = ((index % heroDesigns.length) + heroDesigns.length) % heroDesigns.length;

        heroDesigns.forEach((design, designIndex) => {
            const active = designIndex === heroIndex;
            design.classList.toggle("is-active", active);
            design.setAttribute("aria-hidden", active ? "false" : "true");
        });

        heroDots.forEach((dot, dotIndex) => {
            const active = dotIndex === heroIndex;
            dot.classList.toggle("is-active", active);
            dot.setAttribute("aria-pressed", active ? "true" : "false");
        });

        if (heroTimer) {
            heroTimer.classList.remove("is-running");
            void heroTimer.offsetWidth;
            if (!reduceMotion) heroTimer.classList.add("is-running");
        }

        if (restart && !reduceMotion) {
            window.clearTimeout(heroTimeout);
            heroTimeout = window.setTimeout(() => setHeroDesign(heroIndex + 1), heroInterval);
        }
    };

    if (heroDesigns.length > 1) {
        setHeroDesign(0);

        heroDots.forEach((dot) => {
            dot.addEventListener("click", () => {
                setHeroDesign(Number.parseInt(dot.dataset.homeHeroDot ?? "0", 10));
            });
        });

        if (heroSwitcher && !reduceMotion) {
            heroSwitcher.addEventListener("mouseenter", () => window.clearTimeout(heroTimeout));
            heroSwitcher.addEventListener("mouseleave", () => {
                window.clearTimeout(heroTimeout);
                heroTimeout = window.setTimeout(() => setHeroDesign(heroIndex + 1), heroInterval);
            });
        }
    }

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
