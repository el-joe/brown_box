// Home page behaviour: flash sale countdown timer.

function startCountdown(container) {
    const endsAt = container.dataset.flashEnds;

    if (!endsAt) {
        return;
    }

    const end = new Date(endsAt).getTime();
    const boxes = {
        days: container.querySelector('[data-unit="days"]'),
        hours: container.querySelector('[data-unit="hours"]'),
        minutes: container.querySelector('[data-unit="minutes"]'),
        seconds: container.querySelector('[data-unit="seconds"]'),
    };

    const tick = () => {
        const distance = end - Date.now();

        if (distance <= 0) {
            Object.values(boxes).forEach((el) => el && (el.textContent = '00'));
            clearInterval(timer);
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);
        const seconds = Math.floor((distance / 1000) % 60);

        const pad = (n) => String(n).padStart(2, '0');

        if (boxes.days) boxes.days.textContent = pad(days);
        if (boxes.hours) boxes.hours.textContent = pad(hours);
        if (boxes.minutes) boxes.minutes.textContent = pad(minutes);
        if (boxes.seconds) boxes.seconds.textContent = pad(seconds);
    };

    tick();
    const timer = setInterval(tick, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const flashSection = document.querySelector('[data-flash-ends]');

    if (flashSection) {
        startCountdown(flashSection);
    }
});
