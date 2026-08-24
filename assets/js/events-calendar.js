(() => {
    'use strict';

    const app = document.querySelector('[data-calendar-app]');
    const dataNode = document.getElementById('events-calendar-data');
    if (!app || !dataNode) return;

    let events = [];
    try {
        events = JSON.parse(dataNode.textContent || '[]');
    } catch (error) {
        return;
    }

    const grid = app.querySelector('[data-calendar-grid]');
    const title = app.querySelector('[data-calendar-title]');
    const selectedDate = app.querySelector('[data-selected-date]');
    const selectedEvents = app.querySelector('[data-selected-events]');
    const previous = app.querySelector('[data-calendar-previous]');
    const next = app.querySelector('[data-calendar-next]');
    const todayButton = app.querySelector('[data-calendar-today]');
    const today = new Date();
    let viewDate = new Date(today.getFullYear(), today.getMonth(), 1);
    let selectedKey = '';

    const pad = (number) => String(number).padStart(2, '0');
    const keyForDate = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    const parseKey = (key) => {
        const [year, month, day] = key.split('-').map(Number);
        return new Date(year, month - 1, day);
    };
    const longDate = (key) => new Intl.DateTimeFormat('en-ZA', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(parseKey(key));
    const eventsForDate = (key) => events.filter((event) => {
        const end = event.endDate || event.startDate;
        return event.startDate <= key && end >= key;
    });

    const showSelectedDate = (key) => {
        selectedKey = key;
        if (selectedDate) selectedDate.textContent = longDate(key);
        if (!selectedEvents) return;
        selectedEvents.textContent = '';
        const matches = eventsForDate(key);
        if (!matches.length) {
            const message = document.createElement('p');
            message.className = 'calendar-help';
            message.textContent = 'No published IY events on this date.';
            selectedEvents.append(message);
            return;
        }

        const list = document.createElement('div');
        list.className = 'calendar-selection-list';
        matches.forEach((event) => {
            const link = document.createElement('a');
            link.className = 'calendar-selection-item';
            link.href = event.anchor;
            const heading = document.createElement('strong');
            heading.textContent = event.title;
            const details = document.createElement('span');
            details.textContent = [event.time, event.location, event.statusLabel].filter(Boolean).join(' · ');
            link.append(heading, details);
            list.append(link);
        });
        selectedEvents.append(list);
    };

    const render = () => {
        if (!grid || !title) return;
        grid.textContent = '';
        title.textContent = new Intl.DateTimeFormat('en-ZA', { month: 'long', year: 'numeric' }).format(viewDate);

        const monthStart = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
        const mondayOffset = (monthStart.getDay() + 6) % 7;
        const firstCell = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1 - mondayOffset);

        for (let index = 0; index < 42; index += 1) {
            const cellDate = new Date(firstCell.getFullYear(), firstCell.getMonth(), firstCell.getDate() + index);
            const key = keyForDate(cellDate);
            const matches = eventsForDate(key);
            const element = document.createElement(matches.length ? 'button' : 'div');
            element.className = 'calendar-day';
            if (cellDate.getMonth() !== viewDate.getMonth()) element.classList.add('is-outside');
            if (key === keyForDate(today)) element.classList.add('is-today');
            if (key === selectedKey) element.classList.add('is-selected');
            if (matches.length) {
                element.type = 'button';
                element.setAttribute('aria-label', `${longDate(key)}, ${matches.length} ${matches.length === 1 ? 'event' : 'events'}`);
                element.addEventListener('click', () => {
                    showSelectedDate(key);
                    render();
                });
            }

            const day = document.createElement('span');
            day.className = 'calendar-day-number';
            day.textContent = String(cellDate.getDate());
            element.append(day);

            if (matches.length) {
                const dots = document.createElement('span');
                dots.className = 'calendar-event-dots';
                matches.slice(0, 3).forEach(() => {
                    const dot = document.createElement('span');
                    dot.className = 'calendar-event-dot';
                    dots.append(dot);
                });
                element.append(dots);
            }
            grid.append(element);
        }
    };

    previous?.addEventListener('click', () => {
        viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() - 1, 1);
        render();
    });
    next?.addEventListener('click', () => {
        viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 1);
        render();
    });
    todayButton?.addEventListener('click', () => {
        viewDate = new Date(today.getFullYear(), today.getMonth(), 1);
        showSelectedDate(keyForDate(today));
        render();
    });

    render();
    if (eventsForDate(keyForDate(today)).length) showSelectedDate(keyForDate(today));
})();

