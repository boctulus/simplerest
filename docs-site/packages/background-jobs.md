---

title: "Trabajos en segundo plano"
source:
  - docs/packages/background-jobs.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Trabajos en segundo plano

`boctulus/simplerest-jobs` aporta clases base; no instala una cola, worker residente ni cron del sistema. La aplicación anfitriona debe programar e iniciar las tareas.

## `BackgroundService`

Una subclase implementa `run()`. `start()` invoca `run()` y después `onSuccess()`; captura `Exception` y, ante un fallo, puede limpiar y contar intentos en memoria antes de llamar `onFail($error, $times)`. Si el límite de reintentos está configurado y se alcanza, el código ejecuta `exit`. El estado `$fails` no constituye almacenamiento persistente y no se comparte entre procesos. `Throwable` que no sea `Exception` no entra en ese `catch`.

## `CronJob` y `Task`

`CronJob` hereda `BackgroundService` y expone `getFrequency()` a partir de los campos estáticos mes, día, día de semana, hora, minuto y segundo; define constantes de días `SUN` a `SAT`. `Task` contiene propiedades de prioridad, límites, actividad y solapamiento; `canOverlap()` y `isActive()` exponen dos de ellas. Ninguna clase crea el disparador cron ni aplica exclusión mutua automáticamente.

El nombre de clase `CronJobMananger` (con la falta ortográfica histórica) se conserva en el paquete. Confirma la clase y el contrato exactos antes de depender de ella.

No se inició un proceso, no se esperó una tarea ni se ejecutó trabajo del host. La concurrencia, persistencia de reintentos, timeout e idempotencia requieren decisiones de la aplicación.
