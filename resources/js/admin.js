import * as bootstrap from 'bootstrap';
import Sortable from 'sortablejs';
import 'trix';

// Exposed for page-local widgets that need to reach these from a plain
// inline <script> (e.g. the service form's tab-switching and reorderable
// checklist) without a server round-trip on every drag, unlike the generic
// [data-sortable] tables below.
window.bootstrap = bootstrap;
window.Sortable = Sortable;

// Mobile sidebar toggle.
document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.querySelector('.admin-sidebar')?.classList.toggle('is-open');
    });
});

// Drag-to-reorder tables: <tbody data-sortable data-sortable-url="...">
// rows must carry data-id="{id}"; posts {ids: [...]} in the new order.
document.querySelectorAll('[data-sortable]').forEach((tbody) => {
    const url = tbody.dataset.sortableUrl;
    if (!url) return;

    Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd() {
            const ids = Array.from(tbody.querySelectorAll('tr[data-id]')).map((tr) => tr.dataset.id);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ids }),
            });
        },
    });
});
