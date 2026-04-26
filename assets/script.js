// assets/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Theme Switcher Logic with Sun/Moon Icons
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme') || 'light';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (theme === 'dark') {
                icon.className = 'bi bi-sun-fill';
                themeToggle.title = "الوضع النهاري";
                themeToggle.classList.add('btn-warning');
                themeToggle.classList.remove('btn-primary');
            } else {
                icon.className = 'bi bi-moon-stars-fill';
                themeToggle.title = "الوضع الليلي";
                themeToggle.classList.add('btn-primary');
                themeToggle.classList.remove('btn-warning');
            }
        }
    }

    applyTheme(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(theme);
        });
    }

    // Sidebar Toggle Logic
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const overlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        if (sidebar) sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);

    // Table Search Functionality
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const targetId = this.getAttribute('data-target');
            const table = document.querySelector(targetId);
            if (!table) return;
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let found = false;
                const cells = row.getElementsByTagName('td');
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? "" : "none";
            });
        });
    });

    // Tooltips Initialization (Bootstrap)
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    } catch (e) { console.warn("Bootstrap Tooltip error:", e); }

    // Sidebar Navigation & Persistence
    const activeTab = localStorage.getItem('activeConstructionTab');
    if (activeTab) {
        const tabEl = document.querySelector(`button[data-bs-target="${activeTab}"]`);
        if (tabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            try {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            } catch (e) { console.warn("Bootstrap Tab error:", e); }
        }
    }

    const tabButtons = document.querySelectorAll('.sidebar .nav-link');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // If on mobile, close sidebar after click
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });

        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            btn.addEventListener('shown.bs.tab', function (event) {
                localStorage.setItem('activeConstructionTab', event.target.getAttribute('data-bs-target'));

                // Update active state in sidebar
                tabButtons.forEach(b => b.classList.remove('active'));
                event.target.classList.add('active');
            });
        }
    });
});
