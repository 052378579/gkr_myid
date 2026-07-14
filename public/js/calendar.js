document.addEventListener('DOMContentLoaded', () => {
    const calendarMonthYearLabel = document.getElementById('calendarMonthYearLabel');
    const calendarBody = document.getElementById('calendarBody');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');

    if (!calendarMonthYearLabel || !calendarBody) return;

    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth(); 
    let currentYear = currentDate.getFullYear();
    const thisYear = currentYear;
    const todayDate = currentDate.getDate();
    const todayMonth = currentDate.getMonth();
    const todayYear = currentDate.getFullYear();

    // Prevent dropdown close
    const calendarDropdownWrap = document.getElementById('calendarDropdownWrap');
    if (calendarDropdownWrap) {
        calendarDropdownWrap.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    // Mutually exclusive dropdowns
    const calToggle = document.getElementById('calendarDropdownToggle');
    const appsToggle = document.getElementById('appsDropdownToggle');
    
    if (calToggle && appsToggle) {
        calToggle.addEventListener('show.bs.dropdown', () => {
            if (typeof bootstrap !== 'undefined') {
                const appsInstance = bootstrap.Dropdown.getInstance(appsToggle);
                if (appsInstance) appsInstance.hide();
            }
        });
        appsToggle.addEventListener('show.bs.dropdown', () => {
            if (typeof bootstrap !== 'undefined') {
                const calInstance = bootstrap.Dropdown.getInstance(calToggle);
                if (calInstance) calInstance.hide();
            }
        });
    }

    function getWeekNumber(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
        return weekNo;
    }

    function renderCalendar(month, year) {
        if (!calendarMonthYearLabel) return;
        calendarMonthYearLabel.innerText = monthNames[month] + " " + year;
        
        if (prevMonthBtn) prevMonthBtn.style.visibility = (month <= 0) ? 'hidden' : 'visible';
        if (nextMonthBtn) nextMonthBtn.style.visibility = (month >= 11) ? 'hidden' : 'visible';

        let firstDay = new Date(year, month, 1).getDay(); // 0 is Sunday
        let adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1; // 0 is Monday
        let daysInMonth = new Date(year, month + 1, 0).getDate();
        let daysInPrevMonth = new Date(year, month, 0).getDate();

        let html = '';
        let date = 1;
        let nextMonthDate = 1;

        for (let i = 0; i < 6; i++) {
            let rowHtml = '<tr>';
            
            let isCurrentWeek = false;
            let cellsHtml = '';
            
            for (let j = 0; j < 7; j++) {
                let isWeekend = (j === 5 || j === 6);
                let textColor = isWeekend ? '#dc3545' : '#212529';

                if (i === 0 && j < adjustedFirstDay) {
                    let prevDate = daysInPrevMonth - (adjustedFirstDay - 1 - j);
                    cellsHtml += `<td><span class="text-muted" style="opacity: 0.5;">${prevDate}</span></td>`;
                } else if (date > daysInMonth) {
                    cellsHtml += `<td><span class="text-muted" style="opacity: 0.5;">${nextMonthDate}</span></td>`;
                    nextMonthDate++;
                } else {
                    let isToday = (date === todayDate && month === todayMonth && year === todayYear);
                    if (isToday) {
                        isCurrentWeek = true;
                        let bgColor = isWeekend ? '#dc3545' : '#2B3385';
                        let shadowColor = isWeekend ? 'rgba(220,53,69,0.3)' : 'rgba(43,51,133,0.3)';
                        cellsHtml += `<td><span style="background-color: ${bgColor}; color: white; width: 24px; height: 24px; line-height: 24px; border-radius: 50%; display: inline-block; box-shadow: 0 2px 4px ${shadowColor};">${date}</span></td>`;
                    } else {
                        cellsHtml += `<td><span style="color: ${textColor};">${date}</span></td>`;
                    }
                    
                    date++;
                }
            }
            
            let weekDateFallback = (i === 0 && date > 1) ? 1 : (date <= daysInMonth ? date - 1 : daysInMonth);
            let currentWeekNumber = getWeekNumber(new Date(year, month, weekDateFallback));
            
            let isCurrentWeekWeekend = isCurrentWeek && (new Date().getDay() === 0 || new Date().getDay() === 6);
            let weekBgColor = isCurrentWeekWeekend ? '#dc3545' : '#2B3385';
            
            let weekStyle = isCurrentWeek ? `background-color: ${weekBgColor}; color: white; width: 24px; height: 24px; line-height: 24px; border-radius: 50%; display: inline-block; font-weight: bold;` : "color: #6c757d; font-weight: bold;";
            
            rowHtml += `<td><span style="${weekStyle}">${currentWeekNumber}</span></td>`;
            rowHtml += cellsHtml;
            rowHtml += '</tr>';
            
            html += rowHtml;

            if (date > daysInMonth && i >= 4) {
                break; // Stop adding rows if month is fully rendered
            }
        }
        
        if(calendarBody) calendarBody.innerHTML = html;
    }

    renderCalendar(currentMonth, currentYear);

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentMonth > 0) {
                currentMonth--;
                renderCalendar(currentMonth, currentYear);
            }
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentMonth < 11) {
                currentMonth++;
                renderCalendar(currentMonth, currentYear);
            }
        });
    }
});
