<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->serie }}-{{ $factura->numero }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #1976d2;
            padding-bottom: 20px;
        }
        .empresa-info h1 {
            color: #1976d2;
            margin-bottom: 5px;
        }
        .empresa-info p {
            font-size: 12px;
            color: #666;
        }
        .factura-numero {
            text-align: right;
        }
        .factura-numero h2 {
            color: #1976d2;
            font-size: 24px;
        }
        .factura-numero p {
            font-size: 12px;
            color: #666;
        }
        .cliente-info {
            margin-bottom: 30px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .cliente-info h3 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        .cliente-info p {
            font-size: 14px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th {
            background: #1976d2;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .totales {
            text-align: right;
            margin-bottom: 30px;
        }
        .totales-row {
            display: flex;
            justify-content: flex-end;
            margin: 5px 0;
            font-size: 12px;
        }
        .totales-row label {
            width: 100px;
            text-align: right;
            margin-right: 20px;
            font-weight: bold;
        }
        .totales-row .value {
            width: 80px;
            text-align: right;
        }
        .total-final {
            border-top: 2px solid #1976d2;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #1976d2;
        }
        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .estado.pagada {
            background: #4caf50;
            color: white;
        }
        .estado.pendiente {
            background: #ff9800;
            color: white;
        }
        .estado.anulada {
            background: #f44336;
            color: white;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="empresa-info">
                <h1>{{ $factura->empresa->nombre }}</h1>
                <p><strong>RUC:</strong> {{ $factura->empresa->ruc }}</p>
                <p><strong>Dirección:</strong> {{ $factura->empresa->direccion }}</p>
                <p><strong>Teléfono:</strong> {{ $factura->empresa->telefono }}</p>
                <p><strong>Email:</strong> {{ $factura->empresa->email }}</p>
            </div>
            <div class="factura-numero">
                <h2>{{ $factura->serie }}-{{ $factura->numero }}</h2>
                <p><strong>Fecha:</strong> {{ $factura->fecha->format('d/m/Y') }}</p>
                <p>
                    <span class="estado {{ $factura->estado }}">
                        {{ strtoupper($factura->estado) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Cliente -->
        <div class="cliente-info">
            <h3>Cliente</h3>
            <p><strong>Nombre:</strong> {{ $factura->cliente_nombre }}</p>
            <p><strong>Documento:</strong> {{ $factura->cliente_documento }}</p>
            <p><strong>Email:</strong> {{ $factura->cliente_email ?? 'N/A' }}</p>
        </div>

        <!-- Items -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Descripción</th>
                    <th style="width: 12%;">Cantidad</th>
                    <th style="width: 15%;">Precio Unitario</th>
                    <th style="width: 15%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factura->items as $item)
                    <tr>
                        <td>{{ $item->descripcion }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>S/. {{ number_format($item->precio_unitario, 2) }}</td>
                        <td>S/. {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totales">
            <div class="totales-row">
                <label>Subtotal:</label>
                <div class="value">S/. {{ number_format($factura->subtotal, 2) }}</div>
            </div>
            <div class="totales-row">
                <label>IGV (18%):</label>
                <div class="value">S/. {{ number_format($factura->impuesto, 2) }}</div>
            </div>
            <div class="totales-row total-final">
                <label>Total:</label>
                <div class="value">S/. {{ number_format($factura->total, 2) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Documento generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>