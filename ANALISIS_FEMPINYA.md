# Informe de Análisis del Código de FemPinya (Rama Dev)

## 1. Bugs, Errores y Riesgos de Seguridad

### A. Riesgos de Seguridad e Inyecciones SQL
- **Uso de `DB::raw()` sin sanitizar:** Existen múltiples consultas en el sistema usando `DB::raw` que concatenan variables directamente en lugar de usar "bindings" de parámetros. Ejemplos de archivos afectados:
  - `app/Services/Filters/CastellersFilter.php`
  - `app/Services/Filters/EventsFilter.php`
  - `app/Services/Filters/MultieventsFilter.php`
  *Riesgo:* Esto abre la puerta a inyecciones SQL si los inputs de usuario (como el de las búsquedas en las datatables) se exponen directamente a `DB::raw()`.
- **Falta de Validación Robusta (FormRequests):** Gran parte de las validaciones de input se hacen en línea dentro del controlador llamando a `$request->validate([...])`. Esto no solo va en contra de la separación de responsabilidades, sino que a veces es frágil si no está el `$guarded` o `$fillable` bien definido en los Modelos.
- **Vulnerabilidad de Asignación Masiva:** En los Modelos, algunos tienen `protected $guarded = [];` (como `app/Ronda.php` o `app/Casteller.php` que protegen solo IDs y timestamps). Esto puede ser arriesgado si se usa algo como `$model->update($request->all())` en el futuro.

### B. Bugs Encontrados (Vía PHPStan / Análisis Estático)
- **Reporte de PHPStan:** El análisis con PHPStan nivel 5 ha revelado 297 errores (como se ve en `phpstan-report.txt`). La mayoría son de tipado o llamadas a propiedades indefinidas.
- **Llamadas a métodos y propiedades inexistentes:**
  - En `User.php` (línea 71): Se intenta acceder a la propiedad indefinida `name` de `Spatie\Permission\Models\Permission`.
  - En `Casteller.php` (líneas 295 y 300): Acceso a `$casteller->tags` que no está documentado o no existe como relación correctamente declarada.
  - Llamadas a métodos mágicos estáticos de Builder que fallan al tipado.
  - Propiedades dinámicas en Eventos y Asistencias (`Event::$attendance`).
- **Uso conflictivo del Objeto Auth:** En algunas partes (como la declaración estática de `$colla` en el modelo `Colla.php`), `Auth::user()` es invocado para establecer contexto. Esto rompe la filosofía stateless y causa problemas si el modelo se hidrata fuera de contexto HTTP (como en cronjobs/comandos CLI).

---

## 2. Optimización de Queries y Tiempos de Respuesta

### A. Problema de "N+1 Queries"
- **Renderizado de Datatables:** En `app/DataTables/BaseDataTable.php`, la función `renderRows` ejecuta una consulta y luego itera sobre el modelo invocando `$filter->eloquentBuilder()->get()`. Dependiendo de cómo funcione la función de renderizado y el filtrado, esto podría provocar ejecuciones reiteradas de la DB (N+1).
- **Falta de `with()` (Eager Loading):** Se realizan demasiados filtros dinámicos (Ej. en `CollesController` o `CreateDemo` commands) sin precargar relaciones. Se observa mucho el uso directo de relaciones como métodos (`->castellers()->get()`) lo cual puede disparar demasiadas consultas en colecciones pesadas.

### B. Uso de `->get()->first()` Ineficiente
- En archivos como `NotificationOrder.php`, `CreateDemo.php` y `ScheduledNotification.php`, se observa la estructura `->get()->first()`.
  *Problema:* Esto trae **toda** la colección de la base de datos a la memoria de PHP y luego extrae el primer elemento.
  *Solución:* Se debe cambiar por `->first()`, que añade un `LIMIT 1` a la consulta de MySQL.

---

## 3. Mejoras de Programación (Código Limpio)

### A. Acoplamiento y Patrones de Diseño
- **"Fat Controllers":** Se nota que los controladores como `EventsController`, `EventBoardController` y `BoardsController` tienen demasiada lógica de negocio.
- **Recomendación:** Se debería extraer gran parte de esa lógica hacia "Services" o el patrón "Action" para mantener controladores delgados, centrados únicamente en peticiones y respuestas HTTP.
- **Lógica Mágica Relegada:** Ciertas llamadas como las relaciones no tipadas podrían beneficiarse al utilizar PHPDoc exhaustivo o el Type Hinting fuerte introducido en PHP 8.1+. Actualmente, hay disparidad.

### B. Mantenimiento y Configuración del Paquete
- El `composer.json` tiene dependencias en versiones antiguas (ej. `laravel/framework: ^8.0`). Laravel 8 está fuera de soporte (End of Life). Se recomienda encarecidamente actualizar el stack tecnológico a Laravel 10 o 11 para no perder soporte de seguridad y mejorar el rendimiento con el nuevo motor de PHP. (Actualmente se corre en PHP 8.3, lo cual da problemas de compatibilidad con paquetes abandonados como `fruitcake/laravel-cors` y `swiftmailer/swiftmailer`).

---

## 4. Mejoras de Funcionalidad y Usabilidad Generales

- **Gestión de Respuestas de API/Ajax:** Hay bastantes llamadas a endpoints de AJAX sin una estructura de respuesta estandarizada, a menudo retornando JSON ad-hoc.
- **Implementación de Caché:** Para tablas que apenas cambian o tableros de estadísticas/métricas, se recomienda encarecidamente el uso de `Cache::remember` en lugar de golpear a la DB en cada request.
- **Colas para Procesos Largos:** Las notificaciones push (como las que se envían por Firebase o Telegram en `TelegramNotificator.php`) deberían dispararse dentro de `Jobs` que corran en un "worker" (`queue:work`), y no de forma síncrona dentro del ciclo de vida del "Request/Response" HTTP, ya que esto degrada la experiencia de usuario (tiempo de carga lento o posible timeout).
