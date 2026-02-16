<!DOCTYPE html>
<html>

<head>
    <title>Nota de Venta #{{ $pedido->id }}</title>
    <style>
        /* CONFIGURACIÓN GENERAL */
        body {
            font-family: sans-serif;
            font-size: 11px;
            /* Letra un poco más compacta para que quepa bien */
            color: #333;
            margin: 0;
            /* Quitamos margen default para controlar todo con @page */
        }

        @page {
            margin: 100px 40px 120px 40px;
            /* Margen superior e inferior para header/footer */
        }

        /* --- ENCABEZADO (Header fijo arriba) --- */
        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 80px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        header h2 {
            margin: 0 0 5px 0;
            text-transform: uppercase;
            color: #222;
        }

        .empresa-info {
            font-size: 10px;
            color: #555;
            line-height: 1.3;
        }

        /* --- PIE DE PÁGINA (Footer fijo abajo) --- */
        footer {
            position: fixed;
            bottom: -60px;
            /* Ajuste para que quede hasta abajo */
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* --- SECCIÓN DE INFORMACIÓN (Alineación perfecta) --- */
        .info-section {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            /* Color de fondo gris claro */
            padding: 10px;
            border: 1px solid #ddd;
        }

        /* Tabla interna para alinear etiquetas y valores */
        .info-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px;
            vertical-align: top;
            /* Alineación superior */
        }

        .label {
            font-weight: bold;
            color: #444;
            width: 70px;
            /* Ancho fijo para las etiquetas */
        }

        /* --- TABLA DE PRODUCTOS --- */
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .tabla-productos th,
        .tabla-productos td {
            border: 1px solid #ddd;
            /* Bordes grises */
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }

        /* CABECERA GRIS (Igual que la imagen) */
        .tabla-productos th {
            background-color: #f2f2f2;
            /* Gris claro */
            color: #333;
            /* Texto oscuro */
            font-weight: bold;
        }

        /* Anchos de columnas según tu imagen */
        .col-clave {
            width: 10%;
        }

        .col-nombre {
            width: 45%;
        }

        /* La más ancha */
        .col-cant {
            width: 10%;
            text-align: center;
        }

        .col-precio {
            width: 15%;
            text-align: right;
        }

        .col-importe {
            width: 20%;
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* --- TOTALES --- */
        .tabla-totales {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tabla-totales td {
            padding: 5px;
            border: none;
        }

        .total-label {
            text-align: right;
            font-weight: bold;
            color: #444;
            padding-right: 10px;
        }

        .total-value {
            text-align: right;
            width: 15%;
            border-bottom: 1px solid #ddd !important;
        }

        .total-final {
            background-color: #f2f2f2;
            border-bottom: 2px solid #aaa !important;
            /* Borde un poco más oscuro */
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO --}}
    <header>
        <h2>REFACCIONARIA Madax</h2>
        <div class="empresa-info">
            Dirección: Av. 25 ORIENTE &#35;416 local 1. COL. El Carmen<br>
            Teléfono: [555-000-0000] - Puebla, Puebla
        </div>
    </header>

    {{-- PIE DE PÁGINA --}}
    <footer>
        <p>¡Gracias por su compra!<br>Este documento es una nota de venta, no es un comprobante fiscal.</p>
    </footer>

    {{-- CONTENIDO PRINCIPAL --}}

    {{-- INFORMACIÓN DEL CLIENTE Y VENTA --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="60%">
                    <table class="info-table">
                        <tr>
                            <td class="label">Cliente:</td>
                            <td>{{ $pedido->user->name ?? 'Cliente Eliminado' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Email:</td>
                            <td>{{ $pedido->user->email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>

                <td width="40%">
                    <table class="info-table">
                        <tr>
                            <td class="label">Folio:</td>
                            <td style="color: #d9534f; font-weight: bold;">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Fecha:</td>
                            <td>{{Optional($pedido->created_at)->format('d/m/Y') ?? date('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Vendedor:</td>
                            <td>{{ $pedido->vendedor->name ?? 'Sistema' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABLA DE PRODUCTOS (Formato Imagen) --}}
    <table class="tabla-productos">
        <thead>
            <tr>
                <th class="col-clave">Clave</th>
                <th class="col-nombre">Nombre</th>
                <th class="col-cant">Cantidad</th>
                <th class="col-precio">Precio Unit.</th>
                <th class="col-importe">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
            <tr>
                {{-- Clave del producto --}}
                <td>{{ $detalle->producto->clave ?? ($detalle->producto->id ?? 'N/A') }}</td>

                {{-- Nombre --}}
                <td>{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</td>

                {{-- Cantidad --}}
                <td class="text-center">{{ $detalle->cantidad }}</td>

                {{-- Precio Unitario --}}
                <td class="text-right">${{ number_format($detalle->precio, 2) }}</td>

                {{-- Importe --}}
                <td class="text-right">${{ number_format($detalle->importe, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES --}}
    <table class="tabla-totales">
        <tr>
            {{-- Espaciador para empujar a la derecha --}}
            <td style="width: 70%;"></td>

            <td class="total-label">Subtotal:</td>
            <td class="total-value">${{ number_format($pedido->subtotal, 2) }}</td>
        </tr>
        @if($pedido->descuento > 0)
        <tr>
            <td style="width: 70%;"></td>
            <td class="total-label" style="color: #d9534f;">Descuento:</td>
            <td class="total-value" style="color: #d9534f;">- ${{ number_format($pedido->descuento, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td style="width: 70%;"></td>
            <td class="total-label" style="padding-top: 5px;">Total:</td>
            <td class="total-value total-final" style="padding-top: 5px;">
                ${{ number_format($pedido->total, 2) }}
            </td>
        </tr>
    </table>

</body>

</html>