// assets/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Table Search Functionality
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const targetId = this.getAttribute('data-target');
            const table = document.querySelector(targetId);
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? "" : "none";
            }
        });
    });

    // Tooltips Initialization (Bootstrap)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Active Tab Persistence
    const activeTab = localStorage.getItem('activeConstructionTab');
    if (activeTab) {
        const tabEl = document.querySelector(`button[data-bs-target="${activeTab}"]`);
        if (tabEl) {
            const tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
    }

    const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (event) {
            localStorage.setItem('activeConstructionTab', event.target.getAttribute('data-bs-target'));
        });
    });
});
