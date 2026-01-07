<?php
// src/Views/admin/tours/edit.php
// Nuevo Formulario Avanzado para Tours (V2 - 2026)
// Stack: Bootstrap 5 + Tailwind Utility Classes (Custom CSS) + Vanilla JS
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tour [Avanzado] - Mochileros RD</title>
    <!-- Bootstrap 5 for Layout & Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-orange: #ff6b00;
            --secondary-blue: #0f172a;
        }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .nav-tabs .nav-link.active {
            border-top: 3px solid var(--primary-orange);
            font-weight: 600;
            color: var(--secondary-blue);
        }
        .form-label-sm { font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .img-grid-item { position: relative; border-radius: 8px; overflow: hidden; border: 2px solid #e2e8f0; transition: all 0.2s; }
        .img-grid-item:hover { border-color: var(--primary-orange); }
        .img-cover-badge { position: absolute; top: 5px; right: 5px; background: var(--primary-orange); color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold; }
        
        /* Floating Action Bar */
        .fab-bar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: white; border-top: 1px solid #dee2e6;
            padding: 1rem; z-index: 1050;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
        }
        .main-content { padding-bottom: 80px; } /* Space for FAB */
    </style>
</head>
<body>

<?php
// Helper para fechas específicas
$specificDates = isset($tour['specific_dates']) ? json_decode($tour['specific_dates'], true) : [];
$specificDatesStr = is_array($specificDates) ? implode(',', $specificDates) : '';

// Helper de imágenes
$tourImages = isset($tour['id']) ? (new \App\Models\Tour())->getImages($tour['id']) : [];

// Notificación Success
if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show m-3 position-fixed top-0 end-0 shadow" role="alert" style="z-index: 2000;">
        <strong>¡Guardado!</strong> Los cambios se han aplicado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container-fluid main-content py-4">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h4 class="mb-0 text-secondary fw-bold">
                <?= isset($tour) ? '✏️ Editando: ' . htmlspecialchars($tour['title']) : '✨ Crear Nuevo Tour' ?>
            </h4>
            <small class="text-muted">Modo Avanzado V2.0</small>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#parserModal">
                ⚡ Importar Texto Mágico
            </button>
            <a href="/admin/tours" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" id="tourForm">
        
        <!-- Tabs de Navegación -->
        <ul class="nav nav-tabs px-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-content" type="button">
                    📝 Contenido & Info
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-media" type="button">
                    📸 Galería Media
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">
                    🤖 SEO & IA
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-settings" type="button">
                    ⚙️ Configuración
                </button>
            </li>
        </ul>

        <div class="tab-content p-4 bg-white border border-top-0 shadow-sm rounded-bottom">
            
            <!-- TAB 1: CONTENIDO -->
            <div class="tab-pane fade show active" id="tab-content">
                <div class="row g-4">
                    <!-- Columna Izquierda: Datos Principales -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label-sm">Título Principal</label>
                            <input type="text" name="title" id="input_title" class="form-control form-control-lg fw-bold" 
                                   value="<?= htmlspecialchars($tour['title'] ?? '') ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label-sm">Costo Adulto (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="price_adult" class="form-control" value="<?= $tour['price_adult'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label-sm">Costo Niño (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="price_child" class="form-control" value="<?= $tour['price_child'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label-sm">Duración</label>
                                <input type="text" name="duration" id="input_duration" class="form-control" value="<?= htmlspecialchars($tour['duration'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Campos de Texto Extendido V2 -->
                         <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="text-uppercase fw-bold text-muted mb-3"><i class="fas fa-align-left me-2"></i>Información Detallada</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">💰 Información de Costos</label>
                                    <textarea name="info_cost" id="input_cost" class="form-control" rows="2"><?= htmlspecialchars($tour['info_cost'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">📅 Fechas Disponibles (Texto)</label>
                                    <textarea name="info_dates_text" id="input_dates" class="form-control" rows="2"><?= htmlspecialchars($tour['info_dates_text'] ?? '') ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-dark">🚐 Puntos de Salida</label>
                                        <textarea name="info_departure" id="input_departure" class="form-control" rows="3"><?= htmlspecialchars($tour['info_departure'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-dark">🅿️ Parqueo</label>
                                        <textarea name="info_parking" id="input_parking" class="form-control" rows="3"><?= htmlspecialchars($tour['info_parking'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                         </div>

                         <div class="mb-3">
                            <label class="form-label-sm">Descripción Larga (HTML)</label>
                            <textarea name="description_long" class="form-control" rows="10"><?= htmlspecialchars($tour['description_long'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Columna Derecha: Listas -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label-sm text-success">✅ Incluye</label>
                            <textarea name="info_includes" id="input_includes" class="form-control bg-success-subtle" rows="6" placeholder="Texto libre..."><?= htmlspecialchars($tour['info_includes'] ?? '') ?></textarea>
                            <small class="text-muted">Se mostrará tal cual en la web.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-sm text-primary">📍 Visitaremos</label>
                             <textarea name="info_visiting" id="input_visiting" class="form-control bg-primary-subtle" rows="5"><?= htmlspecialchars($tour['info_visiting'] ?? '') ?></textarea>
                        </div>
                         <div class="mb-3">
                            <label class="form-label-sm text-danger">❌ No Incluye</label>
                            <textarea name="info_not_included" id="input_not_included" class="form-control bg-danger-subtle" rows="4"><?= htmlspecialchars($tour['info_not_included'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-sm text-warning">🎒 Qué Llevar</label>
                            <textarea name="info_what_to_bring" id="input_what_to_bring" class="form-control bg-warning-subtle" rows="4"><?= htmlspecialchars($tour['info_what_to_bring'] ?? '') ?></textarea>
                        </div>
                         <div class="mb-3">
                            <label class="form-label-sm text-dark">⚠️ Importante</label>
                            <textarea name="info_important" id="input_important" class="form-control bg-light" rows="4"><?= htmlspecialchars($tour['info_important'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: GALERÍA -->
            <div class="tab-pane fade" id="tab-media">
                <div class="mb-4">
                    <label class="form-label fw-bold">Subir Nuevas Imágenes</label>
                    <input type="file" name="images[]" multiple class="form-control form-control-lg" accept="image/*">
                </div>
                
                <hr>
                <h6 class="text-uppercase text-muted mb-3">Imágenes Actuales</h6>
                
                <?php if (empty($tourImages)): ?>
                    <p class="text-center text-muted py-5">No hay imágenes cargadas aún.</p>
                <?php else: ?>
                    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
                        <?php foreach($tourImages as $img): ?>
                        <div class="col">
                            <div class="img-grid-item p-2 h-100 bg-light">
                                <div class="ratio ratio-4x3 mb-2">
                                    <img src="/<?= ltrim($img['image_path'], '/') ?>" class="object-fit-cover rounded" onerror="this.src='https://placehold.co/400?text=Error'">
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cover_image" value="<?= $img['id'] ?>" 
                                           id="cover_<?= $img['id'] ?>" <?= $img['is_cover'] ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-bold" for="cover_<?= $img['id'] ?>">Portada</label>
                                </div>
                                <div class="form-check mt-1">
                                    <input class="form-check-input bg-danger border-danger" type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" id="del_<?= $img['id'] ?>">
                                    <label class="form-check-label small text-danger" for="del_<?= $img['id'] ?>">Eliminar</label>
                                </div>

                                <?php if($img['is_cover']): ?>
                                    <span class="img-cover-badge">PORTADA</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 3: SEO -->
            <div class="tab-pane fade" id="tab-seo">
                 <div class="alert alert-info">
                    <i class="fas fa-robot me-2"></i>
                    <strong>Zona AEO:</strong> Optimiza para que la Inteligencia Artificial entienda tu tour.
                 </div>
                 <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">SEO Title</label>
                            <input type="text" name="seo_title" class="form-control" value="<?= htmlspecialchars($tour['seo_title'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="seo_description" class="form-control" rows="3"><?= htmlspecialchars($tour['seo_description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keywords (Etiquetas)</label>
                             <textarea name="keywords" class="form-control" rows="2"><?= htmlspecialchars($tour['keywords'] ?? '') ?></textarea>
                        </div>
                     </div>
                     <div class="col-md-6">
                         <div class="mb-3">
                             <label class="fw-bold">Highlights (Resumido para IA)</label>
                             <textarea name="tour_highlights" class="form-control" rows="6"><?= isset($tour['tour_highlights']) ? implode("\n", json_decode($tour['tour_highlights'], true) ?? []) : '' ?></textarea>
                             <small>Una frase por línea.</small>
                         </div>
                     </div>
                 </div>
            </div>

            <!-- TAB 4: SETTINGS -->
            <div class="tab-pane fade" id="tab-settings">
                 <div class="row">
                     <div class="col-md-4">
                         <div class="card p-3">
                             <label class="form-label fw-bold mb-3">Frecuencia del Tour</label>
                             <select name="frequency_type" id="frequency_select" class="form-select mb-3">
                                 <option value="daily" <?= ($tour['frequency_type'] ?? '') == 'daily' ? 'selected' : '' ?>>Todos los días</option>
                                 <option value="weekends" <?= ($tour['frequency_type'] ?? '') == 'weekends' ? 'selected' : '' ?>>Fines de Semana</option>
                                 <option value="specific" <?= ($tour['frequency_type'] ?? '') == 'specific' ? 'selected' : '' ?>>Fechas Específicas</option>
                             </select>
                             
                             <div id="specific_dates_container" class="d-none">
                                 <label class="small text-muted">Fechas (Separadas por coma YYYY-MM-DD)</label>
                                 <textarea name="specific_dates" class="form-control" rows="3"><?= $specificDatesStr ?></textarea>
                                 <!-- TODO: Implementar Datepicker JS en V1.6 -->
                             </div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="form-check form-switch mt-4 p-3 border rounded">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                <?= (!isset($tour) || $tour['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="isActive">Tour Público / Activo</label>
                        </div>
                     </div>
                 </div>
            </div>
        </div>

        <!-- Floating Saves -->
        <div class="fab-bar">
            <div class="text-muted small">
                Estado: <?= isset($tour) ? '<span class="text-success">● Guardado</span>' : '● Borrador' ?>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-5">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Smart Parser -->
<div class="modal fade" id="parserModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">⚡ Importar desde WhatsApp/Texto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted">Pega aquí el texto completo del tour. La IA detectará campos como COSTO, INCLUYE, FECHAS, etc.</p>
        <textarea id="pasteArea" class="form-control font-monospace" rows="15" placeholder="Pega el texto aquí..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="runSmartParser()">🔮 Procesar y Llenar</button>
      </div>
    </div>
  </div>
</div>

<!-- JS Logic -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Specific Dates
    const freqSelect = document.getElementById('frequency_select');
    const dateContainer = document.getElementById('specific_dates_container');
    
    function toggleDates() {
        if(freqSelect.value === 'specific') {
            dateContainer.classList.remove('d-none');
        } else {
            dateContainer.classList.add('d-none');
        }
    }
    freqSelect.addEventListener('change', toggleDates);
    toggleDates(); // Init

    // SMART PARSER
    function runSmartParser() {
        const text = document.getElementById('pasteArea').value;
        if(!text) return;

        // Mapper de Keywords a IDs de Inputs
        const map = {
            'COSTO': 'input_cost',
            'PRECIO': 'input_cost',
            '𝐃𝐔𝐑𝐀𝐂𝐈𝐎𝐍': 'input_duration', 'DURACION': 'input_duration',
            'FECHAS DISPONIBLES': 'input_dates',
            'INCLUYE': 'input_includes', '✅ INCLUYE': 'input_includes',
            'NO INCLUYE': 'input_not_included', '❌ NO INCLUYE': 'input_not_included',
            'VISITAREMOS': 'input_visiting', 'LUGARES A VISITAR': 'input_visiting',
            'PUNTOS DE SALIDA': 'input_departure', 'SALIDA': 'input_departure',
            'PARQUEOS': 'input_parking',
            'IMPORTANTE': 'input_important',
            'QUE LLEVAR': 'input_what_to_bring'
        };

        // Lógica simple de split por líneas y detección de cabeceras
        const lines = text.split('\n');
        let currentField = null;
        let buffer = '';

        lines.forEach(line => {
            const trimmed = line.trim().toUpperCase();
            // Detectar si la línea es un header conocido
            let matched = false;
            for (const [key, targetId] of Object.entries(map)) {
                if (trimmed.includes(key) && trimmed.length < 50) { // Headers suelen ser cortos
                    // Si ya teníamos algo en buffer, guardarlo en el campo anterior
                    if (currentField && map[currentField]) {
                        appendToInput(map[currentField], buffer);
                    }
                    // Nuevo bloque
                    currentField = key;
                    buffer = ''; // Reiniciar buffer
                    matched = true;
                    // Cheat: Si la linea tiene el valor ahí mismo (Ej: COSTO: $500), guardarlo
                    const splitVal = line.split(/[:|-]/);
                    if(splitVal.length > 1) {
                         buffer += splitVal.slice(1).join(':').trim() + '\n';
                    }
                    break;
                }
            }
            
            if (!matched) {
                if (currentField) {
                    buffer += line + '\n';
                }
            }
        });

        // Flush last buffer
        if (currentField && map[currentField]) {
            appendToInput(map[currentField], buffer);
        }

        // Cerrar modal
        const modalEl = document.getElementById('parserModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
        
        alert('✨ Datos importados mágicamente. Revisa los campos.');
    }

    function appendToInput(id, content) {
        const el = document.getElementById(id);
        if(el) {
            // Limpiar contenido previo si es duplicado o append? Mejor reemplazar clean
            const cleanContent = content.trim();
            if(cleanContent) el.value = cleanContent;
        }
    }
</script>

</body>
</html>
