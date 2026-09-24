# Arquitectura de ORM para SimpleRest

## idea general

Un pseudo-ORM que solo use arrays asociativos y que ofrezca un save() que agrupe muchas escrituras en pocas operaciones reales a la BD puede mejorar el rendimiento reduciendo round-trips y aprovechando bulk operations del motor.

# Ventajas

- Menos round-trips: agrupar 1000 inserts en 10 statements (chunks) en vez de 1000 reduce latencia dramaticamente si la red/BD añaden latencia por petición.


- Menor consumo CPU de la app: no hay coste de construcción de instancias/objetos por fila (hydrate), usar arrays es más ligero.


- Simplicidad: la API basada en arrays suele ser más simple para ETL, importaciones y microservicios.


- Facilidad para bulk SQL: se puede aprovechar INSERT ... VALUES (...),(...),..., ON DUPLICATE KEY UPDATE (MySQL) o INSERT ... ON CONFLICT (Postgres) y operaciones por lotes.


# Implementacion (discusion)

https://chatgpt.com/c/68f7e6a5-4718-8320-9a2e-d35a17cf1b91 <<< 
https://chatgpt.com/c/68f4a1b5-f788-8322-8d2f-71c08ad8f5e6


> php com db:test_orm
