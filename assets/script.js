document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(t => {
        t.addEventListener('shown.bs.tab', e => {
            localStorage.setItem('activeTab', e.target.getAttribute('data-bs-target'));
        });
    });

    let at = localStorage.getItem('activeTab');
    if(at) {
        let el = document.querySelector('[data-bs-target="'+at+'"]');
        if(el) {
            new bootstrap.Tab(el).show();
        }
    }

    // Search functionality for tables
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const targetTableId = this.getAttribute('data-target');
            const rows = document.querySelectorAll(`${targetTableId} tbody tr`);

            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });
    });
});
