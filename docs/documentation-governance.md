# Gobernanza de la documentación de SimpleRest

## Propósito

Esta política protege la documentación de SimpleRest como memoria técnica y editorial del proyecto. El código describe el comportamiento implementado, pero no siempre conserva por sí solo la intención de diseño, las decisiones arquitectónicas, los contratos conceptuales ni el contexto que llevó a una implementación.

Por esa razón, la documentación canónica no debe tratarse como texto descartable que una herramienta o un agente de IA pueda regenerar libremente.

## Capas documentales

SimpleRest distingue tres capas:

1. **Fuente editorial canónica** — corpus mantenido bajo autoridad del maintainer. Conserva hechos, intención, decisiones, contexto y contratos conceptuales.
2. **Documentación pública en español** — representación editorial derivada de la fuente canónica, preparada para lectura y navegación. Puede ser pulida o regenerada siempre que no se convierta en una nueva fuente de verdad independiente.
3. **Traducciones** — versiones derivadas de la misma fuente canónica, por ejemplo inglés, italiano o portugués. Ninguna traducción tiene autoridad sobre la fuente canónica.

La documentación pública y las traducciones son artefactos derivados y regenerables. La fuente editorial canónica no lo es.

## Autoridad

El maintainer conserva la autoridad editorial final sobre la fuente canónica.

El código, la configuración y las pruebas son la autoridad sobre el comportamiento actualmente implementado. La documentación canónica puede además expresar intención, diseño esperado o contratos conceptuales que todavía no estén implementados o que hayan sufrido una regresión.

Por lo tanto, una discrepancia entre código y documentación no autoriza a modificar automáticamente la documentación para hacerla coincidir con el código.

Ante una discrepancia se debe determinar cuál de estas situaciones aplica:

- la documentación quedó obsoleta;
- el código contiene una regresión;
- la implementación está incompleta;
- existe una decisión de diseño aún no implementada;
- la afirmación necesita evidencia adicional.

Hasta resolverla, la discrepancia debe quedar explícita; no debe borrarse una de las dos fuentes para producir una falsa coherencia.

## Política de edición de la fuente canónica

La fuente canónica es **editable mediante cambios localizados, no regenerable mediante reescritura libre**.

Un agente humano o de IA puede:

- corregir una afirmación concreta cuando existe evidencia suficiente;
- actualizar nombres de clases, rutas, comandos, firmas o ejemplos;
- añadir una sección nueva;
- ampliar o aclarar una explicación existente;
- corregir enlaces, formato, ortografía o terminología;
- marcar límites, incertidumbres, regresiones o comportamiento no verificado;
- proponer una reorganización cuando existe una razón técnica o editorial concreta.

Un agente de IA no debe, por iniciativa propia:

- reemplazar un documento canónico completo por una nueva versión redactada desde cero;
- reescribir una sección extensa sólo para uniformar estilo o tono;
- eliminar razonamiento, contexto histórico o intención de diseño por considerarlos redundantes;
- sustituir explicaciones originales por resúmenes más breves sin preservar la información que contienen;
- fusionar, dividir, mover o eliminar documentos canónicos sin justificar el cambio;
- asumir que el código actual invalida automáticamente una intención documentada.

## Reescrituras amplias

Una reescritura completa o sustancial de un documento o sección canónica requiere justificación explícita y aprobación del maintainer.

Son razones posibles:

- el documento describe mayoritariamente una arquitectura que ya no existe;
- el contenido tiene contradicciones graves que no pueden resolverse mediante parches localizados;
- una reestructuración es necesaria para separar conceptos distintos;
- el maintainer decide sustituir deliberadamente el contrato editorial anterior.

Incluso en esos casos debe preservarse el material anterior cuando contenga información histórica o intención útil, mediante Git o mediante un archivo histórico cuando corresponda.

## Documentos nuevos

Una IA puede proponer o crear documentación nueva. Una vez que el maintainer la acepta como parte de la fuente canónica, queda protegida por esta misma política.

## Documentación derivada

Las versiones públicas y las traducciones pueden aplicar transformaciones editoriales más agresivas:

- normalizar estilo;
- reorganizar secciones para mejorar navegación;
- eliminar redundancias;
- adaptar ejemplos y advertencias a la presentación;
- traducir;
- regenerar páginas completas.

Estas transformaciones no deben introducir contratos, hechos o decisiones que no estén respaldados por la fuente canónica, el código o evidencia explícita.

Si una versión derivada contradice la fuente canónica, prevalece la fuente canónica.

## Trazabilidad

Siempre que sea posible, una página derivada debe poder identificar de qué documento canónico procede y qué revisión utilizó.

La automatización futura puede incorporar metadatos como:

```yaml
source: /canonical/database/query-builder.md
source_revision: <commit>
```

El mecanismo concreto puede cambiar; la propiedad requerida es la trazabilidad.

## Cambios realizados por IA

Los agentes deben preferir diffs pequeños y revisables sobre documentos canónicos.

Antes de una modificación amplia deben explicar:

1. qué afirmación o estructura necesita cambiar;
2. por qué un parche localizado no es suficiente;
3. qué información se conservará;
4. qué evidencia respalda el cambio.

Una mejora puramente estilística no justifica una reescritura sustancial de la fuente canónica.

## Automatización de protección

El proyecto debe incorporar progresivamente controles para detectar cambios destructivos sobre documentos canónicos. Como mínimo, el control debería poder advertir o bloquear:

- eliminación de un documento canónico;
- renombrados o movimientos no declarados;
- diffs que sustituyan una proporción anormalmente grande del texto;
- conversiones automáticas que pierdan secciones;
- cambios derivados que no indiquen su fuente.

El umbral técnico es una alarma, no una definición semántica de reescritura. La aprobación humana sigue siendo la autoridad final.

## Relación con la auditoría documental

La auditoría verifica afirmaciones contra implementación, configuración, pruebas y ejecuciones reproducidas. Su función es detectar el estado real de cada afirmación, no borrar intención documental.

Cuando una auditoría encuentra una contradicción debe clasificarla y proponer un cambio localizado o una decisión explícita. No debe sustituir automáticamente el documento completo por una reconstrucción generada.

## Regla resumida

> La fuente editorial canónica de SimpleRest puede evolucionar, pero no debe ser regenerada. La IA trabaja sobre ella mediante cambios localizados y justificables. La documentación pública y las traducciones son derivados regenerables de esa fuente.
