package com.example.creacionescamar

import android.app.Activity
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import kotlinx.coroutines.*
import org.json.JSONArray
import org.json.JSONObject
import java.io.OutputStream
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL
import java.util.UUID

class MainActivity : AppCompatActivity() {

    private val BASE_URL = "http://10.0.2.2:8000/api"

    private val LOGIN_CORREO = "admin@test.com"
    private val LOGIN_PASSWORD = "123456"

    private var authToken: String? = null

    private lateinit var etIdChaqueta: EditText
    private lateinit var etNombre: EditText
    private lateinit var spinnerCategoria: Spinner
    private lateinit var etNuevaCategoria: EditText
    private lateinit var etPrecio: EditText
    private lateinit var etXS: EditText
    private lateinit var etS: EditText
    private lateinit var etM: EditText
    private lateinit var etL: EditText
    private lateinit var etXL: EditText
    private lateinit var btnSeleccionarImagen: Button
    private lateinit var tvArchivo: TextView
    private lateinit var lvMateriales: ListView
    private lateinit var btnGuardar: Button
    private lateinit var btnCancelar: Button

    private var categorias = mutableListOf<Pair<Long, String>>()
    private var materiales = mutableListOf<Pair<Long, String>>()
    private var imagenUri: Uri? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        etIdChaqueta = findViewById(R.id.et_id_chaqueta)
        etNombre = findViewById(R.id.et_nombre)
        spinnerCategoria = findViewById(R.id.spinner_categoria)
        etNuevaCategoria = findViewById(R.id.et_nueva_categoria)
        etPrecio = findViewById(R.id.et_precio)
        etXS = findViewById(R.id.et_xs)
        etS = findViewById(R.id.et_s)
        etM = findViewById(R.id.et_m)
        etL = findViewById(R.id.et_l)
        etXL = findViewById(R.id.et_xl)
        btnSeleccionarImagen = findViewById(R.id.btn_seleccionar_imagen)
        tvArchivo = findViewById(R.id.tv_archivo_nombre)
        lvMateriales = findViewById(R.id.lv_materiales)
        btnGuardar = findViewById(R.id.btn_guardar)
        btnCancelar = findViewById(R.id.btn_cancelar)

        loginYCargarDatos()

        btnSeleccionarImagen.setOnClickListener {
            val intent = Intent(Intent.ACTION_GET_CONTENT)
            intent.type = "image/*"
            intent.addCategory(Intent.CATEGORY_OPENABLE)
            intent.setPackage("com.android.documentsui")
            try {
                startActivityForResult(intent, 100)
            } catch (e: Exception) {
                val fallback = Intent(Intent.ACTION_GET_CONTENT)
                fallback.type = "image/*"
                startActivityForResult(fallback, 100)
            }
        }

        btnGuardar.setOnClickListener { guardarChaqueta() }
        btnCancelar.setOnClickListener { limpiarFormulario() }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == Activity.RESULT_OK) {
            imagenUri = data?.data
            tvArchivo.text = imagenUri?.lastPathSegment ?: "Archivo seleccionado"
        }
    }

    private fun loginYCargarDatos() {
        CoroutineScope(Dispatchers.IO).launch {
            try {
                val loginBody = JSONObject().apply {
                    put("correo", LOGIN_CORREO)
                    put("password", LOGIN_PASSWORD)
                }
                val loginResponse = postJson("$BASE_URL/auth/login", loginBody.toString(), null)
                val loginJson = JSONObject(loginResponse)

                if (!loginJson.has("token")) {
                    withContext(Dispatchers.Main) {
                        Toast.makeText(this@MainActivity,
                            "Error de login: ${loginJson.optString("message")}", Toast.LENGTH_LONG).show()
                    }
                    return@launch
                }

                authToken = loginJson.getString("token")

                val catJson = get("$BASE_URL/categorias", authToken)
                val catObj = JSONObject(catJson)
                val catArray = catObj.getJSONArray("data")
                val catList = mutableListOf<Pair<Long, String>>()
                val catNames = mutableListOf("Seleccionar categoría existente")
                for (i in 0 until catArray.length()) {
                    val obj = catArray.getJSONObject(i)
                    catList.add(Pair(obj.getLong("id_categoria"), obj.getString("tipo_categoria")))
                    catNames.add(obj.getString("tipo_categoria"))
                }

                val matJson = get("$BASE_URL/materiales", authToken)
                val matObj = JSONObject(matJson)
                val matArray = matObj.getJSONArray("data")
                val matList = mutableListOf<Pair<Long, String>>()
                val matNames = mutableListOf<String>()
                for (i in 0 until matArray.length()) {
                    val obj = matArray.getJSONObject(i)
                    matList.add(Pair(obj.getLong("id_materiales"), obj.getString("material")))
                    matNames.add(obj.getString("material"))
                }

                withContext(Dispatchers.Main) {
                    categorias = catList
                    materiales = matList

                    val catAdapter = ArrayAdapter(this@MainActivity,
                        android.R.layout.simple_spinner_item, catNames)
                    catAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    spinnerCategoria.adapter = catAdapter

                    val matAdapter = ArrayAdapter(this@MainActivity,
                        android.R.layout.simple_list_item_multiple_choice, matNames)
                    lvMateriales.adapter = matAdapter

                    Toast.makeText(this@MainActivity, "Conectado correctamente", Toast.LENGTH_SHORT).show()
                }

            } catch (e: Exception) {
                withContext(Dispatchers.Main) {
                    Toast.makeText(this@MainActivity,
                        "Error conectando: ${e.message}", Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    private fun guardarChaqueta() {
        val idChaquetaStr = etIdChaqueta.text.toString().trim()
        val nombre = etNombre.text.toString().trim()
        val precio = etPrecio.text.toString().trim()

        // --- Validación ID: obligatorio, solo números ---
        if (idChaquetaStr.isEmpty()) {
            Toast.makeText(this, "El ID de chaqueta es obligatorio", Toast.LENGTH_SHORT).show()
            return
        }
        if (!idChaquetaStr.matches(Regex("^[0-9]+$"))) {
            Toast.makeText(this, "El ID de chaqueta solo puede contener números", Toast.LENGTH_SHORT).show()
            return
        }
        val idChaqueta = idChaquetaStr.toLongOrNull()
        if (idChaqueta == null || idChaqueta <= 0) {
            Toast.makeText(this, "El ID de chaqueta debe ser un número válido", Toast.LENGTH_SHORT).show()
            return
        }

        // --- Validación nombre: obligatorio, solo letras ---
        if (nombre.isEmpty()) {
            Toast.makeText(this, "El nombre es obligatorio", Toast.LENGTH_SHORT).show()
            return
        }
        if (!nombre.matches(Regex("^[a-zA-ZáéíóúÁÉÍÓÚñÑ\\s]+$"))) {
            Toast.makeText(this, "El nombre solo puede contener letras", Toast.LENGTH_SHORT).show()
            return
        }

        // --- Validación precio: obligatorio, solo números ---
        if (precio.isEmpty()) {
            Toast.makeText(this, "El precio es obligatorio", Toast.LENGTH_SHORT).show()
            return
        }
        val precioDouble = precio.toDoubleOrNull()
        if (precioDouble == null || precioDouble <= 0) {
            Toast.makeText(this, "El precio debe ser un número válido", Toast.LENGTH_SHORT).show()
            return
        }

        if (authToken == null) {
            Toast.makeText(this, "No hay sesión activa, espera a que cargue", Toast.LENGTH_SHORT).show()
            return
        }

        // --- Validación imagen: obligatoria ---
        if (imagenUri == null) {
            Toast.makeText(this, "Debes seleccionar una imagen", Toast.LENGTH_SHORT).show()
            return
        }

        // --- Validación materiales: mínimo 1 ---
        val materialesSeleccionados = mutableListOf<Long>()
        for (i in 0 until lvMateriales.count) {
            if (lvMateriales.isItemChecked(i)) materialesSeleccionados.add(materiales[i].first)
        }
        if (materialesSeleccionados.isEmpty()) {
            Toast.makeText(this, "Debes seleccionar al menos un material", Toast.LENGTH_SHORT).show()
            return
        }
        val materialesUnicos = materialesSeleccionados.distinct()

        // --- Validación categoría ---
        var categoriaId: Long? = null
        val nuevaCat = etNuevaCategoria.text.toString().trim()
        val spinnerPos = spinnerCategoria.selectedItemPosition
        if (spinnerPos > 0) categoriaId = categorias[spinnerPos - 1].first

        if (categoriaId == null && nuevaCat.isEmpty()) {
            Toast.makeText(this, "Selecciona o escribe una categoría", Toast.LENGTH_SHORT).show()
            return
        }

        // --- Validación tallas: al menos una cantidad > 0 ---
        val stock = mapOf(
            "XS" to (etXS.text.toString().toIntOrNull() ?: 0),
            "S" to (etS.text.toString().toIntOrNull() ?: 0),
            "M" to (etM.text.toString().toIntOrNull() ?: 0),
            "L" to (etL.text.toString().toIntOrNull() ?: 0),
            "XL" to (etXL.text.toString().toIntOrNull() ?: 0)
        )
        if (stock.values.all { it <= 0 }) {
            Toast.makeText(this, "Debes ingresar al menos una talla con cantidad mayor a 0", Toast.LENGTH_SHORT).show()
            return
        }

        // --- Verificar duplicados antes de guardar ---
        CoroutineScope(Dispatchers.IO).launch {
            try {
                val existeResponse = get("$BASE_URL/chaquetas", authToken)
                val existeObj = JSONObject(existeResponse)
                val existeArray = existeObj.optJSONArray("data") ?: JSONArray()

                var idRepetido = false
                var nombreRepetido = false
                for (i in 0 until existeArray.length()) {
                    val item = existeArray.getJSONObject(i)
                    if (item.optLong("id_chaqueta") == idChaqueta) idRepetido = true
                    if (item.optString("modelo_chaqueta").trim().equals(nombre, ignoreCase = true)) nombreRepetido = true
                }

                withContext(Dispatchers.Main) {
                    when {
                        idRepetido -> Toast.makeText(this@MainActivity, "Ya existe una chaqueta con ese ID", Toast.LENGTH_LONG).show()
                        nombreRepetido -> Toast.makeText(this@MainActivity, "Ya existe un producto con ese nombre", Toast.LENGTH_LONG).show()
                        else -> continuarGuardado(idChaqueta, nombre, precioDouble, categoriaId, nuevaCat, materialesUnicos, stock)
                    }
                }
            } catch (e: Exception) {
                withContext(Dispatchers.Main) {
                    continuarGuardado(idChaqueta, nombre, precioDouble, categoriaId, nuevaCat, materialesUnicos, stock)
                }
            }
        }
    }

    private fun continuarGuardado(
        idChaqueta: Long,
        nombre: String,
        precio: Double,
        categoriaId: Long?,
        nuevaCat: String,
        materialesUnicos: List<Long>,
        stock: Map<String, Int>
    ) {
        CoroutineScope(Dispatchers.IO).launch {
            try {
                val campos = mutableMapOf<String, String>()
                campos["id_chaqueta"] = idChaqueta.toString()
                campos["modelo_chaqueta"] = nombre
                campos["precio"] = precio.toString()
                if (categoriaId != null) campos["categoria_id_categoria"] = categoriaId.toString()
                if (nuevaCat.isNotEmpty()) campos["nueva_categoria"] = nuevaCat

                materialesUnicos.forEachIndexed { index, id ->
                    campos["materiales[$index]"] = id.toString()
                }
                stock.forEach { (talla, cantidad) ->
                    campos["stock[$talla]"] = cantidad.toString()
                }

                val response = postMultipartConImagen(
                    "$BASE_URL/chaquetas",
                    campos,
                    imagenUri!!,
                    authToken
                )
                val result = JSONObject(response)

                withContext(Dispatchers.Main) {
                    if (result.has("data")) {
                        Toast.makeText(this@MainActivity, "¡Guardado exitosamente!", Toast.LENGTH_SHORT).show()
                        limpiarFormulario()
                    } else {
                        Toast.makeText(this@MainActivity,
                            result.optString("message", "Error al guardar"), Toast.LENGTH_LONG).show()
                    }
                }
            } catch (e: Exception) {
                withContext(Dispatchers.Main) {
                    Toast.makeText(this@MainActivity, "Error: ${e.message}", Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    private fun limpiarFormulario() {
        etIdChaqueta.text.clear()
        etNombre.text.clear()
        etPrecio.text.clear()
        etNuevaCategoria.text.clear()
        etXS.setText("0"); etS.setText("0"); etM.setText("0")
        etL.setText("0"); etXL.setText("0")
        tvArchivo.text = "Ningún archivo seleccionado"
        imagenUri = null
        spinnerCategoria.setSelection(0)
        for (i in 0 until lvMateriales.count) lvMateriales.setItemChecked(i, false)
    }

    private fun get(urlStr: String, token: String?): String {
        val url = URL(urlStr)
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "GET"
        conn.setRequestProperty("Accept", "application/json")
        if (token != null) conn.setRequestProperty("Authorization", "Bearer $token")
        return conn.inputStream.bufferedReader().readText()
    }

    private fun postJson(urlStr: String, json: String, token: String?): String {
        val url = URL(urlStr)
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "POST"
        conn.setRequestProperty("Content-Type", "application/json")
        conn.setRequestProperty("Accept", "application/json")
        if (token != null) conn.setRequestProperty("Authorization", "Bearer $token")
        conn.doOutput = true
        OutputStreamWriter(conn.outputStream).use { it.write(json) }
        return try {
            conn.inputStream.bufferedReader().readText()
        } catch (e: Exception) {
            conn.errorStream?.bufferedReader()?.readText() ?: e.message ?: "Error"
        }
    }

    /**
     * Envía un POST tipo multipart/form-data con campos de texto + 1 archivo de imagen.
     * Esto permite que Laravel reciba la imagen real con $request->file('imagen').
     */
    private fun postMultipartConImagen(
        urlStr: String,
        campos: Map<String, String>,
        imagenUri: Uri,
        token: String?
    ): String {
        val boundary = "----KotlinBoundary${UUID.randomUUID()}"
        val lineEnd = "\r\n"
        val twoHyphens = "--"

        val url = URL(urlStr)
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "POST"
        conn.doOutput = true
        conn.useCaches = false
        conn.setRequestProperty("Accept", "application/json")
        conn.setRequestProperty("Content-Type", "multipart/form-data; boundary=$boundary")
        if (token != null) conn.setRequestProperty("Authorization", "Bearer $token")

        val outputStream: OutputStream = conn.outputStream

        // Campos de texto normales
        for ((key, value) in campos) {
            outputStream.write((twoHyphens + boundary + lineEnd).toByteArray())
            outputStream.write("Content-Disposition: form-data; name=\"$key\"$lineEnd".toByteArray())
            outputStream.write(lineEnd.toByteArray())
            outputStream.write((value + lineEnd).toByteArray())
        }

        // Archivo de imagen
        val inputStream = contentResolver.openInputStream(imagenUri)
        val fileName = "imagen_${System.currentTimeMillis()}.jpg"
        val mimeType = contentResolver.getType(imagenUri) ?: "image/jpeg"

        outputStream.write((twoHyphens + boundary + lineEnd).toByteArray())
        outputStream.write("Content-Disposition: form-data; name=\"imagen\"; filename=\"$fileName\"$lineEnd".toByteArray())
        outputStream.write("Content-Type: $mimeType$lineEnd".toByteArray())
        outputStream.write(lineEnd.toByteArray())

        inputStream?.use { input ->
            val buffer = ByteArray(4096)
            var bytesRead: Int
            while (input.read(buffer).also { bytesRead = it } != -1) {
                outputStream.write(buffer, 0, bytesRead)
            }
        }
        outputStream.write(lineEnd.toByteArray())
        outputStream.write((twoHyphens + boundary + twoHyphens + lineEnd).toByteArray())
        outputStream.flush()
        outputStream.close()

        return try {
            conn.inputStream.bufferedReader().readText()
        } catch (e: Exception) {
            conn.errorStream?.bufferedReader()?.readText() ?: e.message ?: "Error"
        }
    }
}