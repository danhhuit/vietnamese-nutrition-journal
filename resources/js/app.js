

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

window.toggleNutritionTheme = () => {
    const dark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('nutrition-theme', dark ? 'dark' : 'light');
};

const loadingOverlay = () => document.getElementById('global-loading');
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.noLoading !== undefined || !form.checkValidity()) return;
    const overlay = loadingOverlay();
    if (overlay) overlay.hidden = false;
});
document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link || link.target === '_blank' || link.hasAttribute('download') || link.origin !== window.location.origin || link.hash) return;
    const overlay = loadingOverlay();
    if (overlay) overlay.hidden = false;
});
window.addEventListener('pageshow', () => {
    const overlay = loadingOverlay();
    if (overlay) overlay.hidden = true;
});

window.enableNutritionReminders = async (button) => {
    if (!('Notification' in window)) {
        button.textContent = 'Trình duyệt không hỗ trợ';
        return;
    }

    const permission = await Notification.requestPermission();
    button.textContent = permission === 'granted' ? 'Đã cho phép thông báo' : 'Thông báo chưa được cho phép';
};

async function showReminder(key, title, body) {
    const today = new Date().toISOString().slice(0, 10);
    const storageKey = `nutrition-reminder-${key}-${today}`;
    if (localStorage.getItem(storageKey) || Notification.permission !== 'granted') return;

    localStorage.setItem(storageKey, 'shown');
    const registration = await navigator.serviceWorker.ready;
    await registration.showNotification(title, { body, icon: '/favicon.ico', tag: storageKey });
}

async function checkNutritionReminders() {
    if (!('serviceWorker' in navigator) || !('Notification' in window)) return;

    let reminders = {};
    try {
        reminders = JSON.parse(document.body.dataset.reminders || '{}');
    } catch {
        return;
    }

    const now = new Date();
    if (reminders.meals && now.getMinutes() < 10 && [7, 12, 18].includes(now.getHours())) {
        await showReminder(`meal-${now.getHours()}`, 'Đến giờ ăn', 'Hãy chọn khẩu phần phù hợp và ghi lại bữa ăn của bạn.');
    }
    if (reminders.water && now.getMinutes() < 10 && now.getHours() >= 8 && now.getHours() <= 20 && now.getHours() % 2 === 0) {
        await showReminder(`water-${now.getHours()}`, 'Nhắc uống nước', 'Uống một ly nước và cập nhật nhật ký nước nhé.');
    }
}

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').then(() => {
        checkNutritionReminders();
        setInterval(checkNutritionReminders, 60_000);
    });
}
