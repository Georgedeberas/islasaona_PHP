# 🛑 PROTOCOLO MAESTRO DE AGENTES (AI_RULES.md)

> [!IMPORTANT]
> **LECTURA OBLIGATORIA:** Tú, Agente AI, debes leer y asimilar estas reglas ANTES de procesar cualquier solicitud del usuario. Tu eficiencia y la estabilidad del proyecto dependen de esto.

## 0. 👮 PROTOCOLO DE SUPERVISIÓN

Existe un rol de **Supervisor Técnico** en este proyecto.
*   **Consulta Obligatoria:** Ante cambios críticos (Base de Datos, Arquitectura, Seguridad, Eliminación de Archivos), debes presentar tu plan y **ESPERAR APROBACIÓN** explícita.
*   **Si tienes dudas:** No adivines. Pregunta al Supervisor (vía chat con el usuario) antes de escribir código riesgoso.

## 1. 🧠 AUTONOMÍA Y EFICIENCIA (Pensar antes de Preguntar)

*   **Prohibido Preguntar lo Obvio:** Antes de pedir contexto al usuario, DEBES agotar tus herramientas (`list_dir`, `grep_search`, `read_file`, `view_file_outline`) para entender el estado actual.
*   **Cero Suposiciones Técnicas:** Si el código no te da la respuesta, entonces y SOLO entonces, pregunta.
*   **Idioma:** Toda comunicación, código (comentarios) y documentación debe ser en **ESPAÑOL**.

## 2. 📦 FLUJO DE APROBACIÓN POR LOTES (Batch Approval)

Para minimizar el ruido y las interrupciones, sigue estrictamente este ciclo:

1.  **🔍 FASE DE RECOPILACIÓN:** Analiza todos los archivos necesarios. Entiende el problema completo.
2.  **📝 FASE DE PLANIFICACIÓN:** Diseña la solución ENTERA.
    *   ¿Qué archivos se crean?
    *   ¿Qué archivos se modifican?
    *   ¿Qué cambios de BD se requieren?
3.  **✅ SOLICITUD DE APROBACIÓN ÚNICA:** Presenta el plan completo al usuario.
    *   *"He analizado X, haré Y y Z. ¿Procedo?"*
    *   **NO** pidas aprobación paso a paso. Pídela para el bloque de trabajo completo.
4.  **🚀 EJECUCIÓN MASIVA:** Una vez aprobado:
    *   Edita/Crea todos los archivos.
    *   Ejecuta los comandos necesarios.
    *   Realiza el commit/push.
    *   Solo detente si encuentras un error crítico bloqueante.

## 3. 💾 GESTIÓN DE BASE DE DATOS (Archivo Único)

Mantén la base de datos limpia y ordenada. Evita la dispersión de archivos.

*   **Fuente de Verdad:** `database/schema.sql` (Debe reflejar la estructura completa y actual).
*   **Actualizaciones Pendientes:**
    *   Usa **SIEMPRE** el archivo: `database/update.sql`.
    *   **NO CRES** archivos dispersos (nada de `migration_2024.sql`, `fix_table.sql`).
    *   **Flujo:**
        1.  Escribe las sentencias `ALTER`, `CREATE`, `INSERT` necesarias en `database/update.sql`.
        2.  Sube el archivo a GitHub.
        3.  **Solicita al Usuario:** *"Por favor, accede al enlace de actualización [LINK] para aplicar los cambios en `update.sql`."*
    *   **Limpieza:** Una vez confirmada la actualización en producción, consolida los cambios en `schema.sql` y vacía `update.sql`.

## 4. ☁️ CONTROL DE VERSIONES (Git/GitHub)

Al finalizar **CUALQUIER** bloque de trabajo (o sub-tarea significativa):

1.  **Verificar:** `git status`
2.  **Preparar:** `git add .`
3.  **Guardar:** `git commit -m "Descripción clara del cambio en español"`
4.  **Subir:** `git push`
5.  **Confirmar:** Avisa al usuario: *"Cambios subidos al repositorio correctamente."*

## 5. 🛡️ SEGURIDAD Y ARQUITECTURA (Recordatorio Técnico)

*   **Zero Trust:** Valida todo input.
*   **Secretos:** NUNCA hardcodees credenciales. Usa `.env`.
*   **Móvil:** App -> API -> DB (Nunca conexión directa).
