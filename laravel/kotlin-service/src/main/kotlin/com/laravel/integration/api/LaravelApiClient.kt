package com.laravel.integration.api

import com.google.gson.Gson
import com.google.gson.JsonObject
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody
import okhttp3.MediaType.Companion.toMediaType
import java.io.IOException

/**
 * Cliente HTTP para comunicarse con la API REST de Laravel
 * 
 * Esta clase proporciona métodos para interactuar con los endpoints de la API,
 * manejando autenticación mediante tokens y gestión de sesiones de usuarios.
 */
class LaravelApiClient(
    private val baseUrl: String,
    private var authToken: String? = null
) {
    private val client = OkHttpClient()
    private val gson = Gson()
    private val jsonMediaType = "application/json".toMediaType()

    /**
     * Registrar un nuevo usuario
     */
    fun registerUser(
        id: String,
        nombres: String,
        apellidos: String,
        correo: String,
        password: String,
        tipoDocumentoId: Int,
        telefono: String? = null
    ): ApiResponse {
        val body = JsonObject().apply {
            addProperty("id_usuario", id)
            addProperty("nombres", nombres)
            addProperty("apellidos", apellidos)
            addProperty("correo", correo)
            addProperty("password", password)
            addProperty("password_confirmation", password)
            addProperty("tipo_documento_id", tipoDocumentoId)
            if (telefono != null) addProperty("telefono", telefono)
        }

        return post("/auth/register", body.toString())
    }

    /**
     * Iniciar sesión de usuario
     */
    fun loginUser(correo: String, password: String): ApiResponse {
        val body = JsonObject().apply {
            addProperty("correo", correo)
            addProperty("password", password)
        }

        val response = post("/auth/login", body.toString())
        
        // Guardar token si es exitoso
        if (response.success) {
            try {
                val jsonObject = gson.fromJson(response.data, JsonObject::class.java)
                authToken = jsonObject.get("token")?.asString
            } catch (e: Exception) {
                // Token no encontrado
            }
        }

        return response
    }

    /**
     * Cerrar sesión
     */
    fun logout(): ApiResponse {
        return post("/auth/logout", "{}")
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    fun getProfile(): ApiResponse {
        return get("/auth/profile")
    }

    /**
     * Obtener lista de chaquetas
     */
    fun getChaquetas(page: Int = 1): ApiResponse {
        return get("/chaquetas?page=$page")
    }

    /**
     * Obtener detalle de una chaqueta
     */
    fun getChaqueta(id: Int): ApiResponse {
        return get("/chaquetas/$id")
    }

    /**
     * Crear nueva chaqueta
     */
    fun createChaqueta(
        modelo: String,
        precio: Double,
        categoriaId: Int,
        materiales: List<Int> = emptyList()
    ): ApiResponse {
        val body = JsonObject().apply {
            addProperty("modelo_chaqueta", modelo)
            addProperty("precio", precio)
            addProperty("categoria_id_categoria", categoriaId)
            add("materiales", gson.toJsonTree(materiales))
        }

        return post("/chaquetas", body.toString())
    }

    /**
     * Obtener reporte de stock disponible
     */
    fun getReporteStock(): ApiResponse {
        return get("/reportes/stock")
    }

    /**
     * Obtener reporte de ventas por mes
     */
    fun getReporteVentas(): ApiResponse {
        return get("/reportes/ventas")
    }

    /**
     * Obtener reporte de usuarios por rol
     */
    fun getReporteUsuarios(): ApiResponse {
        return get("/reportes/usuarios")
    }

    /**
     * GET request
     */
    private fun get(endpoint: String): ApiResponse {
        val url = "$baseUrl/api$endpoint"
        val requestBuilder = Request.Builder()
            .url(url)
            .get()

        if (authToken != null) {
            requestBuilder.addHeader("Authorization", "Bearer $authToken")
        }

        return executeRequest(requestBuilder.build())
    }

    /**
     * POST request
     */
    private fun post(endpoint: String, jsonBody: String): ApiResponse {
        val url = "$baseUrl/api$endpoint"
        val body = RequestBody.create(jsonMediaType, jsonBody)
        val requestBuilder = Request.Builder()
            .url(url)
            .post(body)

        if (authToken != null) {
            requestBuilder.addHeader("Authorization", "Bearer $authToken")
        }

        return executeRequest(requestBuilder.build())
    }

    /**
     * Ejecutar request HTTP
     */
    private fun executeRequest(request: Request): ApiResponse {
        return try {
            val response = client.newCall(request).execute()
            val body = response.body?.string() ?: ""

            ApiResponse(
                success = response.isSuccessful,
                statusCode = response.code,
                data = body,
                message = response.message
            )
        } catch (e: IOException) {
            ApiResponse(
                success = false,
                statusCode = 0,
                data = "",
                message = "Error de conexión: ${e.message}"
            )
        }
    }

    /**
     * Establecer token manualmente
     */
    fun setAuthToken(token: String) {
        this.authToken = token
    }

    /**
     * Obtener token actual
     */
    fun getAuthToken(): String? = authToken
}

/**
 * Respuesta de API
 */
data class ApiResponse(
    val success: Boolean,
    val statusCode: Int,
    val data: String,
    val message: String
)
