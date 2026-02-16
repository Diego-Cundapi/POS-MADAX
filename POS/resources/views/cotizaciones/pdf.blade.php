<!DOCTYPE html>
<html>

<head>
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        /* CONFIGURACIÓN GENERAL (Copiada de Ventas) */
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
        }

        @page {
            margin: 100px 40px 120px 40px;
        }

        /* --- ENCABEZADO --- */
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

        /* --- PIE DE PÁGINA --- */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* --- SECCIÓN DE INFORMACIÓN --- */
        .info-section {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .info-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #444;
            width: 70px;
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
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }

        .tabla-productos th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        /* Anchos de columnas */
        .col-clave { width: 10%; }
        .col-nombre { width: 45%; }
        .col-cant { width: 10%; text-align: center; }
        .col-precio { width: 15%; text-align: right; }
        .col-importe { width: 20%; text-align: right; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

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
            Dirección: Av. 25 ORIENTE &#35;416 local 1. COL.el Carmen<br>
            Teléfono: [555-000-0000] - Puebla, Puebla
        </div>
    </header>

    {{-- PIE DE PÁGINA (Texto específico de cotización) --}}
    <footer>
        <p>
            Este documento es una cotización informativa y no representa una reserva de inventario.<br>
            Los precios están sujetos a cambios sin previo aviso.
        </p>
    </footer>

    {{-- INFORMACIÓN DE LA COTIZACIÓN --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                {{-- Columna Izquierda: Datos Cliente --}}
                <td width="60%">
                    <table class="info-table">
                        <tr>
                            <td class="label">Atención a:</td>
                            {{-- CORRECCIÓN: Usamos cliente_nombre explícitamente para el cliente --}}
                            <td>{{ $cotizacion->cliente_nombre ?? 'Público General' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Email:</td>
                            <td>{{ $cotizacion->cliente_email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>

                {{-- Columna Derecha: Datos Documento --}}
                <td width="40%">
                    <table class="info-table">
                        <tr>
                            <td class="label">Folio:</td>
                            <td style="color: #007bff; font-weight: bold;">#{{ str_pad($cotizacion->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Fecha:</td>
                            <td>{{ $cotizacion->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Vendedor:</td>
                            {{-- El user_id es quien creó el registro (Vendedor) --}}
                            <td>{{ $cotizacion->user->name ?? 'Sistema/Web' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABLA DE PRODUCTOS --}}
    <table class="tabla-productos">
        <thead>
            <tr>
                <th class="col-clave">Clave</th>
                <th class="col-nombre">Nombre / Descripción</th>
                <th class="col-cant">Cant.</th>
                <th class="col-precio">Precio Unit.</th>
                <th class="col-importe">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->detalles as $detalle)
            <tr>
                {{-- Clave --}}
                <td>{{ $detalle->producto->clave ?? $detalle->producto_id }}</td>

                {{-- Nombre --}}
                <td>{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</td>

                {{-- Cantidad --}}
                <td class="text-center">{{ $detalle->cantidad }}</td>

                {{-- Precio --}}
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
            <td style="width: 70%;"></td>
            <td class="total-label">Subtotal:</td>
            <td class="total-value">${{ number_format($cotizacion->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="width: 70%;"></td>
            <td class="total-label">IVA (16%):</td>
            <td class="total-value">${{ number_format($cotizacion->impuesto, 2) }}</td>
        </tr>
        <tr>
            <td style="width: 70%;"></td>
            <td class="total-label" style="padding-top: 5px;">Total:</td>
            <td class="total-value total-final" style="padding-top: 5px;">
                ${{ number_format($cotizacion->total, 2) }}
            </td>
        </tr>
    </table>

</body>

</html>