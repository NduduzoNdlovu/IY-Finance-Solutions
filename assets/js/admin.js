(() => {
    "use strict";

    const input = document.querySelector("[data-file-input]");
    const selectedFiles = document.querySelector("[data-selected-files]");
    const uploadForm = document.querySelector("[data-upload-form]");
    const uploadButton = document.querySelector("[data-upload-button]");

    input?.addEventListener("change", () => {
        const files = [...input.files];
        if (!selectedFiles) return;

        if (files.length === 0) {
            selectedFiles.textContent = "";
            return;
        }

        selectedFiles.innerHTML = "";
        const summary = document.createElement("strong");
        summary.textContent = `${files.length} ${files.length === 1 ? "image" : "images"} selected`;
        selectedFiles.appendChild(summary);

        const list = document.createElement("ul");
        files.slice(0, 5).forEach((file) => {
            const item = document.createElement("li");
            item.textContent = file.name;
            list.appendChild(item);
        });
        if (files.length > 5) {
            const item = document.createElement("li");
            item.textContent = `and ${files.length - 5} more`;
            list.appendChild(item);
        }
        selectedFiles.appendChild(list);
    });

    uploadForm?.addEventListener("submit", () => {
        if (!uploadButton) return;
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Uploading…';
    });

    document.querySelectorAll("[data-delete-form]").forEach((form) => {
        form.addEventListener("submit", (event) => {
            const message = form.dataset.deleteMessage || "Remove this image from the public gallery? This cannot be undone.";
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
})();

