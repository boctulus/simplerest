---
title: "Webhooks: seguridad de callbacks y firma HMAC"
current_step: 1
next_step: 2
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
tags: [webhooks, security, ssrf, hmac, secrets]
---

## Objetivo

Endurecer los callbacks configurables por usuarios o integraciones para que el motor no pueda utilizarse como vector SSRF y para que el receptor pueda verificar autenticidad e integridad de cada entrega.

## Pasos planificados

1. **Auditar superficie de ataque** — relevar validación actual de `callback`, redirects, DNS resolution, protocolos permitidos, timeouts y manejo de secretos/logs.
2. **Definir política de endpoints** — permitir únicamente esquemas soportados y bloquear localhost, loopback, link-local, redes privadas, metadata endpoints y destinos internos no autorizados; contemplar DNS rebinding y redirects.
3. **Agregar secreto por subscription** — generar/almacenar un secreto de entrega de forma que no se exponga en respuestas o logs innecesarios y definir mecanismo de rotación.
4. **Firmar el cuerpo exacto** — definir headers de event id, timestamp y firma; calcular HMAC-SHA256 sobre `timestamp + "." + raw_body` o contrato equivalente documentado y versionado.
5. **Definir verificación y tolerancia temporal** — documentar algoritmo para consumidores, comparación constant-time y ventana contra replay de requests firmados.
6. **Asegurar el cliente HTTP** — timeouts, límites de redirects/body, TLS verificable y sanitización de errores antes de persistir/loggear.
7. **Agregar pruebas de seguridad** — cubrir loopback IPv4/IPv6, rangos privados, redirect a red privada, DNS host permitido/rechazado, firma válida/inválida y rotación de secreto.
8. **Actualizar documentación pública** — incluir ejemplo de verificación de firma y reglas de endpoints aceptados.

## Criterios de aceptación

- No se pueden registrar o alcanzar callbacks hacia destinos internos prohibidos mediante resolución directa o redirects.
- Cada subscription puede autenticar sus deliveries mediante una firma verificable.
- Los secretos no aparecen en payloads, logs ni errores normales.
- La firma usa el raw body realmente transmitido y un timestamp incluido en el contrato.
- La política de red y el algoritmo de firma están cubiertos por pruebas automatizadas.
