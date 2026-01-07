/**
 * Live Search Engine
 * Native JS for instant search results
 */

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch();
});

function initLiveSearch() {
    const searchInput = document.querySelector('input[name="q"]'); // Asumiremos input name='q'
    if (!searchInput) return;

    // Crear contenedor de resultados si no existe
    let resultsContainer = document.getElementById('live-search-results');
    if (!resultsContainer) {
        resultsContainer = document.createElement('div');
        resultsContainer.id = 'live-search-results';
        resultsContainer.className = 'absolute top-full left-0 w-full bg-white shadow-xl rounded-b-xl border border-gray-100 hidden z-50 overflow-hidden';
        searchInput.parentNode.style.position = 'relative'; // Asegurar pariente relativo
        searchInput.parentNode.appendChild(resultsContainer);
    }

    let debounceTimer;

    searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSearch(query, resultsContainer);
        }, 300);
    });

    // Cerrar al click fuera
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('hidden');
        }
    });
}

function fetchSearch(query, container) {
    // Simulamos búsqueda o conectamos a endpoint real
    // Como no tenemos endpoint JSON de search, usaremos el endpoint de tours y filtraremos en cliente (o crearemos endpoint)
    // Opción PRO: Crear endpoint dedicado /api/search?q=...
    // Opción Rápida: Usar endpoint existente y filtrar (no ideal para large sets)

    // Vamos a asumir que crearemos un endpoint ligero de búsqueda en Phase 6 Backend parts.
    fetch(`/api/search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            renderResults(data, container);
        })
        .catch(err => console.error('Search error', err));
}

function renderResults(data, container) {
    if (!data || data.length === 0) {
        container.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">No se encontraron resultados</div>';
    } else {
        const html = data.map(item => `
            <a href="/tour/${item.slug}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 text-left group">
                <img src="/${item.thumb}" class="w-10 h-10 rounded object-cover shadow-sm group-hover:scale-105 transition">
                <div>
                    <div class="font-bold text-gray-800 text-sm group-hover:text-primary leading-tight">${item.title}</div>
                    <div class="text-xs text-green-600 font-bold">$${item.price}</div>
                </div>
            </a>
        `).join('');
        container.innerHTML = html;
    }
    container.classList.remove('hidden');
}
