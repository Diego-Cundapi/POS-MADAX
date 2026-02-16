<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $id }}</title>
    <style>
        /* RESET Y CONFIGURACIÓN BÁSICA */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif; /* Arial es más limpia para térmicas que Courier */
            font-size: 11px; /* Ajuste leve de tamaño */
            background-color: #fff;
            color: #000;
            width: 80mm; 
            margin: auto;
            padding: 2mm 2mm 5mm 2mm;
        }

        /* Ocultar elementos innecesarios al imprimir */
        @media print {
            body { 
                margin: 0;
                /* Padding derecho vital para evitar cortes en impresoras térmicas */
                padding-right: 5mm; 
            }
            @page {
                margin: 0;
                size: auto;
            }
        }

        /* CLASES UTILITARIAS */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        
        .double-divider {
            border-top: 2px dashed #000;
            margin: 5px 0;
        }

        /* ENCABEZADO */
        .header {
            margin-bottom: 5px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 10px;
            line-height: 1.2;
        }

        /* TÍTULO DOC */
        .doc-title {
            margin: 8px 0;
            font-size: 14px;
        }

        /* TABLA DE PRODUCTOS */
        .table-products {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .table-products th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        
        .table-products td {
            vertical-align: top;
            padding: 2px 0;
        }

        /* TOTALES */
        .totals {
            margin-top: 5px;
            width: 100%;
        }
        .totals td {
            padding: 2px 0;
        }
        .total-final {
            font-size: 14px;
            border-top: 1px solid #000;
        }

        /* PIE DE PÁGINA */
        .footer {
            margin-top: 10px;
            font-size: 10px;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 1000);">

    <div class="header text-center">
        <h1 class="uppercase">Refaccionaria Madax</h1>
        <p>Av. 25 ORIENTE #416 local 1.</p>
        <p>COL. El Carmen, Puebla, Pue.</p>
        <p>Tel: 555-000-0000</p>
    </div>

    <div class="double-divider"></div>

    <div class="info-venta">
        <p><strong>Folio:</strong> {{ str_pad($id, 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Fecha:</strong> {{ $fecha ?? date('d/m/Y H:i A') }}</p>
        <p><strong>Cliente:</strong> {{ Str::limit($cliente ?? 'Público General', 25) }}</p>
        @if(isset($vendedor))
        <p><strong>Atendió:</strong> {{ Str::limit($vendedor, 20) }}</p>
        @endif
    </div>

    <div class="divider"></div>

    <div class="text-center font-bold doc-title">
        {{ $tipo == 'cotizacion' ? '*** COTIZACIÓN ***' : '*** NOTA DE VENTA ***' }}
    </div>

    <table class="table-products">
        <thead>
            <tr>
                <th style="width: 10%;">Cant</th>
                <th style="width: 55%;">Descripción</th>
                <th style="width: 15%;">Precio</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $item)
            <tr>
                <td class="text-center">{{ $item['cantidad'] }}</td>
                <td>{{ Str::limit($item['descripcion'], 35) }}</td>
                <td class="text-left">${{ number_format($item['precio'], 2) }}</td>
                <td class="text-right">${{ number_format($item['importe'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals">
        <tr>
            <td class="text-right font-bold" style="width: 60%;">SUBTOTAL:</td>
            <td class="text-right" style="width: 40%;">${{ number_format($subtotal, 2) }}</td>
        </tr>
        @if($impuesto > 0)
        <tr>
            <td class="text-right font-bold">IVA:</td>
            <td class="text-right">${{ number_format($impuesto, 2) }}</td>
        </tr>
        @endif
        @if(isset($descuento) && $descuento > 0)
        <tr>
            <td class="text-right font-bold">DESCUENTO:</td>
            <td class="text-right">- ${{ number_format($descuento, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td class="text-right font-bold total-final" style="padding-top: 5px;">TOTAL:</td>
            <td class="text-right font-bold total-final" style="padding-top: 5px;">${{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <div class="footer text-center">
        <p>{{ $tipo == 'cotizacion' ? 'Precios sujetos a cambio sin previo aviso.' : '¡Gracias por su preferencia!' }}</p>
        <p class="uppercase" style="margin-top: 5px;">*** REVISE SU MERCANCÍA ***</p>
    </div>

</body>
</html>
