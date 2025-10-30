<?php
/**
 * Helper para generar recibos HTML
 */

date_default_timezone_set('America/Argentina/Buenos_Aires');

/**
 * Guarda un recibo en formato HTML
 * @param array $datosVenta Datos de la venta
 * @return array Resultado de la operación
 */
function guardarReciboHTML($datosVenta) {
    // Ruta a la carpeta recibos
    $directorioRecibos = dirname(dirname(__DIR__)) . '/recibos';
    
    // Crear directorio principal si no existe
    if (!file_exists($directorioRecibos)) {
        mkdir($directorioRecibos, 0755, true);
    }
    
    // Crear subdirectorios por año/mes
    $anio = date('Y');
    $mes = date('m');
    $directorioMes = $directorioRecibos . '/' . $anio . '/' . $mes;
    
    if (!file_exists($directorioMes)) {
        mkdir($directorioMes, 0755, true);
    }
    
    // Generar contenido HTML
    $html = generarHTMLRecibo($datosVenta);
    
    // Guardar archivo
    $nombreArchivo = 'recibo_' . $datosVenta['numero_venta'] . '.html';
    $rutaCompleta = $directorioMes . '/' . $nombreArchivo;
    
    $resultado = file_put_contents($rutaCompleta, $html);
    
    if ($resultado !== false) {
        return [
            'success' => true,
            'ruta' => $rutaCompleta,
            'nombre_archivo' => $nombreArchivo,
            'url_descarga' => BASE_URL . '/recibos/' . $anio . '/' . $mes . '/' . $nombreArchivo
        ];
    } else {
        return [
            'success' => false,
            'error' => 'No se pudo guardar el archivo'
        ];
    }
}

/**
 * Genera el HTML del recibo
 */
function generarHTMLRecibo($datosVenta) {
    $fecha = date('d/m/Y H:i:s');
    $totalLetras = numeroALetras($datosVenta['total']);
    
    // Datos del cliente
    $clienteNombre = 'Cliente de Mostrador';
    $clienteDoc = 'N/A';
    $clienteDomicilio = 'N/A';
    $datosBancarios = '';
    
    if ($datosVenta['metodo_pago'] === 'Transferencia' && !empty($datosVenta['datos_cliente'])) {
        $clienteNombre = htmlspecialchars($datosVenta['datos_cliente']['nombre']);
        $clienteDoc = htmlspecialchars($datosVenta['datos_cliente']['cbu'] ?? 'N/A');
        $clienteDomicilio = htmlspecialchars($datosVenta['datos_cliente']['alias'] ?? 'N/A');
        
        $datosBancarios = '<div style="margin-top: 10px; padding: 10px; background: #e7f3ff; border-radius: 5px; border: 1px solid #007bff;">
            <p style="margin: 3px 0; font-size: 12px;"><strong>Alias/CVU:</strong> ' . htmlspecialchars($datosVenta['datos_cliente']['alias']) . '</p>';
        
        if (!empty($datosVenta['datos_cliente']['cbu'])) {
            $datosBancarios .= '<p style="margin: 3px 0; font-size: 12px;"><strong>CBU:</strong> ' . htmlspecialchars($datosVenta['datos_cliente']['cbu']) . '</p>';
        }
        $datosBancarios .= '</div>';
    }
    
    // Generar lista de productos
    $productosHTML = '';
    foreach ($datosVenta['productos'] as $item) {
        $nombreProducto = htmlspecialchars($item['nombre'] ?? $item['producto']['Nombre_Producto']);
        $cantidad = $item['cantidad'];
        $precioUnit = number_format($item['precio'] ?? $item['precio_unitario'], 2, ',', '.');
        $subtotalItem = number_format($item['subtotal'], 2, ',', '.');
        
        $productosHTML .= '<div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e9ecef;">
            <div>
                <strong>' . $nombreProducto . '</strong><br>
                <small>' . $cantidad . ' x $' . $precioUnit . '</small>
            </div>
            <span style="font-weight: 600;">$' . $subtotalItem . '</span>
        </div>';
    }
    
    $subtotalFormateado = number_format($datosVenta['subtotal'], 2, ',', '.');
    $ivaFormateado = number_format($datosVenta['iva'], 2, ',', '.');
    $totalFormateado = number_format($datosVenta['total'], 2, ',', '.');
    $metodoPago = strtoupper(str_replace('_', ' ', $datosVenta['metodo_pago']));
    
    $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo - ' . $datosVenta['numero_venta'] . '</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; background: #f5f5f5; }
        .recibo { background: white; padding: 0; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background: #F5A623; padding: 20px; text-align: center; color: #1a1a1a; }
        .seccion { padding: 15px; border-bottom: 1px solid #e9ecef; }
        .seccion-destacada { background: #fff3cd; border: 2px solid #ffc107; }
        .seccion-azul { background: #e7f3ff; border: 2px solid #007bff; }
        .totales { background: #f8f9fa; padding: 15px; }
        .firma { padding: 20px 15px; background: #f8f9fa; border-top: 2px dashed #999; text-align: center; }
        .pie { background: #343a40; color: white; padding: 15px; text-align: center; font-size: 11px; }
        @media print {
            body { background: white; }
            .recibo { box-shadow: none; }
            .no-imprimir { display: none; }
        }
    </style>
</head>
<body>
    <div class="recibo">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">RECIBO OFICIAL</h1>
        </div>
        
        <div class="seccion" style="text-align: center; background: #f8f9fa;">
            <p style="font-size: 18px; font-weight: bold; color: #F5A623; margin: 5px 0;">ALTO VOLTAJE S.R.L.</p>
            <p style="font-size: 12px; margin: 3px 0;">Av. Principal 123, Formosa, Formosa (CP: 3600)</p>
            <p style="font-size: 12px; margin: 3px 0;">CUIT: 30-12345678-9 | Tel: +54 370 123-4567</p>
            <p style="font-size: 12px; margin: 3px 0;">Email: info@altovoltaje.com</p>
        </div>
        
        <div class="seccion seccion-destacada">
            <p style="margin: 5px 0;"><strong>N° RECIBO:</strong> ' . $datosVenta['numero_venta'] . '</p>
            <p style="margin: 5px 0;"><strong>FECHA Y HORA:</strong> ' . $fecha . '</p>
            <p style="margin: 5px 0;"><strong>LUGAR:</strong> Formosa, Formosa, Argentina</p>
        </div>
        
        <div class="seccion">
            <p style="font-weight: bold; margin-bottom: 10px; color: #333;">RECIBÍ DE:</p>
            <div style="margin-left: 10px;">
                <p style="margin: 5px 0;"><strong>Nombre/Razón Social:</strong> ' . $clienteNombre . '</p>
                <p style="margin: 5px 0;"><strong>CUIT/CUIL/DNI:</strong> ' . $clienteDoc . '</p>
                <p style="margin: 5px 0;"><strong>Domicilio:</strong> ' . $clienteDomicilio . '</p>
            </div>
        </div>
        
        <div class="seccion seccion-azul">
            <p style="font-weight: bold; margin-bottom: 10px; color: #007bff;">IMPORTE:</p>
            <div style="margin-left: 10px;">
                <p style="margin: 5px 0; font-size: 16px;"><strong>En números:</strong> $' . $totalFormateado . '</p>
                <p style="margin: 5px 0; font-size: 12px;"><strong>En letras:</strong> <em>' . $totalLetras . '</em></p>
            </div>
        </div>
        
        <div class="seccion">
            <p style="font-weight: bold; margin-bottom: 10px;">CONCEPTO: Venta de productos</p>
            ' . $productosHTML . '
        </div>
        
        <div class="totales">
            <div style="display: flex; justify-content: space-between; margin: 8px 0;">
                <span>Subtotal:</span><span>$' . $subtotalFormateado . '</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0;">
                <span>IVA (21%):</span><span>$' . $ivaFormateado . '</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: #F5A623; padding-top: 12px; border-top: 3px solid #F5A623; margin-top: 12px;">
                <span>TOTAL:</span><span>$' . $totalFormateado . '</span>
            </div>
        </div>
        
        <div class="seccion">
            <p style="margin: 5px 0;"><strong>FORMA DE PAGO:</strong> <span style="color: #28a745; font-weight: bold;">' . $metodoPago . '</span></p>
            ' . $datosBancarios . '
            <p style="margin: 10px 0 5px 0;"><strong>SALDO PENDIENTE:</strong> <span style="color: #28a745; font-weight: bold;">$0.00</span> (Pago completo)</p>
        </div>
        
        <div class="seccion">
            <p style="margin: 0; font-size: 12px;"><strong>Emitido por:</strong> ' . htmlspecialchars($datosVenta['empleado_nombre']) . '</p>
        </div>
        
        <div class="firma">
            <div style="margin-top: 50px; border-top: 2px solid #000; padding-top: 5px; width: 250px; margin-left: auto; margin-right: auto;">
                <p style="font-size: 11px; font-weight: bold; margin: 0;">FIRMA Y ACLARACIÓN</p>
                <p style="font-size: 10px; margin: 0;">Alto Voltaje S.R.L.</p>
            </div>
        </div>
        
        <div class="pie">
            <p style="margin: 5px 0;">Este documento constituye un comprobante de pago válido</p>
            <p style="margin: 5px 0;">Original para el cliente - Duplicado para archivo</p>
            <p style="margin: 5px 0; font-style: italic;">Gracias por su compra</p>
        </div>
        
        <div class="seccion no-imprimir" style="text-align: center; background: #f8f9fa;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">Imprimir Recibo</button>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

/**
 * Convierte un número a letras
 */
function numeroALetras($numero) {
    $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    $especiales = ['', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
    $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
    
    $convertirGrupo = function($num) use ($unidades, $decenas, $especiales, $centenas) {
        if ($num == 0) return 'CERO';
        if ($num == 100) return 'CIEN';
        $texto = '';
        $c = floor($num / 100);
        if ($c > 0) { $texto .= $centenas[$c] . ' '; $num %= 100; }
        if ($num >= 11 && $num <= 19) return trim($texto . $especiales[$num - 10]);
        $d = floor($num / 10);
        if ($d > 0) {
            $texto .= ($d == 2 && $num % 10 > 0) ? 'VEINTI' : $decenas[$d];
            if ($num % 10 > 0 && $d > 2) $texto .= ' Y ';
            $num %= 10;
        }
        if ($num > 0) $texto .= $unidades[$num];
        return trim($texto);
    };
    
    $partes = number_format($numero, 2, '.', '');
    list($entero, $decimales) = explode('.', $partes);
    $entero = intval($entero);
    $resultado = '';
    
    if ($entero >= 1000000) {
        $millones = floor($entero / 1000000);
        $resultado .= ($millones == 1 ? 'UN MILLÓN ' : $convertirGrupo($millones) . ' MILLONES ');
        $entero %= 1000000;
    }
    if ($entero >= 1000) {
        $miles = floor($entero / 1000);
        $resultado .= ($miles == 1 ? 'MIL ' : $convertirGrupo($miles) . ' MIL ');
        $entero %= 1000;
    }
    if ($entero > 0) $resultado .= $convertirGrupo($entero);
    if (empty(trim($resultado))) $resultado = 'CERO';
    
    return trim($resultado) . ' CON ' . $decimales . '/100 PESOS';
}
?>