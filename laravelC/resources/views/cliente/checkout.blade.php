@extends('layouts.app_cliente')

@section('title', 'Finalizar compra')

@section('content')

<div class="container mt-5 mb-5">

    <div class="card shadow border-0 p-4">

        <h2 class="text-center mb-4">
            Confirmar datos de envío
        </h2>

        <form action="{{ route('cliente.pedido.guardar') }}" method="POST">

            @csrf

            <div class="row">

                <!-- FORMULARIO -->
                <div class="col-lg-8">

                    <!-- País -->
                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            País
                        </label>

                        <select id="pais"
                                name="pais"
                                class="form-select"
                                required>

                            <option value="">
                                Seleccione un país
                            </option>

                            <option value="Argentina">🇦🇷 Argentina</option>
                            <option value="Bolivia">🇧🇴 Bolivia</option>
                            <option value="Brasil">🇧🇷 Brasil</option>
                            <option value="Chile">🇨🇱 Chile</option>
                            <option value="Colombia">🇨🇴 Colombia</option>
                            <option value="Ecuador">🇪🇨 Ecuador</option>
                            <option value="Paraguay">🇵🇾 Paraguay</option>
                            <option value="Perú">🇵🇪 Perú</option>
                            <option value="Uruguay">🇺🇾 Uruguay</option>
                            <option value="México">🇲🇽 México</option>

                        </select>

                    </div>

                    <!-- Ciudad -->
                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Ciudad
                        </label>

                        <select id="ciudad"
                                name="ciudad"
                                class="form-select"
                                required>

                            <option value="">
                                Primero seleccione un país
                            </option>

                        </select>

                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Dirección
                        </label>

                        <input type="text"
                               name="direccion"
                               class="form-control"
                               placeholder="Ej: Calle 15 #20-35"
                               required>

                    </div>

                    <!-- Código Postal -->
                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Código Postal
                        </label>

                        <input type="text"
                               name="codigo_postal"
                               class="form-control"
                               placeholder="Ingrese código postal">

                    </div>

                   <!-- Método Pago -->
<div class="mb-3">

    <label class="form-label fw-bold">
        Método de pago
    </label>

    <select name="metodo_pago"
            class="form-select"
            required>

        <option value="">
            Seleccione
        </option>

        <option value="Nequi">
            Nequi
        </option>

        <option value="Daviplata">
            Daviplata
        </option>

        <option value="Transferencia Bancaria">
            Transferencia Bancaria
        </option>

        <option value="PayPal">
            PayPal
        </option>

        <option value="Mercado Pago">
            Mercado Pago
        </option>

    </select>

</div>

<input type="hidden" name="moneda" id="moneda_hidden">

<input type="hidden" name="total_convertido" id="total_hidden">

<input type="hidden" name="envio_convertido" id="envio_hidden">

<button type="submit"
        class="btn btn-success btn-lg">

    Confirmar pedido

</button>
                </div>

                <!-- PANEL LATERAL -->
                <div class="col-lg-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h5 class="mb-3">
                                Información del envío
                            </h5>

                            <p>
                                <strong>País:</strong>
                                <span id="pais-seleccionado">-</span>
                            </p>





                           <p>
    <strong>Moneda:</strong>
    <span id="moneda">COP</span>
</p>

<p>
    <strong>Subtotal:</strong>
    <span id="subtotal-convertido">
        {{ number_format($subtotal,0,',','.') }} COP
    </span>
</p>

<p>
    <strong>Costo envío:</strong>
    <span id="costo-envio">$0 COP</span>
</p>

<hr>

<h5 class="text-success fw-bold">
    Total:
</h5>

<h4 id="total-convertido">
    {{ number_format($subtotal,0,',','.') }} COP
</h4>

<hr>

<div class="text-center">

    <span id="bandera"
          style="font-size:60px;">
    </span>

</div>





                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<script>

const ciudades = {

    Argentina:["Buenos Aires","Córdoba","Rosario","Mendoza"],

    Bolivia:["La Paz","Santa Cruz","Cochabamba"],

    Brasil:["São Paulo","Rio de Janeiro","Brasilia"],

    Chile:["Santiago","Valparaíso","Concepción"],

    Colombia:[
        "Bogotá",
        "Medellín",
        "Cali",
        "Barranquilla",
        "Cartagena",
        "Bucaramanga",
        "Pereira",
        "Manizales"
    ],

    Ecuador:["Quito","Guayaquil","Cuenca"],

    Paraguay:["Asunción","Ciudad del Este"],

    Perú:["Lima","Arequipa","Cusco"],

    Uruguay:["Montevideo","Salto"],

    México:["Ciudad de México","Guadalajara","Monterrey"]

};

const banderas = {

    Argentina:"🇦🇷",
    Bolivia:"🇧🇴",
    Brasil:"🇧🇷",
    Chile:"🇨🇱",
    Colombia:"🇨🇴",
    Ecuador:"🇪🇨",
    Paraguay:"🇵🇾",
    Perú:"🇵🇪",
    Uruguay:"🇺🇾",
    México:"🇲🇽"

};

const monedas = {

    Argentina:"ARS",
    Bolivia:"BOB",
    Brasil:"BRL",
    Chile:"CLP",
    Colombia:"COP",
    Ecuador:"USD",
    Paraguay:"PYG",
    Perú:"PEN",
    Uruguay:"UYU",
    México:"MXN"

};

const enviosCOP = {

    Argentina:40000,
    Bolivia:30000,
    Brasil:45000,
    Chile:35000,
    Colombia:15000,
    Ecuador:25000,
    Paraguay:35000,
    Perú:30000,
    Uruguay:40000,
    México:50000

};

const tasas = {

    Colombia:1,
    México:0.00095,
    Argentina:0.22,
    Chile:0.23,
    Perú:0.00095,
    Ecuador:0.00024,
    Brasil:0.0013,
    Uruguay:0.0095,
    Paraguay:1.8,
    Bolivia:0.0017

};

const subtotalCOP = {{ $subtotal }};

document.getElementById('pais').addEventListener('change', function() {

    const pais = this.value;

    document.getElementById('pais-seleccionado').innerText =
        pais;

    document.getElementById('bandera').innerText =
        banderas[pais] || '';

    document.getElementById('moneda').innerText =
        monedas[pais] || 'COP';

    const tasa = tasas[pais] || 1;

    const envioCOP = enviosCOP[pais] || 0;

    const envioConvertido = envioCOP * tasa;

    const subtotalConvertido = subtotalCOP * tasa;

    const totalConvertido =
        subtotalConvertido + envioConvertido;

    document.getElementById('costo-envio').innerText =
        envioConvertido.toLocaleString('es-CO') +
        ' ' +
        (monedas[pais] || 'COP');

    document.getElementById('subtotal-convertido').innerText =
        subtotalConvertido.toLocaleString('es-CO') +
        ' ' +
        (monedas[pais] || 'COP');

    document.getElementById('total-convertido').innerText =
        totalConvertido.toLocaleString('es-CO') +
        ' ' +
        (monedas[pais] || 'COP');

    // CAMPOS OCULTOS PARA GUARDAR EN BD

    document.getElementById('moneda_hidden').value =
        monedas[pais] || 'COP';

    document.getElementById('total_hidden').value =
        totalConvertido;

    document.getElementById('envio_hidden').value =
        envioConvertido;

    const ciudad = document.getElementById('ciudad');

    ciudad.innerHTML =
        '<option value="">Seleccione una ciudad</option>';

    if (ciudades[pais]) {

        ciudades[pais].forEach(function(c) {

            ciudad.innerHTML +=
                `<option value="${c}">${c}</option>`;

        });

    }

});
</script>

@endsection