class Calendar {
    constructor(containerElement, reminders) {
        this.container = containerElement;
        this.currentDate = new Date();
        this.displayDate = new Date();
        this.reminders = reminders;
        console.log('Initialized calendar with reminders:', reminders); // Debug log
        this.init();
    }

    init() {
        this.render();
        this.attachEventListeners();
    }

    formatMonth(month) {
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return months[month];
    }

    formatDate(year, month, day) {
        month = month + 1; // JavaScript months are 0-based
        return `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    }

    getRemindersForDate(date) {
        console.log('Getting reminders for date:', date, 'Available reminders:', this.reminders[date]); // Debug log
        return this.reminders[date] || [];
    }

    render() {
        const year = this.displayDate.getFullYear();
        const month = this.displayDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        const firstDayIndex = firstDay.getDay();
        const lastDayDate = lastDay.getDate();
        
        const prevLastDay = new Date(year, month, 0).getDate();
        const nextDays = 7 - ((firstDayIndex + lastDayDate) % 7);
        
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        let html = `
            <div class="calendar-header">
                <div class="calendar-controls">
                    <button class="prev-month">&lt;</button>
                </div>
                <h2>${this.formatMonth(month)} ${year}</h2>
                <div class="calendar-controls">
                    <button class="next-month">&gt;</button>
                </div>
            </div>
            <div class="calendar-grid">
        `;

        // Add day headers
        days.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        // Previous month's days
        for (let x = firstDayIndex - 1; x >= 0; x--) {
            const day = prevLastDay - x;
            const prevMonth = month - 1 < 0 ? 11 : month - 1;
            const prevYear = month - 1 < 0 ? year - 1 : year;
            const date = this.formatDate(prevYear, prevMonth, day);
            html += `<div class="calendar-day inactive" data-date="${date}">${day}</div>`;
        }

        // Current month's days
        for (let i = 1; i <= lastDayDate; i++) {
            const isToday = i === this.currentDate.getDate() && 
                           month === this.currentDate.getMonth() && 
                           year === this.currentDate.getFullYear();
            
            const date = this.formatDate(year, month, i);
            const reminders = this.getRemindersForDate(date);
            
            let reminderHtml = '';
            if (reminders.length > 0) {
                reminderHtml = reminders.map(reminder => `
                    <div class="calendar-reminder" 
                         data-bs-toggle="modal" 
                         data-bs-target="#reminderModal" 
                         data-reminder='${JSON.stringify(reminder)}'>
                        ${reminder.title}
                    </div>
                `).join('');
            }

            html += `
                <div class="calendar-day${isToday ? ' today' : ''}" data-date="${date}">
                    <span class="day-number">${i}</span>
                    <div class="reminder-list">
                        ${reminderHtml}
                    </div>
                </div>
            `;
        }

        // Next month's days
        for (let j = 1; j <= nextDays && nextDays < 7; j++) {
            const nextMonth = month + 1 > 11 ? 0 : month + 1;
            const nextYear = month + 1 > 11 ? year + 1 : year;
            const date = this.formatDate(nextYear, nextMonth, j);
            html += `<div class="calendar-day inactive" data-date="${date}">${j}</div>`;
        }

        html += '</div>';
        this.container.innerHTML = html;
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Previous month button
        this.container.querySelector('.prev-month').addEventListener('click', () => {
            this.displayDate.setMonth(this.displayDate.getMonth() - 1);
            this.render();
        });

        // Next month button
        this.container.querySelector('.next-month').addEventListener('click', () => {
            this.displayDate.setMonth(this.displayDate.getMonth() + 1);
            this.render();
        });

        // Day click event (for admins)
        const isAdmin = document.body.classList.contains('is-admin');
        if (isAdmin) {
            this.container.querySelectorAll('.calendar-day').forEach(day => {
                day.addEventListener('click', (e) => {
                    if (!e.target.closest('.calendar-reminder')) {
                        const date = day.dataset.date;
                        window.location.href = `/reminders/create?date=${date}`;
                    }
                });
            });
        }
    }
}

// Initialize calendar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.querySelector('.calendar-container');
    if (calendarContainer) {
        const reminders = JSON.parse(calendarContainer.dataset.reminders || '{}');
        new Calendar(calendarContainer, reminders);
    }
});
