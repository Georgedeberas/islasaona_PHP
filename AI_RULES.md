# MANIFIESTO MAESTRO: DIRECTIVAS DE SEGURIDAD, DESARROLLO Y ARQUITECTURA - MOCHILEROS RD

> [!IMPORTANT]
> **LECTURA OBLIGATORIA:** Tú, Agente AI, debes leer y asimilar estas reglas ANTES de procesar cualquier solicitud del usuario. Tu eficiencia y la estabilidad del proyecto dependen de esto.

## 0. 👮 PROTOCOLO DE SUPERVISIÓN

Existe un rol de **Supervisor Técnico** en este proyecto.
*   **Consulta Obligatoria:** Ante cambios críticos (Base de Datos, Arquitectura, Seguridad, Eliminación de Archivos), debes presentar tu plan y **ESPERAR APROBACIÓN** explícita.
*   **Actualización Constante:** Antes de proponer CUALQUIER plan, verifica si este archivo (`AI_RULES.md`) ha cambiado. Tu propuesta debe comenzar confirmando: *"He leído las reglas actualizadas y mi plan cumple con ellas."*
*   **Si tienes dudas:** No adivines. Pregunta al Supervisor (vía chat con el usuario) antes de escribir código riesgoso.

---

### 🔥 HERRAMIENTA DE CONTROL REMOTO (MCP BRIDGE)
Tienes acceso a un "Puente MCP" para gestionar el servidor de producción directamente.

**Endpoint:** `https://islasaona.mochilerosrd.com/mcp_bridge.php`
**Auth Header:** `Authorization: Bearer Mochileros_Secret_Key_998877`

**Capacidades:**
1.  **Listar:** `{ "action": "listar", "file": "src" }`
2.  **Leer:** `{ "action": "leer", "file": "public/index.php" }`
3.  **Escribir:** `{ "action": "escribir", "file": "ruta", "content": "..." }`
4.  **SQL Directo:** `{ "action": "sql_query", "query": "SELECT * FROM users" }`

**Uso Recomendado:** Usa `curl` o scripts PHP locales (evita `Invoke-WebRequest` de PowerShell si falla) para contactar con este puente y realizar tareas de mantenimiento o debug en vivo.


---

**PROYECTO:** MOCHILEROS RD (CMS Turismo)
**ENTORNO:** Antigravity + GitHub + iPage (Shared Hosting)
**FILOSOFÍA:** Autonomía, Seguridad Modular y "AEO First".

### 1. PRINCIPIOS OPERATIVOS

#### 1.1. Idioma y Comunicación
*   **ESTRICTO:** Toda comunicación, planes, comentarios en el código, documentación y mensajes de commit deben ser en **ESPAÑOL**.
*   *Excepción:* Sintaxis del código (nombres de funciones nativas, palabras reservadas de PHP/JS) se mantienen en inglés.

#### 1.2. Autonomía y Ejecución en Lote (Batching)
*   **Minimizar Preguntas:** No pidas permiso por cada archivo individual. Asume la autoridad para crear la estructura necesaria basada en el objetivo macro.
*   **Planificación Obligatoria:** Antes de escribir una sola línea de código, presenta un **PLAN DE EJECUCIÓN**.
    *   Si el usuario modifica el requerimiento, **re-genera el plan completo** hasta obtener aprobación.
*   **Ejecución Masiva:** Una vez aprobado el plan, agrupa todas las creaciones y ediciones de archivos en **un solo bloque de ejecución**. El objetivo es que el usuario apruebe todo con una sola confirmación.

#### 1.3. Política de "Cabos Sueltos" (No Loose Ends)
*   **Integridad:** Prohibido crear una "Página Huérfana". Si creas una vista nueva, debes obligatoriamente:
    1.  Crear su Controlador en el BACKEND.
    2.  Crear su Ruta.
    3.  Agregarla al Menú de Navegación o vincularla desde una página padre.
*   **UI/UX:** Verificar que no existan botones sin acción o enlaces rotos antes de finalizar la tarea.

## 2. STACK TECNOLÓGICO (INAMOVIBLE)

Debido a las restricciones de **iPage (Shared Hosting)**, nos adherimos estrictamente a:
*   **Backend:** PHP 7.4 / 8.0 (Vanilla/Nativo).
    *   🚫 **PROHIBIDO:** React, Vue, jQuery, Node.js, Build tools (Webpack/Vite), Laravel, Symfony, Composer (salvo estricta necesidad y con vendor local), en servidor.
*   **Base de Datos:** MySQL / MariaDB.
    *   **Motor:** InnoDB.
    *   **Conexión:** PDO con patrón Singleton.
    *   **Seguridad:** Sentencias Preparadas (Prepared Statements) obligatorias.
*   **Frontend:** HTML5 + TailwindCSS (vía CDN para desarrollo ágil).
    *   **Scripting:** JavaScript Vanilla (ES6+). Nada de React/Vue/Angular.
*   **Servidor Web:** Apache (Manejo vía `.htaccess`).

## 3. ESTRUCTURA DE DIRECTORIOS

El sistema debe respetar estrictamente esta organización:

```text
/
├── public/                 # Document Root (Único acceso web)
│   ├── index.php           # Router principal
│   ├── assets/
│   │   ├── css/
│   │   └── uploads/        # ALMACÉN DE IMÁGENES (Ruta física de escritura)
│   └── .htaccess           # Reglas de reescritura
├── src/                    # Lógica de Negocio (Protegido)
│   ├── Config/Database.php # Conexión PDO
│   ├── Controllers/        # Controladores (ej: TourController.php)
│   ├── Models/             # Modelos de Datos (ej: Tour.php)
│   └── Views/              # Plantillas HTML
│       ├── admin/          # Interfaz de Administración
│       └── front/          # Web Pública
├── database/
│   ├── schema.sql          # Estructura completa BD
│   └── update.sql          # Actualizaciones pendientes
└── AI_RULES.md             # Este archivo
```

## 4. PROTOCOLOS DE SEGURIDAD (ALTA PRIORIDAD)

#### 4.1. Gestión de Secretos (Anti-Hardcoding)
*   🚫 **PROHIBIDO:** Dejar credenciales (DB User, Pass, API Keys) visibles en el código fuente.
*   **SOLUCIÓN:** Usar `src/Config/config.php` (o varenvs simuladas) excluido del repositorio.
*   **Agente:** Genera código asumiendo que las credenciales vienen de `src/Config/Database.php` y provee una plantilla `config.sample.php` si es necesario.

#### 4.2. Inyección SQL y XSS
*   **Base de Datos:** Sentencias Preparadas (PDO) **OBLIGATORIAS**.
*   **Frontend:** Escapar salida (`htmlspecialchars`) para evitar XSS.

#### 4.3. Seguridad Modular (IA Coding Trends)
*   **Validación:** "Nunca confíes en el usuario". Valida `$_POST`, `$_GET`, `$_FILES` en el servidor.
*   **Sanitización:** Limpia nombres de archivos subidos antes de guardar.

## 5. FLUJO DE DESARROLLO Y DESPLIEGUE

### FASE 1: Planificación y Análisis
1.  Recibir instrucción.
2.  Analizar dependencias (`list_dir`, `grep_search`).
3.  Generar **Plan en Español**: "Voy a modificar X, crear tabla Y, conectar menú Z".

### FASE 2: Desarrollo y GitHub
1.  Generar código siguiendo la estructura.
2.  **Commit/Push:** Subir cambios a GitHub. (iPage sincroniza automáticamente).
3.  **Confirmar:** Avisar al usuario "Cambios subidos".

### FASE 3: Gestión de Base de Datos (Browser-Based & Archivo Único)
Como no hay SSH confiable:
1.  **NO** crees archivos de migración dispersos.
2.  Escribe tus cambios SQL en `database/update.sql`.
3.  Si es necesario, instruye al usuario para ejecutar un script PHP que procese este archivo (ej: visitar `tudominio.com/update_db.php`).
4.  Una vez aplicados, consolida en `database/schema.sql` y limpia `update.sql`.

## 6. PROTOCOLO DE ERRORES Y DEBUGGING

Si ocurre un error, **NO ADIVINES**.
1.  **Análisis:** Lee el error y el contexto.
2.  **Causa Raíz:** Explica en español técnico el porqué.
3.  **Revisión Histórica:** ¿Qué cambio reciente rompió el código?
4.  **Solución:** Propone el código corregido.

## 7. OBJETIVOS FUNCIONALES ACTUALES (ROADMAP)

Priorizar conversión a **CMS Total**:
*   📊 **Dashboard Visual:** Estadísticas en inicio admin.
*   📝 **Gestor de Contenido:** Todo editable desde Admin (Frontend texts/images).
*   📰 **Blog/Noticias:** Módulo SEO completo.
*   👥 **Roles:** Admin vs Editor.
*   🤖 **Smart Parser:** Auto-rellenado de tours.
