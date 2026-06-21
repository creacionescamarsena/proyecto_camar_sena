package com.laravel.integration.service

import com.google.gson.Gson
import com.google.gson.JsonArray
import com.google.gson.JsonObject
import com.laravel.integration.api.LaravelApiClient

/**
 * Servicio de gestión de inventario
 * 
 * Proporciona métodos de alto nivel para gestionar el inventario de chaquetas,
 * procesando datos y aplicando lógica de negocio.
 */
class InventoryService(private val apiClient: LaravelApiClient) {
    private val gson = Gson()

    /**
     * Procesar reporte de stock y generar recomendaciones
     */
    fun procesarReporteStockConRecomendaciones(): StockReport? {
        val response = apiClient.getReporteStock()
        
        if (!response.success) {
            println("Error al obtener reporte de stock: ${response.message}")
            return null
        }

        return try {
            val jsonObject = gson.fromJson(response.data, JsonObject::class.java)
            val datos = jsonObject.get("datos").asJsonArray
            val resumen = jsonObject.get("resumen").asJsonObject

            val productos = datos.map { elemento ->
                val obj = elemento.asJsonObject
                ProductoStock(
                    id = obj.get("id_chaqueta").asInt,
                    modelo = obj.get("modelo").asString,
                    categoria = obj.get("categoria").asString,
                    precio = obj.get("precio").asDouble,
                    totalStock = obj.get("total_stock").asInt,
                    estado = obj.get("estado").asString
                )
            }

            // Generar recomendaciones
            val recomendaciones = generarRecomendacionesStock(productos)

            StockReport(
                totalProductos = resumen.get("total_productos").asInt,
                productosDisponibles = resumen.get("productos_disponibles").asInt,
                productosAgotados = resumen.get("productos_agotados").asInt,
                stockTotal = resumen.get("stock_total").asInt,
                productos = productos,
                recomendaciones = recomendaciones
            )
        } catch (e: Exception) {
            println("Error al procesar reporte: ${e.message}")
            null
        }
    }

    /**
     * Generar recomendaciones basadas en stock disponible
     */
    private fun generarRecomendacionesStock(productos: List<ProductoStock>): List<String> {
        val recomendaciones = mutableListOf<String>()

        // Productos sin stock
        val sinStock = productos.filter { it.totalStock == 0 }
        if (sinStock.isNotEmpty()) {
            recomendaciones.add("⚠️ ${sinStock.size} producto(s) agotado(s): ${sinStock.map { it.modelo }.joinToString(", ")}")
        }

        // Stock bajo
        val stockBajo = productos.filter { it.totalStock in 1..10 }
        if (stockBajo.isNotEmpty()) {
            recomendaciones.add("⚠️ ${stockBajo.size} producto(s) con stock bajo (1-10 unidades)")
        }

        // Stock crítico
        val stockCritico = productos.filter { it.totalStock in 1..5 }
        if (stockCritico.isNotEmpty()) {
            recomendaciones.add("🚨 CRÍTICO: ${stockCritico.size} producto(s) con stock crítico (<5 unidades)")
        }

        if (recomendaciones.isEmpty()) {
            recomendaciones.add("✓ Inventario en buen estado")
        }

        return recomendaciones
    }

    /**
     * Procesar reporte de ventas mensuales
     */
    fun procesarReporteVentas(): VentasReport? {
        val response = apiClient.getReporteVentas()
        
        if (!response.success) {
            println("Error al obtener reporte de ventas: ${response.message}")
            return null
        }

        return try {
            val jsonObject = gson.fromJson(response.data, JsonObject::class.java)
            val datos = jsonObject.get("datos").asJsonArray
            val totalGeneral = jsonObject.get("total_general").asDouble

            val ventas = datos.map { elemento ->
                val obj = elemento.asJsonObject
                VentaMensual(
                    mesNombre = obj.get("mes_nombre").asString,
                    mes = obj.get("mes").asInt,
                    anio = obj.get("anio").asInt,
                    totalVentas = obj.get("total_ventas").asDouble,
                    cantidadFacturas = obj.get("cantidad_facturas").asInt
                )
            }

            VentasReport(
                totalGeneral = totalGeneral,
                ventasMensuales = ventas
            )
        } catch (e: Exception) {
            println("Error al procesar reporte de ventas: ${e.message}")
            null
        }
    }

    /**
     * Procesar reporte de usuarios
     */
    fun procesarReporteUsuarios(): UsuariosReport? {
        val response = apiClient.getReporteUsuarios()
        
        if (!response.success) {
            println("Error al obtener reporte de usuarios: ${response.message}")
            return null
        }

        return try {
            val jsonObject = gson.fromJson(response.data, JsonObject::class.java)
            val datos = jsonObject.get("datos").asJsonArray
            val resumen = jsonObject.get("resumen").asJsonObject

            val usuariosPorRol = datos.map { elemento ->
                val obj = elemento.asJsonObject
                UsuariosPorRol(
                    rol = obj.get("rol").asString,
                    totalUsuarios = obj.get("total_usuarios").asInt,
                    usuariosActivos = obj.get("usuarios_activos").asInt,
                    usuariosInactivos = obj.get("usuarios_inactivos").asInt
                )
            }

            UsuariosReport(
                totalUsuarios = resumen.get("total_usuarios").asInt,
                usuariosActivos = resumen.get("usuarios_activos").asInt,
                usuariosInactivos = resumen.get("usuarios_inactivos").asInt,
                usuariosPorRol = usuariosPorRol
            )
        } catch (e: Exception) {
            println("Error al procesar reporte de usuarios: ${e.message}")
            null
        }
    }
}

// Data classes para reportes
data class StockReport(
    val totalProductos: Int,
    val productosDisponibles: Int,
    val productosAgotados: Int,
    val stockTotal: Int,
    val productos: List<ProductoStock>,
    val recomendaciones: List<String>
)

data class ProductoStock(
    val id: Int,
    val modelo: String,
    val categoria: String,
    val precio: Double,
    val totalStock: Int,
    val estado: String
)

data class VentasReport(
    val totalGeneral: Double,
    val ventasMensuales: List<VentaMensual>
)

data class VentaMensual(
    val mesNombre: String,
    val mes: Int,
    val anio: Int,
    val totalVentas: Double,
    val cantidadFacturas: Int
)

data class UsuariosReport(
    val totalUsuarios: Int,
    val usuariosActivos: Int,
    val usuariosInactivos: Int,
    val usuariosPorRol: List<UsuariosPorRol>
)

data class UsuariosPorRol(
    val rol: String,
    val totalUsuarios: Int,
    val usuariosActivos: Int,
    val usuariosInactivos: Int
)
