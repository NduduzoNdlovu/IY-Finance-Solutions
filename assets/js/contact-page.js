(() => {
    'use strict';

    const mapElement = document.querySelector('[data-branch-map]');
    const dataElement = document.getElementById('branch-map-data');
    const filterButtons = [...document.querySelectorAll('[data-branch-filters] [data-province]')];
    const cards = [...document.querySelectorAll('[data-branch-card]')];
    const statusElement = document.querySelector('[data-branch-status]');

    if (!mapElement || !dataElement) return;

    let branches;
    try {
        branches = JSON.parse(dataElement.textContent || '[]');
    } catch (error) {
        branches = [];
    }

    if (!Array.isArray(branches) || branches.length === 0) return;

    let map = null;
    let markerGroup = null;
    const markers = new Map();
    let activeProvince = 'all';

    const loadStyle = (href) => new Promise((resolve, reject) => {
        const existing = document.querySelector(`link[href="${href}"]`);
        if (existing) {
            resolve();
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.onload = resolve;
        link.onerror = reject;
        document.head.appendChild(link);
    });

    const loadScript = (src) => new Promise((resolve, reject) => {
        if (window.L) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = src;
        script.defer = true;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const visibleBranches = () => branches.filter((branch) => (
        activeProvince === 'all' || branch.provinceKey === activeProvince
    ));

    const fitVisibleMarkers = () => {
        if (!map || !window.L) return;

        const visible = visibleBranches();
        const bounds = visible
            .map((branch) => [branch.latitude, branch.longitude])
            .filter(([latitude, longitude]) => Number.isFinite(latitude) && Number.isFinite(longitude));

        if (bounds.length === 1) {
            map.setView(bounds[0], 14, { animate: true });
        } else if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [44, 44], maxZoom: 11, animate: true });
        }
    };

    const updateMarkers = () => {
        if (!markerGroup) return;
        markerGroup.clearLayers();

        visibleBranches().forEach((branch) => {
            const marker = markers.get(branch.slug);
            if (marker) marker.addTo(markerGroup);
        });

        fitVisibleMarkers();
    };

    const setSelectedCard = (slug, shouldScroll = false) => {
        cards.forEach((card) => {
            const isSelected = card.dataset.branchSlug === slug;
            card.classList.toggle('is-selected', isSelected);

            if (isSelected && shouldScroll) {
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                card.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            }
        });
    };

    const setProvince = (province) => {
        activeProvince = province;

        filterButtons.forEach((button) => {
            const isActive = button.dataset.province === province;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });

        let count = 0;
        cards.forEach((card) => {
            const isVisible = province === 'all' || card.dataset.province === province;
            card.hidden = !isVisible;
            if (isVisible) count += 1;
        });

        if (statusElement) {
            const activeButton = filterButtons.find((button) => button.dataset.province === province);
            const label = province === 'all' ? 'all' : (activeButton?.textContent || '').replace(/\d+\s*$/, '').trim();
            statusElement.textContent = `Showing ${province === 'all' ? 'all ' : ''}${count} ${count === 1 ? 'location' : 'locations'}${province === 'all' ? '' : ` in ${label}`}`;
        }

        setSelectedCard('');
        updateMarkers();
    };

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => setProvince(button.dataset.province || 'all'));
    });

    document.querySelectorAll('[data-show-on-map]').forEach((button) => {
        button.addEventListener('click', () => {
            const slug = button.dataset.showOnMap;
            const branch = branches.find((item) => item.slug === slug);
            const marker = markers.get(slug);

            if (!branch || !map || !marker) {
                mapElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            map.setView([branch.latitude, branch.longitude], 15, { animate: true });
            marker.openPopup();
            setSelectedCard(slug);
            mapElement.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'center'
            });
        });
    });

    const initialiseMap = async () => {
        try {
            await Promise.all([
                loadStyle('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
                loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js')
            ]);

            if (!window.L) throw new Error('Leaflet did not load.');

            map = window.L.map(mapElement, {
                scrollWheelZoom: false,
                zoomControl: true
            });

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            markerGroup = window.L.layerGroup().addTo(map);

            branches.forEach((branch) => {
                const icon = window.L.divIcon({
                    className: `iy-map-marker${branch.featured ? ' is-main' : ''}`,
                    html: '<span><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>',
                    iconSize: [42, 42],
                    iconAnchor: [21, 40],
                    popupAnchor: [0, -39]
                });

                const marker = window.L.marker([branch.latitude, branch.longitude], {
                    icon,
                    title: branch.name
                });

                marker.bindPopup(`
                    <div class="iy-map-popup">
                        <strong>${escapeHtml(branch.name)}</strong>
                        <span>${escapeHtml(branch.address)}</span>
                        <a href="#branch-${encodeURIComponent(branch.slug)}" data-popup-branch="${escapeHtml(branch.slug)}">View contact details</a>
                    </div>
                `);

                marker.on('click', () => setSelectedCard(branch.slug));
                marker.on('popupopen', (event) => {
                    const popupLink = event.popup.getElement()?.querySelector('[data-popup-branch]');
                    popupLink?.addEventListener('click', (clickEvent) => {
                        clickEvent.preventDefault();
                        setSelectedCard(branch.slug, true);
                    }, { once: true });
                });

                markers.set(branch.slug, marker);
                marker.addTo(markerGroup);
            });

            mapElement.closest('.branch-map-wrap')?.classList.add('has-map');
            fitVisibleMarkers();
            window.setTimeout(() => map.invalidateSize(), 150);
        } catch (error) {
            mapElement.setAttribute('aria-label', 'Interactive branch map unavailable. Use the branch cards below for directions.');
        }
    };

    initialiseMap();
})();
