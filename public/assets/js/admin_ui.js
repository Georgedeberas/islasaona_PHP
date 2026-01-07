/**
 * Admin UI Enhancements
 * Handles Template Switching, Previews, Sample Data, and Alerts.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Unsaved Changes Warning
    let isDirty = false;
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('change', () => isDirty = true);
        form.addEventListener('input', () => isDirty = true);
        form.addEventListener('submit', () => isDirty = false);
    }

    window.addEventListener('beforeunload', function (e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Initialize Tooltips if Bootstrap is present
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

/**
 * Select Template Logic
 */
function selectTemplate(templateName, element) {
    // Update hidden input
    const input = document.getElementById('selectedTemplate');
    if (input) input.value = templateName;

    // Update visual state
    document.querySelectorAll('.template-option').forEach(el => {
        el.classList.remove('active-template', 'border-primary', 'bg-light');
        el.classList.add('border-light');
    });

    if (element) {
        element.classList.add('active-template', 'border-primary', 'bg-light');
        element.classList.remove('border-light');
    }

    // Show/Hide Sections
    document.querySelectorAll('.template-section').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById('section-' + templateName);
    if (target) {
        target.classList.remove('d-none');
        // Toast Notification
        showToast(`Plantilla "${templateName}" seleccionada`);
    }

    // Update Content Title
    const titleEl = document.getElementById('contentTitle');
    if (titleEl) {
        const labels = { 'classic': 'Clásica', 'landing': 'Landing Page', 'gallery': 'Galería Visual' };
        titleEl.innerHTML = `Contenido: <span class="fw-bold text-dark">${labels[templateName] || templateName}</span>`;
    }
}

/**
 * Show Preview Modal
 */
function showPreview(templateName) {
    const modalHtml = `
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content overflow-hidden">
                    <div class="modal-header py-2 bg-light">
                        <h6 class="modal-title m-0">👁️ Vista Previa: ${templateName.toUpperCase()}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 bg-dark text-center">
                        <img src="/assets/img/previews/preview_${templateName}.png" class="img-fluid" style="max-height: 80vh; opacity: 0.9;">
                        <!-- Fallback text if image missing -->
                        <div class="text-white py-5 d-none">
                            <h3 class="text-white">Wireframe no disponible</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    // Remove old modal
    const old = document.getElementById('previewModal');
    if (old) old.remove();

    // Append new
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

/**
 * Load Sample Data
 */
function loadSampleData() {
    const tpl = document.getElementById('selectedTemplate').value;

    Swal.fire({
        title: '¿Cargar Datos de Ejemplo?',
        text: "Esto reemplazará el contenido actual de los campos vacíos.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, rellenar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fillData(tpl);
            showToast('Datos cargados con éxito 🪄', 'success');
        }
    });
}

function fillData(template) {
    if (template === 'landing') {
        setVal('hero_title', 'Bienvenido al Paraíso: Isla Saona');
        setVal('hero_subtitle', 'La experiencia más exclusiva del Caribe te espera.');
        setVal('cta_text', 'Reservar Aventura');
        setVal('cta_link', '/tours/saona-vip');

        // Features
        setVal('feature_title_0', 'Playas Vírgenes');
        setVal('feature_desc_0', 'Disfruta de kilómetros de arena blanca sin multitudes.');
        setVal('feature_title_1', 'Gastronomía Local');
        setVal('feature_desc_1', 'Almuerzo buffet con langosta fresca incluida.');
        setVal('feature_title_2', 'Transporte VIP');
        setVal('feature_desc_2', 'Catamarán de lujo con barra libre y música.');
    }
    else if (template === 'gallery') {
        setVal('gallery_description', 'Una colección de nuestros mejores momentos en la Isla. Desde el amanecer hasta las fiestas en el catamarán.');
    }
    else {
        // Classic
        const tinymceData = `<h2>Nuestra Misión</h2><p>Ofrecer las mejores excursiones de República Dominicana...</p>`;
        if (tinymce && tinymce.get('editorContent')) {
            tinymce.get('editorContent').setContent(tinymceData);
        } else {
            setVal('editorContent', tinymceData);
        }
    }
}

function setVal(id, val) {
    const el = document.getElementById(id);
    if (el && !el.value) el.value = val; // Only fill if empty
}

/**
 * Toast Helper (Requires SweetAlert2)
 */
function showToast(title, icon = 'info') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    Toast.fire({
        icon: icon,
        title: title
    });
}
