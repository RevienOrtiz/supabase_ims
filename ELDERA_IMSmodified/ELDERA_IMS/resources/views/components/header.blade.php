<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
<style>
    body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5faff;
        }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 20px 30px;
        position: fixed;
        top: 0;
        left: 250px; /* matches sidebar width */
        right: 0;
        height: 60px;
        z-index: 20;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-bottom: 1px solid #e0e0e0;
    }

    .dashboard-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        font-size: 18px;
    }

    .dashboard-icon {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 2px;
        width: 20px;
        height: 20px;
    }

    .dashboard-icon div {
        background: #555;
        border-radius: 2px;
    }

    .dashboard-text {
        font-weight: bold;
        color: #333;
        font-size: 18px;
        text-transform: uppercase;
    }

    .user-section {
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    .calendar-icon-button {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
    }

    .calendar-icon-button:hover {
        background: #f0f0f0;
    }

    .calendar-icon-button.has-events::after {
        content: '';
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        background: #e31575;
        border-radius: 50%;
        border: 2px solid white;
    }

    .calendar-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: 1px solid #e0e0e0;
        min-width: 320px;
        max-width: 350px;
        z-index: 1001;
        display: none;
        margin-top: 8px;
        overflow: hidden;
    }

    .calendar-dropdown.show {
        display: block;
    }

    .calendar-dropdown-header {
        padding: 12px 16px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .calendar-dropdown-title {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .calendar-dropdown-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #666;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }

    .calendar-dropdown-close:hover {
        background: #e9ecef;
    }

    .calendar-dropdown-body {
        padding: 12px;
    }

    .mini-calendar {
        width: 100%;
        font-size: 12px;
        display: flex;
        flex-direction: column;
    }

    .mini-calendar-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .mini-calendar-nav-btn {
        background: #277da1;
        color: white;
        border: none;
        border-radius: 4px;
        width: 28px;
        height: 28px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: background 0.2s;
    }

    .mini-calendar-nav-btn:hover {
        background: #1e6b8c;
    }

    .mini-calendar-month-year {
        font-weight: bold;
        font-size: 14px;
        color: #333;
    }

    .mini-calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0;
        background: #f1f3f4;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 4px;
    }

    .mini-calendar-weekday {
        text-align: center;
        font-weight: bold;
        font-size: 10px;
        color: #666;
        padding: 6px 2px;
        border-right: 1px solid #e0e0e0;
    }

    .mini-calendar-weekday:last-child {
        border-right: none;
    }

    .mini-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0;
        background: white;
    }

    .mini-calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        position: relative;
        background: white;
        color: #333;
        min-height: 32px;
        border-right: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
        transition: background-color 0.2s ease;
    }

    .mini-calendar-day:nth-child(7n) {
        border-right: none;
    }

    .mini-calendar-day:hover {
        background: #f5f5f5;
    }

    .mini-calendar-day.today {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: bold;
        border: 2px solid #1976d2;
    }

    .mini-calendar-day.other-month {
        color: #bbb;
        background: #fafafa;
    }

    .mini-calendar-day.has-event {
        font-weight: bold;
    }

    .mini-calendar-day.event-general {
        background: #e8f5e8 !important;
        color: #2e7d32 !important;
    }

    .mini-calendar-day.event-health {
        background: #ffebee !important;
        color: #c62828 !important;
    }

    .mini-calendar-day.event-pension {
        background: #e3f2fd !important;
        color: #1565c0 !important;
    }

    .mini-calendar-day.event-id {
        background: #fffde7 !important;
        color: #f57f17 !important;
    }

    .mini-calendar-event-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        border: 1px solid white;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .icon-button {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .icon-button:hover {
        background: #f0f0f0;
    }

    .envelope-button {
        background: #555;
        color: white;
    }

    .user-icon {
        color: #555;
        font-size: 18px;
        cursor: pointer;
        transition: color 0.2s;
    }

    

    .admin-group {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background-color 0.2s;
        position: relative;
    }

    .admin-group:hover {
        background: #f0f0f0;
    }

    .admin-text {
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    /* Admin Dropdown Styles */
    .admin-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: 1px solid #e0e0e0;
        min-width: 180px;
        z-index: 1001;
        display: none;
        margin-top: 5px;
    }

    .admin-dropdown.show {
        display: block;
    }

    .admin-dropdown-item {
        padding: 12px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        font-size: 14px;
    }

    .admin-dropdown-item:hover {
        background: #f8f9fa;
    }

    .admin-dropdown-item:first-child {
        border-radius: 8px 8px 0 0;
    }

    .admin-dropdown-item:last-child {
        border-radius: 0 0 8px 8px;
    }

    .admin-dropdown-item.logout {
        color: #dc3545;
        border-top: 1px solid #e0e0e0;
    }

    .admin-dropdown-item.logout:hover {
        background: #fff5f5;
    }


    body {
        background-color: ##fff5f5;
    }
    
</style>
</head>
<body>

    <div class="header">
        <div class="dashboard-title">
            @if(isset($attributes) && $attributes->has('icon'))
                <div class="page-icon">
                    <i class="{{ $attributes->get('icon') }}"></i>
                </div>
            @else
                <div class="dashboard-icon">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            @endif
            <span class="dashboard-text">{{ isset($attributes) ? $attributes->get('title', 'Dashboard') : 'Dashboard' }}</span>
        </div>
        
        
        <div class="user-section">
            <div class="calendar-icon-button" id="calendarIcon" title="View Calendar">
                <i class="fas fa-calendar-alt"></i>
            </div>
            
            <!-- Calendar Dropdown -->
            <div class="calendar-dropdown" id="calendarDropdown">
                <div class="calendar-dropdown-header">
                    <span class="calendar-dropdown-title">Calendar</span>
                    <button class="calendar-dropdown-close" id="closeCalendar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="calendar-dropdown-body">
                    <div class="mini-calendar" id="miniCalendar">
                        <div class="mini-calendar-nav">
                            <button class="mini-calendar-nav-btn" id="prevMonth">&lt;</button>
                            <span class="mini-calendar-month-year" id="monthYear"></span>
                            <button class="mini-calendar-nav-btn" id="nextMonth">&gt;</button>
                        </div>
                        <div class="mini-calendar-weekdays">
                            <div class="mini-calendar-weekday">S</div>
                            <div class="mini-calendar-weekday">M</div>
                            <div class="mini-calendar-weekday">T</div>
                            <div class="mini-calendar-weekday">W</div>
                            <div class="mini-calendar-weekday">T</div>
                            <div class="mini-calendar-weekday">F</div>
                            <div class="mini-calendar-weekday">S</div>
                        </div>
                        <div class="mini-calendar-days" id="calendarDays">
                            <!-- Days will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-group" id="adminGroup">
                <i class="fas fa-user user-icon"></i>
                <span class="admin-text" id="adminText">Admin</span>
                
                <!-- Admin Dropdown -->
                <div class="admin-dropdown" id="adminDropdown">
                    {{-- <div class="admin-dropdown-item" onclick="window.location.href='{{ route('admin.profile') }}'">
                        <i class="fas fa-user-circle"></i>
                        Profile
                    </div> --}}
                    <div class="admin-dropdown-item">
                        <i class="fas fa-cog"></i>
                        Settings
                    </div>
                        <div class="admin-dropdown-item logout" id="logoutItem">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </div>
                </div>
            </div>
        </div>
    </div>

    

    @if(isset($slot))
        {{$slot}}
    @endif

    <!-- Include popup message modal -->
    @include('message.popup_message')

    <script>
        

        class AdminDropdown {
            constructor() {
                this.init();
            }

            init() {
                this.bindEvents();
            }

            bindEvents() {
                const adminGroup = document.getElementById('adminGroup');
                const adminDropdown = document.getElementById('adminDropdown');
                const logoutItem = document.getElementById('logoutItem');

                adminGroup.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDropdown();
                });

                logoutItem.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.handleLogout();
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.admin-group')) {
                        this.hideDropdown();
                    }
                });
            }

            toggleDropdown() {
                const dropdown = document.getElementById('adminDropdown');
                if (dropdown.classList.contains('show')) {
                    this.hideDropdown();
                } else {
                    this.showDropdown();
                }
            }

            showDropdown() {
                document.getElementById('adminDropdown').classList.add('show');
            }

            hideDropdown() {
                document.getElementById('adminDropdown').classList.remove('show');
            }

            handleLogout() {
                this.hideDropdown();
                // Use the custom confirmation modal
                showConfirmModal(
                    'Are you sure you want to logout?',
                    'You will be redirected to the login page.',
                    '{{ route("logout") }}',
                    'POST'
                );
            }
        }

        // Initialize components when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new AdminDropdown();
            initializeHeaderCalendar();
        });

        // Calendar functionality
        function initializeHeaderCalendar() {
            const calendarIcon = document.getElementById('calendarIcon');
            const calendarDropdown = document.getElementById('calendarDropdown');
            const closeCalendar = document.getElementById('closeCalendar');
            let calendarCurrentDate = new Date();
            let eventsData = [];

            // Fetch events for calendar (get all events)
            const currentYear = new Date().getFullYear();
            const startDate = `${currentYear}-01-01`;
            const endDate = `${currentYear + 1}-12-31`;
            
            fetch(`/api/events/calendar?start=${startDate}&end=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    // Handle calendar response
                    const events = Array.isArray(data) ? data : (data.data || []);
                    eventsData = events.map(event => ({
                        id: event.id,
                        title: event.title,
                        event_type: event.type || event.event_type,
                        event_date: event.start || event.date || event.event_date,
                        start_time: event.time || event.start_time
                    }));
                    renderCalendar(calendarCurrentDate);
                    // Check if there are events and add indicator
                    if (eventsData.length > 0) {
                        calendarIcon.classList.add('has-events');
                    }
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    renderCalendar(calendarCurrentDate);
                });

            // Toggle calendar dropdown
            calendarIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                if (calendarDropdown.classList.contains('show')) {
                    calendarDropdown.classList.remove('show');
                } else {
                    calendarDropdown.classList.add('show');
                    renderCalendar(calendarCurrentDate);
                }
            });

            // Close calendar
            closeCalendar.addEventListener('click', () => {
                calendarDropdown.classList.remove('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.calendar-icon-button') && !e.target.closest('.calendar-dropdown')) {
                    calendarDropdown.classList.remove('show');
                }
            });

            // Navigation buttons
            document.getElementById('prevMonth')?.addEventListener('click', () => {
                calendarCurrentDate.setMonth(calendarCurrentDate.getMonth() - 1);
                renderCalendar(calendarCurrentDate);
            });

            document.getElementById('nextMonth')?.addEventListener('click', () => {
                calendarCurrentDate.setMonth(calendarCurrentDate.getMonth() + 1);
                renderCalendar(calendarCurrentDate);
            });

            function renderCalendar(date) {
                const monthYear = document.getElementById('monthYear');
                const calendarDays = document.getElementById('calendarDays');
                
                if (!monthYear || !calendarDays) return;
                
                const year = date.getFullYear();
                const month = date.getMonth();
                
                // Set month/year header
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                                   'July', 'August', 'September', 'October', 'November', 'December'];
                monthYear.textContent = `${monthNames[month]} ${year}`;
                
                // Clear previous days
                calendarDays.innerHTML = '';
                
                // Get first day of month and number of days
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDayOfWeek = firstDay.getDay();
                
                // Add empty cells for days before month starts
                for (let i = 0; i < startingDayOfWeek; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'mini-calendar-day other-month';
                    const prevMonthDay = new Date(year, month, 0 - (startingDayOfWeek - 1 - i));
                    emptyDay.textContent = prevMonthDay.getDate();
                    calendarDays.appendChild(emptyDay);
                }
                
                // Add days of current month
                const today = new Date();
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'mini-calendar-day';
                    dayElement.textContent = day;
                    
                    // Check if it's today
                    if (year === today.getFullYear() && 
                        month === today.getMonth() && 
                        day === today.getDate()) {
                        dayElement.classList.add('today');
                    }
                    
                    // Check if there are events on this day
                    const dayDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const dayEvents = eventsData.filter(event => {
                        const eventDate = new Date(event.event_date).toISOString().split('T')[0];
                        return eventDate === dayDate;
                    });
                    
                    if (dayEvents.length > 0) {
                        dayElement.classList.add('has-event');
                        
                        // Get the primary event type for CSS class
                        const primaryEventType = dayEvents[0].event_type;
                        
                        // Add specific event type class for styling
                        switch(primaryEventType) {
                            case 'general':
                                dayElement.classList.add('event-general');
                                break;
                            case 'health':
                                dayElement.classList.add('event-health');
                                break;
                            case 'pension':
                                dayElement.classList.add('event-pension');
                                break;
                            case 'id_claiming':
                                dayElement.classList.add('event-id');
                                break;
                            default:
                                dayElement.classList.add('event-general');
                        }
                        
                        // Add title with event info
                        const eventTitles = dayEvents.map(e => e.title).join(', ');
                        dayElement.title = eventTitles;
                        
                        // Create small indicators for multiple event types
                        if (dayEvents.length > 1) {
                            const eventTypes = [...new Set(dayEvents.map(event => event.event_type))];
                            eventTypes.slice(1).forEach((eventType, index) => {
                                const indicator = document.createElement('div');
                                indicator.className = 'mini-calendar-event-indicator';
                                
                                // Set color based on event type
                                let color = '#4caf50';
                                switch(eventType) {
                                    case 'general':
                                        color = '#4caf50';
                                        break;
                                    case 'health':
                                        color = '#f44336';
                                        break;
                                    case 'pension':
                                        color = '#2196f3';
                                        break;
                                    case 'id_claiming':
                                        color = '#ffc107';
                                        break;
                                    default:
                                        color = '#4caf50';
                                }
                                
                                indicator.style.backgroundColor = color;
                                indicator.style.right = `${2 + (index * 8)}px`;
                                dayElement.appendChild(indicator);
                            });
                        }
                        
                        // Make day clickable to go to events page
                        dayElement.style.cursor = 'pointer';
                        dayElement.addEventListener('click', () => {
                            window.location.href = '/Events';
                        });
                    }
                    
                    calendarDays.appendChild(dayElement);
                }
                
                // Add remaining cells to complete the grid
                const totalCells = calendarDays.children.length;
                const remainingCells = 42 - totalCells;
                for (let i = 1; i <= remainingCells && i <= 14; i++) {
                    const nextMonthDay = document.createElement('div');
                    nextMonthDay.className = 'mini-calendar-day other-month';
                    nextMonthDay.textContent = i;
                    calendarDays.appendChild(nextMonthDay);
                }
            }
        }
    </script>
</body>
</html>