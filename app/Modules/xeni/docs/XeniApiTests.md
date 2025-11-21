# Documentación de Pruebas de Integración con API de Xeni

## Resumen

Este documento describe la suite de pruebas creada para ensayar la integración con la API de Xeni (v1/sandbox). El objetivo principal es verificar el ciclo de vida completo de una reserva de hotel: autenticación, búsqueda, obtención de tarifas, creación de la reserva y cancelación de la misma.

## Controlador de Pruebas

La lógica de las pruebas se encuentra en el siguiente archivo:

- **Ruta:** `app/Controllers/XeniTestController.php`

Este controlador contiene métodos individuales para cada paso de la integración, con comentarios que explican su propósito y cómo se corresponderían con una interacción en la interfaz de usuario (UI).

## Credenciales de Prueba (Sandbox)

El controlador utiliza las siguientes credenciales para el entorno de UAT (User Acceptance Testing) de Xeni:

- **API Key:** `96989ee3-5c9c-4557-851c-40d292ab4319`
- **Secret:** `M$72tYWz$3ZJJ71`
- **Endpoint:** `https://uat.travelapi.ai`

**Reglas importantes del entorno de pruebas:**
- Todas las reservas deben ser canceladas.
- El monto máximo por reserva es de $100.
- La disponibilidad se limita a una ventana de 3 meses.

La suite de pruebas automatizada cumple con estas reglas.

## Flujo de la Prueba

La suite ejecuta los siguientes pasos en secuencia:

1.  **Autenticación (`test_auth`)**: Obtiene un token de acceso temporal usando la API Key y el Secret.
2.  **Búsqueda de Hoteles (`test_hotel_search`)**: Realiza una búsqueda de hoteles en Londres (GB) y guarda el ID del primer hotel encontrado.
3.  **Obtención de Tarifas (`test_hotel_rates`)**: Con el ID del hotel, solicita las tarifas disponibles y busca una que sea menor a $100. Guarda la `rateKey` de esa tarifa.
4.  **Creación de Reserva (`test_hotel_booking`)**: Utiliza la `rateKey` para crear una reserva con datos de un huésped ficticio (John Doe). Guarda el `bookingId` de la reserva creada.
5.  **Cancelación de Reserva (`test_booking_cancellation`)**: Inmediatamente después de crear la reserva, utiliza el `bookingId` para cancelarla, cumpliendo con las reglas del sandbox.

## Cómo Ejecutar las Pruebas

Para ejecutar la suite de pruebas completa, simplemente acceda a la siguiente URL en su navegador:

- **URL:** `/test/xeni`

La página mostrará los resultados de cada paso del proceso en orden.

## Posible Refactorización

Para alinear esta implementación con la estructura modular del proyecto, se podría considerar mover la lógica de `XeniTestController` a una librería o SDK dentro de este mismo módulo (`app/Modules/xeni/src/XeniSDK.php`). El controlador entonces se convertiría en un cliente más ligero de dicha librería.
