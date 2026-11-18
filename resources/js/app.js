

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

window.sortRestaurantsByDistance = (button) => {
    const status = document.getElementById('distance-status');
    if (!navigator.geolocation) {
        status.textContent = 'Trình duyệt không hỗ trợ định vị.';
        return;
    }

    button.disabled = true;
    status.textContent = 'Đang lấy vị trí của bạn…';
    navigator.geolocation.getCurrentPosition(({ coords }) => {
        const radians = (degrees) => degrees * Math.PI / 180;
        const distance = (latitude, longitude) => {
            const dLat = radians(latitude - coords.latitude);
            const dLon = radians(longitude - coords.longitude);
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(radians(coords.latitude)) * Math.cos(radians(latitude)) * Math.sin(dLon / 2) ** 2;
            return 6371 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        };
        const grid = document.getElementById('restaurant-grid');
        const cards = [...grid.querySelectorAll('[data-restaurant-card]')];
        cards.forEach((card) => {
            const latitude = Number(card.dataset.latitude);
            const longitude = Number(card.dataset.longitude);
            card.distance = latitude && longitude ? distance(latitude, longitude) : Number.POSITIVE_INFINITY;
            card.querySelector('[data-distance]').textContent = Number.isFinite(card.distance) ? `${card.distance.toFixed(1)} km từ bạn` : 'Chưa có tọa độ';
        });
        cards.sort((first, second) => first.distance - second.distance).forEach((card) => grid.append(card));
        status.textContent = 'Đã sắp xếp các quán trên trang hiện tại theo khoảng cách.';
        button.disabled = false;
    }, () => {
        status.textContent = 'Không thể lấy vị trí. Hãy cho phép quyền định vị hoặc mở bản đồ từ trang quán.';
        button.disabled = false;
    }, { enableHighAccuracy: false, timeout: 10000 });
};

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
