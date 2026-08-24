(() => {
    "use strict";

    const dialog = document.querySelector("[data-lightbox]");
    const image = dialog?.querySelector("[data-lightbox-image]");
    const caption = dialog?.querySelector("[data-lightbox-caption]");
    const items = [...document.querySelectorAll("[data-gallery-item]")];
    let currentIndex = 0;

    if (!dialog || !image || items.length === 0) return;

    const showImage = (index) => {
        currentIndex = (index + items.length) % items.length;
        const item = items[currentIndex];
        image.src = item.dataset.src ?? "";
        image.alt = item.dataset.alt ?? "Gallery photograph";
        if (caption) caption.textContent = `${currentIndex + 1} of ${items.length}`;
    };

    items.forEach((item, index) => {
        item.addEventListener("click", () => {
            showImage(index);
            dialog.showModal();
        });
    });

    dialog.querySelector("[data-lightbox-close]")?.addEventListener("click", () => dialog.close());
    dialog.querySelector("[data-lightbox-previous]")?.addEventListener("click", () => showImage(currentIndex - 1));
    dialog.querySelector("[data-lightbox-next]")?.addEventListener("click", () => showImage(currentIndex + 1));

    dialog.addEventListener("click", (event) => {
        if (event.target === dialog) dialog.close();
    });

    dialog.addEventListener("keydown", (event) => {
        if (event.key === "ArrowLeft") showImage(currentIndex - 1);
        if (event.key === "ArrowRight") showImage(currentIndex + 1);
    });
})();

