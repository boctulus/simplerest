# Database - Sistema de Base de Datos

Esta sección contiene la documentación para el sistema de base de datos de SimpleRest.

## Clases Principales

### [DB](DB.md)
La clase DB es una librería clave cuyo rol principal es manejar las conexiones de base de datos y ofrecer información sobre las mismas. Posee además un mini Query Builder para consultas "raw".

### [Model](Model.md)
Un modelo extiende a la clase Model que les provee toda la funcionalidad y acepta un schema. Tanto modelos como schemas pueden ser generados por el comando "make".

### [Schema](Schema.md)
La clase principal para manejo de migraciones y constructor de esquemas de base de datos.

### [Paginator](Paginator.md)
La clase Paginator se encarga de generar el SQL para el modelo y ofrece métodos de cálculo de paginación.

## Características Principales

- **Múltiples Conexiones**: Soporte para múltiples bases de datos simultáneamente
- **Query Builder**: Constructor de consultas con métodos encadenados
- **Transacciones**: Manejo automático de transacciones con rollback
- **Migraciones**: Sistema completo de migraciones con Schema Builder
- **Paginación**: Múltiples métodos de paginación (offset/limit, take/skip, paginate)
- **Prefijos**: Soporte para prefijos de tablas
- **Procedimientos Almacenados**: Ejecución de stored procedures