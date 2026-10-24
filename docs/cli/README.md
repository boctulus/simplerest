# CLI

## Documentación disponible

- [Catálogo de `php com`](commands.md) — CommandRegistry, descubrimiento de proveedores, prioridad, aliases y grupos presentes en la ayuda del checkout.
- [Routing CLI con CliRouter](routing.md) — registro de comandos de aplicación/paquete, argumentos, grupos y límites observados en fuente. No se invocaron rutas de `CliRouter`; sólo se consultó la ayuda de `php com`.
- [Ciclo de migraciones](../database/migrations.md) — archivos, registro y comandos de migración; no se ejecutó DDL ni una migración.

## Límites

La ayuda de `php com` es dinámica y depende de la aplicación y los paquetes configurados. Los comandos operativos, los seeds y sus efectos no se presentan como reproducidos por haber consultado la ayuda. `CliRouter::command()` y `CommandRegistry` son APIs distintas.
