package com.laravel.integration

import com.laravel.integration.api.LaravelApiClient
import com.laravel.integration.service.InventoryService

/**
 * Aplicación principal del servicio Kotlin integrado con Laravel
 * 
 * Demuestra:
 * - Autenticación mediante tokens (Sanctum)
 * - Llamadas a API REST
 * - Procesamiento de datos
 * - Generación de reportes
 */
fun main() {
    println("=" * 60)
    println("SERVICIO KOTLIN INTEGRADO CON LARAVEL")
    println("=" * 60)
    println()

    // Configuración
    val baseUrl = System.getenv("API_BASE_URL") ?: "http://localhost:8000"
    val apiClient = LaravelApiClient(baseUrl)

    // Demostración de flujos
    demonstrarAutenticacion(apiClient)
    println()
    demonstrarReportes(apiClient)
}

fun demonstrarAutenticacion(apiClient: LaravelApiClient) {
    println("📝 DEMOSTRACIÓN: Autenticación de Usuario")
    println("-" * 60)

    // Registrar usuario
    println("\n1️⃣  Registrando nuevo usuario...")
    val registro = apiClient.registerUser(
        id = "usuario_kotlin",
        nombres = "Servicio",
        apellidos = "Kotlin",
        correo = "kotlin.service@example.com",
        password = "SecurePass123",
        tipoDocumentoId = 1,
        telefono = "3001234567"
    )

    if (registro.success) {
        println("✓ Usuario registrado exitosamente")
        println("  Token obtenido: ${apiClient.getAuthToken()?.take(20)}...")
    } else {
        println("✗ Error: ${registro.message}")
    }

    // Obtener perfil
    println("\n2️⃣  Obteniendo perfil del usuario autenticado...")
    val profile = apiClient.getProfile()
    if (profile.success) {
        println("✓ Perfil obtenido: ${profile.data.take(100)}...")
    } else {
        println("✗ Error: ${profile.message}")
    }
}

fun demonstrarReportes(apiClient: LaravelApiClient) {
    println("📊 DEMOSTRACIÓN: Generación de Reportes")
    println("-" * 60)

    val inventoryService = InventoryService(apiClient)

    // Reporte de stock
    println("\n1️⃣  Procesando Reporte de Stock...")
    val stockReport = inventoryService.procesarReporteStockConRecomendaciones()
    if (stockReport != null) {
        println("✓ Reporte de Stock:")
        println("  • Total productos: ${stockReport.totalProductos}")
        println("  • Disponibles: ${stockReport.productosDisponibles}")
        println("  • Agotados: ${stockReport.productosAgotados}")
        println("  • Stock total: ${stockReport.stockTotal} unidades")
        println("  • Recomendaciones:")
        stockReport.recomendaciones.forEach { println("    - $it") }
    }

    // Reporte de ventas
    println("\n2️⃣  Procesando Reporte de Ventas...")
    val ventasReport = inventoryService.procesarReporteVentas()
    if (ventasReport != null) {
        println("✓ Reporte de Ventas:")
        println("  • Total general: \$${ventasReport.totalGeneral}")
        println("  • Últimas ventas por mes:")
        ventasReport.ventasMensuales.take(3).forEach { venta ->
            println("    - ${venta.mesNombre}: \$${venta.totalVentas} (${venta.cantidadFacturas} facturas)")
        }
    }

    // Reporte de usuarios
    println("\n3️⃣  Procesando Reporte de Usuarios...")
    val usuariosReport = inventoryService.procesarReporteUsuarios()
    if (usuariosReport != null) {
        println("✓ Reporte de Usuarios:")
        println("  • Total usuarios: ${usuariosReport.totalUsuarios}")
        println("  • Activos: ${usuariosReport.usuariosActivos}")
        println("  • Inactivos: ${usuariosReport.usuariosInactivos}")
        println("  • Por rol:")
        usuariosReport.usuariosPorRol.forEach { rol ->
            println("    - ${rol.rol}: ${rol.totalUsuarios} (${rol.usuariosActivos} activos)")
        }
    }
}

// Extensión para imprimir de forma legible
operator fun String.times(n: Int): String = this.repeat(n)
