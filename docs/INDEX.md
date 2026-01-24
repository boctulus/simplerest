# Índice de Documentación - SimpleRest Framework

Guía completa de la documentación del framework SimpleRest.

---

## 🚀 Inicio Rápido

- [README Principal](../README.md)
- [Quick Start](./QuickStart.md)
- [Filosofía del Framework](./SimpleRest-philosophy.md)

---

## 🏗️ Arquitectura

### Estructura del Framework
- [**Arquitectura del Framework**](./Framework-Architecture.md) - Estructura completa de directorios
- [Changelog](./CHANGELOG.md) - Registro de cambios importantes
- [Guía de Migración v0.9](./MIGRATION-v0.9.md) - Migrar a la nueva estructura
- [Core Directives](./core-directives.md) - Principios y metodologías de desarrollo

### PSR Compliance
- [Resumen PSR](./PSR-SUMMARY.md) - Estado de cumplimiento PSR
- [Guía PSR-7](./PSR-7.md) - HTTP Message Interfaces
- [Métodos Inmutables](./ImmutableMethods.md) - Immutability en Request/Response
- [Changelog PSR](./CHANGELOG-PSR.md) - Cambios relacionados con PSR

---

## 💻 Desarrollo

### Fundamentos
- [Routing](./Routing.md) - Sistema de rutas web y API
- [Request](./Request.md) - Manejo de peticiones HTTP
- [Response](./Response.md) - Manejo de respuestas HTTP
- [Middlewares](./Middlewares.md) - Middlewares y filtros

### Base de Datos
- [ORM](./ORM.md) - Object-Relational Mapping
- [Query Builder](./QueryBuilder.md) - Constructor de consultas
- [Excepciones](./Exceptions.md) - Manejo de excepciones de BD

### CLI y Testing
- [Comandos CLI](./CommandLine.md) - Sistema de comandos
- [Testing](./unit-tests-pruebas-unitarias.md) - Pruebas unitarias
- [Zippy Commands](./Zippy%20Commands.md) - Comandos del package Zippy

### API y Networking
- [API Rest](./SimpleRest-API-Rest.md) - Creación de APIs REST
- [ApiClient](./ApiClient.md) - Cliente HTTP para consumir APIs
- [Output Methods](./Output-Methods.md) - Métodos de salida y respuesta
- [Automatic Endpoints](./AutomaticEndpoints-Summary.md) - Endpoints automáticos

---

## 🔐 Seguridad y ACL

- [ACL](./ACL.md) - Control de Acceso (Access Control Lists)

---

## 🧩 Extensibilidad

### Packages y Módulos
- [Packages y Módulos](./Packages%20and%20Modules.md) - Guía de packages
- [Module Provider](./ModuleProvider.md) - Creación de módulos
- [Packages (Typo)](./Pacakges.md) - Referencia adicional

### Packages Específicos
Ver carpeta [`docs/packages/`](./packages/)

---

## 🎨 Frontend y Vistas

- [HTML Form Builder](./HTML-Form-builder.md) - Constructor de formularios HTML
- [HTML Form Builder AdminLTE](./HTML-Form-builder.AdminLTE.md) - Formularios con AdminLTE

---

## 🤖 Integraciones

- [Ollama Models](./Ollama-Models.md) - Integración con modelos LLM

---

## 📂 Organización de la Documentación

```
docs/
├─ INDEX.md                          # Este archivo
├─ Framework-Architecture.md         # Arquitectura del framework
├─ CHANGELOG.md                      # Changelog general
├─ MIGRATION-v0.9.md                 # Guía de migración
│
├─ PSR-*.md                          # Documentación PSR
├─ *-Commands.md                     # Documentación de comandos
├─ *.md                              # Documentos individuales
│
├─ etc/                              # Documentación adicional
├─ extras/                           # Extras
├─ issues/                           # Issues y soluciones
├─ packages/                         # Documentación de packages
└─ to-do/                            # Planes y tareas pendientes
```

---

## 🔍 Buscar Documentación

### Por Tema

**Routing y Controllers**:
- [Routing.md](./Routing.md)
- [Middlewares.md](./Middlewares.md)
- [Request.md](./Request.md)
- [Response.md](./Response.md)

**Base de Datos**:
- [ORM.md](./ORM.md)
- [QueryBuilder.md](./QueryBuilder.md)
- [Exceptions.md](./Exceptions.md)

**API Development**:
- [SimpleRest-API-Rest.md](./SimpleRest-API-Rest.md)
- [ApiClient.md](./ApiClient.md)
- [Output-Methods.md](./Output-Methods.md)
- [AutomaticEndpoints-Summary.md](./AutomaticEndpoints-Summary.md)

**CLI y Testing**:
- [CommandLine.md](./CommandLine.md)
- [unit-tests-pruebas-unitarias.md](./unit-tests-pruebas-unitarias.md)

**Extensibilidad**:
- [Packages and Modules.md](./Packages%20and%20Modules.md)
- [ModuleProvider.md](./ModuleProvider.md)

---

## 📋 Plantillas y Generadores

El framework incluye generadores de código para:

- Controllers
- Models
- Migrations
- Commands
- Middlewares
- Packages
- Modules

Ver: [CommandLine.md](./CommandLine.md)

---

## 🆘 Soporte

### Problemas Comunes
- [Issues](./issues/) - Carpeta con problemas conocidos y soluciones

### Reportar Problemas
Abrir un issue en el repositorio del proyecto.

---

## 📝 Contribuir

Para contribuir a la documentación:

1. Seguir el estilo de documentación existente
2. Incluir ejemplos de código cuando sea relevante
3. Mantener la estructura de directorios
4. Actualizar este índice si añades nuevos documentos

---

## 🔗 Enlaces Externos

- [Packagist](https://packagist.org/) - Repositorio de packages PHP
- [PSR Standards](https://www.php-fig.org/psr/) - PHP Standards Recommendations
- [Composer Documentation](https://getcomposer.org/doc/) - Documentación de Composer

---

**Última actualización**: 2026-01-24
**Versión del Framework**: 0.9.0

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
