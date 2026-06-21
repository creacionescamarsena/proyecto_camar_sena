# Servicio Kotlin Integrado con Laravel

Este proyecto demuestra la integración de un servicio en Kotlin con una aplicación Laravel, cumpliendo con los requisitos de evaluación.

## Descripción

Servicio que proporciona:
- **Cliente API REST**: Comunicación con endpoints Laravel mediante Sanctum tokens
- **Gestión de Inventario**: Procesamiento de datos de stock y productos
- **Generación de Reportes**: Procesamiento y análisis de datos
- **Autenticación**: Token-based authentication con Laravel Sanctum

## Estructura

```
kotlin-service/
├── pom.xml                    # Configuración Maven
├── src/main/kotlin/
│   └── com/laravel/integration/
│       ├── Main.kt           # Punto de entrada
│       ├── api/
│       │   └── LaravelApiClient.kt    # Cliente HTTP para API
│       └── service/
│           └── InventoryService.kt    # Lógica de negocio
└── README.md
```

## Características Implementadas

### 1. Cliente API REST (`LaravelApiClient`)
- ✓ GET, POST requests con autenticación
- ✓ Manejo de tokens Bearer (Sanctum)
- ✓ Gestión de errores HTTP
- ✓ Serialización/deserialización JSON

### 2. Servicio de Inventario (`InventoryService`)
- ✓ Procesamiento de reportes de stock
- ✓ Análisis de ventas mensuales
- ✓ Gestión de usuarios por rol
- ✓ Generación de recomendaciones automáticas

### 3. Interoperabilidad
- ✓ Comunicación HTTP con Laravel
- ✓ Autenticación mediante tokens
- ✓ Procesamiento de JSON
- ✓ Manejo de excepciones

## Compilación y Ejecución

### Requisitos
- Java 11+
- Maven 3.6+

### Compilar
```bash
mvn clean compile
```

### Empaquetar
```bash
mvn package
```

### Ejecutar
```bash
# Con variable de entorno
API_BASE_URL=http://localhost:8000 java -cp target/kotlin-inventory-service-1.0.0.jar:target/dependency/* com.laravel.integration.MainKt

# O usar valor por defecto
java -cp target/kotlin-inventory-service-1.0.0.jar:target/dependency/* com.laravel.integration.MainKt
```

## Dependencias

- **kotlin-stdlib**: Biblioteca estándar de Kotlin
- **okhttp3**: Cliente HTTP moderno
- **gson**: Procesamiento de JSON
- **slf4j**: Logging

## API Endpoints Utilizados

- `POST /api/auth/register` - Registro de usuario
- `POST /api/auth/login` - Iniciar sesión
- `GET /api/auth/profile` - Obtener perfil
- `GET /api/reportes/stock` - Reporte de stock
- `GET /api/reportes/ventas` - Reporte de ventas
- `GET /api/reportes/usuarios` - Reporte de usuarios

## Flujo de Ejecución

1. **Autenticación**: El servicio registra/autentica un usuario en Laravel
2. **Obtención de Token**: Recibe y almacena el token Sanctum
3. **Consulta de Reportes**: Obtiene datos mediante API REST
4. **Procesamiento**: Analiza datos y genera recomendaciones
5. **Presentación**: Muestra resultados en consola

## Casos de Uso

### Monitoreo de Inventario
```kotlin
val service = InventoryService(apiClient)
val stock = service.procesarReporteStockConRecomendaciones()
// Recibe análisis de productos con bajo stock y recomendaciones
```

### Análisis de Ventas
```kotlin
val ventas = service.procesarReporteVentas()
// Obtiene ventas por mes con totales
```

### Gestión de Usuarios
```kotlin
val usuarios = service.procesarReporteUsuarios()
// Analiza distribución de usuarios por rol
```

## Integración con Laravel

El servicio se comunica con la API REST de Laravel:
1. Realiza autenticación mediante `/api/auth/login`
2. Recibe token Sanctum
3. Usa token en encabezado `Authorization: Bearer {token}`
4. Consulta endpoints protegidos
5. Procesa respuestas JSON

## Seguridad

- ✓ Autenticación mediante tokens (Sanctum)
- ✓ HTTPS recomendado en producción
- ✓ Manejo de excepciones
- ✓ Validación de respuestas

## Autor

Proyecto desarrollado para demostrar integración Kotlin-PHP con Laravel
