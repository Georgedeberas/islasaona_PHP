/**
 * Admin Tour Wizard (2026)
 * - Smart Parser (Regex Auto-fill)
 * - AEO Traffic Light (Real-time SEO/AI validation)
 */

document.addEventListener('DOMContentLoaded', () => {
    initAEO();
    initParser();
});

/* =========================================
   1. SMART PARSER ENGINE 🪄
   ========================================= */
function initParser() {
    const parseBtn = document.getElementById('btnParseAction');
    if (!parseBtn) return;

    parseBtn.addEventListener('click', () => {
        const text = document.getElementById('parserTextarea').value;
        if (!text.trim()) return;

        // 1. Detect Title (First line typically)
        const lines = text.split('\n').map(l => l.trim()).filter(l => l);
        if (lines.length > 0 && !document.getElementById('title').value) {
            document.getElementById('title').value = lines[0].replace(/[*#]/g, '');
        }

        // 2. Helper Regex
        const patterns = {
            price: /(?:precio|costo|valor)\s*[:\.]?\s*\$?([\d,]+)/i, // Matches "Precio: 50" or "$50"
            duration: /(?:duraci[oó]n|tiempo)\s*[:\.]?\s*(.+)/i,
            includes: /(?:incluye|que\s*incluye)\s*[:\.]?/i,
            not_included: /(?:no\s*incluye|excluye)\s*[:\.]?/i,
            what_to_bring: /(?:que\s*llevar|llevar|recomendaciones)\s*[:\.]?/i
        };

        // 3. Simple block extraction
        // This is a naive implementation. A better one would find indices of headers and slice.

        // Find Prices
        const priceMatch = text.match(patterns.price);
        if (priceMatch) {
            document.getElementById('price_adult').value = priceMatch[1].replace(/\D/g, '');
            // Simple assumption: first number is adult price. 
        }

        // Find Duration
        const durationMatch = text.match(patterns.duration);
        if (durationMatch) {
            document.getElementById('duration').value = durationMatch[1];
        }

        // Extract Lists (Includes, Not Includes)
        // detailed parsing would require improved buffer logic

        fillFieldFromBlock(text, patterns.includes, patterns.not_included, 'info_includes');
        fillFieldFromBlock(text, patterns.not_included, patterns.what_to_bring, 'info_not_included');
        fillFieldFromBlock(text, patterns.what_to_bring, /zzzz/, 'info_what_to_bring'); // End of text

        // Close Modal
        const modalEl = document.getElementById('parserModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        // Trigger AEO check
        checkAEO();

        // Toast
        Swal.fire({
            icon: 'success',
            title: '¡Magia Realizada!',
            text: 'He rellenado los campos que pude detectar. Por favor revisa.',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

function fillFieldFromBlock(fullText, startRegex, endRegex, targetId) {
    const startMatch = fullText.match(startRegex);
    if (!startMatch) return;

    const startIndex = startMatch.index + startMatch[0].length;

    // Find nearest end token
    let endIndex = fullText.length;
    const endMatch = fullText.substr(startIndex).match(endRegex);

    if (endMatch) {
        endIndex = startIndex + endMatch.index;
    }

    const content = fullText.substring(startIndex, endIndex).trim();
    // Clean bullets
    const cleanContent = content.split('\n')
        .map(line => line.replace(/^[-*•]\s?/, '').trim())
        .filter(l => l)
        .join('\n');

    const el = document.getElementById(targetId);
    if (el) el.value = cleanContent;
}


/* =========================================
   2. AEO TRAFFIC LIGHT 🚦
   ========================================= */
function initAEO() {
    // fields to watch
    const inputs = ['title', 'description_short', 'seo_description'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', checkAEO);
    });

    checkAEO(); // Initial check
}

function checkAEO() {
    let score = 0;
    const maxScore = 3;

    // 1. Title Analysis
    const title = document.getElementById('title').value.trim();
    const titleStatus = document.getElementById('aeo-title-status');
    if (title.length > 10 && title.length < 70) {
        setStatus(titleStatus, 'good');
        score++;
    } else if (title.length > 0) {
        setStatus(titleStatus, 'warning');
    } else {
        setStatus(titleStatus, 'bad');
    }

    // 2. Description Analysis
    const desc = document.getElementById('description_short').value.trim();
    const descStatus = document.getElementById('aeo-desc-short-status');
    if (desc.length > 50 && desc.length < 160) {
        setStatus(descStatus, 'good');
        score++;
    } else if (desc.length > 0) {
        setStatus(descStatus, 'warning');
    } else {
        setStatus(descStatus, 'bad');
    }

    // 3. SEO Meta Analysis
    const seo = document.getElementById('seo_description').value.trim();
    const seoStatus = document.getElementById('aeo-seo-status');
    if (seo.length > 100) {
        setStatus(seoStatus, 'good');
        score++;
    } else {
        setStatus(seoStatus, 'warning'); // Optional but recommended
    }

    // Update Global Semáforo UI
    updateGlobalLight(score);
}

function setStatus(el, status) {
    if (!el) return;
    el.className = 'aeo-dot'; // reset
    el.classList.add('aeo-' + status);
}

function updateGlobalLight(score) {
    const light = document.getElementById('global-aeo-light');
    if (!light) return;

    light.className = 'aeo-traffic-light'; // reset

    if (score === 3) {
        light.classList.add('bg-success');
        light.innerHTML = '<i class="fas fa-check-circle"></i> AEO Optimizado';
    } else if (score >= 1) {
        light.classList.add('bg-warning');
        light.innerHTML = '<i class="fas fa-exclamation-circle"></i> Mejorable';
    } else {
        light.classList.add('bg-danger');
        light.innerHTML = '<i class="fas fa-times-circle"></i> Crítico';
    }
}
