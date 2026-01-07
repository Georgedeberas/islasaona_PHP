/**
 * Admin Power Tools - Master Plan Phase 4
 * Handles Quick Edit, Drag & Drop, and UI enhancements.
 */

document.addEventListener('DOMContentLoaded', function () {
    initSortable();
    initQuickEdit();
});

// --- 1. Drag & Drop Reorder ---
function initSortable() {
    const el = document.getElementById('tourTableBody');
    if (!el) return;

    // Usamos SortableJS (cdn debe estar incluido)
    Sortable.create(el, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-light',
        onEnd: function (evt) {
            saveOrder();
        }
    });
}

function saveOrder() {
    const rows = document.querySelectorAll('#tourTableBody tr');
    let order = [];
    rows.forEach((row, index) => {
        const id = row.getAttribute('data-id');
        order.push(id); // El index en el array será la posición
    });

    fetch('/admin/tours/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order: order })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Orden actualizado'
                });
            }
        });
}

// --- 2. Quick Edit ---
let currentEditId = null;

function initQuickEdit() {
    // Listener delegado para botones de edición rápida
    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.btn-quick-edit')) {
            const btn = e.target.closest('.btn-quick-edit');
            const row = btn.closest('tr');
            openQuickEdit(row);
        }
    });
}

function openQuickEdit(row) {
    const id = row.getAttribute('data-id');
    const title = row.querySelector('.tour-title').innerText;
    const price = row.getAttribute('data-price');
    const active = row.getAttribute('data-active') == '1';

    // Rellenamos el modal (debe existir en el DOM)
    document.getElementById('qe_id').value = id;
    document.getElementById('qe_title').value = title;
    document.getElementById('qe_price').value = price;
    document.getElementById('qe_active').checked = active;

    const modal = new bootstrap.Modal(document.getElementById('quickEditModal'));
    modal.show();
}

function saveQuickEdit() {
    const id = document.getElementById('qe_id').value;
    const title = document.getElementById('qe_title').value;
    const price = document.getElementById('qe_price').value;
    const active = document.getElementById('qe_active').checked ? 1 : 0;

    // Enviamos updates individuales (o uno masivo si el controller lo soportara)
    // Por simplicidad, enviamos 3 peticiones o un endpoint mas inteligente
    // Vamos a usar el endpoint `quickUpdate` genérico 'field/value' 
    // pero idealmente deberiamos hacer un `update` parcial.
    // Hack: Llamar quickUpdate 3 veces en paralelo.

    const p1 = updateField(id, 'title', title);
    const p2 = updateField(id, 'price_adult', price);
    const p3 = updateField(id, 'is_active', active);

    Promise.all([p1, p2, p3]).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Guardado',
            timer: 1000,
            showConfirmButton: false
        }).then(() => {
            location.reload(); // Recargar para ver cambios
        });
    });
}

function updateField(id, field, value) {
    return fetch('/admin/tours/quick-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, field, value })
    });
}

// --- 3. Private Notes ---
// Se activará un pequeño popover o modal para notas rapidas
window.editNotes = function (id, currentNotes) {
    Swal.fire({
        title: '🔒 Notas Privadas',
        input: 'textarea',
        inputLabel: 'Solo visible para administradores',
        inputValue: currentNotes,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            updateField(id, 'private_notes', result.value).then(() => {
                Swal.fire('Guardado', '', 'success');
                // Actualizar icono visualmente si se pudiera, o reload
                setTimeout(() => location.reload(), 500);
            });
        }
    });
}
