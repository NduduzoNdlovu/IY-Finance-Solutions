(() => {
    "use strict";

    const nav = document.querySelector("[data-nav]");
    const navToggle = document.querySelector("[data-nav-toggle]");
    const dropdown = document.querySelector(".nav-dropdown");
    const dropdownToggle = document.querySelector("[data-dropdown-toggle]");
    const navWrap = document.querySelector(".main-nav-wrap");

    const closeNavigation = () => {
        nav?.classList.remove("open");
        navToggle?.setAttribute("aria-expanded", "false");
        document.body.classList.remove("nav-open");
    };

    navToggle?.addEventListener("click", () => {
        const isOpen = nav?.classList.toggle("open") ?? false;
        navToggle.setAttribute("aria-expanded", String(isOpen));
        document.body.classList.toggle("nav-open", isOpen);
    });

    dropdownToggle?.addEventListener("click", () => {
        const isOpen = dropdown?.classList.toggle("open") ?? false;
        dropdownToggle.setAttribute("aria-expanded", String(isOpen));
    });

    nav?.querySelectorAll("a").forEach((link) => link.addEventListener("click", closeNavigation));

    document.addEventListener("click", (event) => {
        if (dropdown && !dropdown.contains(event.target)) {
            dropdown.classList.remove("open");
            dropdownToggle?.setAttribute("aria-expanded", "false");
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeNavigation();
            dropdown?.classList.remove("open");
            dropdownToggle?.setAttribute("aria-expanded", "false");
        }
    });

    if (navWrap) {
        let lastSticky = false;
        const updateHeader = () => {
            const sticky = window.scrollY > 170;
            if (sticky !== lastSticky) {
                navWrap.classList.toggle("is-sticky", sticky);
                lastSticky = sticky;
            }
        };
        updateHeader();
        window.addEventListener("scroll", updateHeader, { passive: true });
    }

    document.querySelectorAll("[data-accordion] .faq-item > button").forEach((button) => {
        button.addEventListener("click", () => {
            const item = button.closest(".faq-item");
            const container = button.closest("[data-accordion]");
            const willOpen = !item?.classList.contains("open");

            container?.querySelectorAll(".faq-item.open").forEach((openItem) => {
                openItem.classList.remove("open");
                openItem.querySelector("button")?.setAttribute("aria-expanded", "false");
            });

            if (willOpen) {
                item?.classList.add("open");
                button.setAttribute("aria-expanded", "true");
            }
        });
    });

    const revealItems = document.querySelectorAll(".reveal");
    if ("IntersectionObserver" in window && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: "0px 0px -30px" });
        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add("visible"));
    }
})();


