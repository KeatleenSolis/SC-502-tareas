<?php
/*
 * Práctica Programada #3 - PHP
 * SC-502 Ambiente Web Cliente/Servidor
 *
 * Sistema que registra transacciones de una tarjeta de crédito
 * y genera un estado de cuenta en pantalla y en estado_cuenta.txt
 */

// ------------------------------------------------------------
// 1. Constantes con los porcentajes que pide el enunciado
// ------------------------------------------------------------
const TASA_INTERES = 0.026;  // 2.6 %
const TASA_CASHBACK = 0.001; // 0.1 %

// Zona horaria de Costa Rica para que la fecha salga correcta
date_default_timezone_set("America/Costa_Rica");

// ------------------------------------------------------------
// 2. Arreglo donde se guardan las transacciones
// ------------------------------------------------------------
$transacciones = [];

// ------------------------------------------------------------
// Función de ayuda: rellena un texto con espacios hasta cierto largo.
// (str_pad cuenta mal las tildes y la ñ, por eso usamos mb_strlen)
// ------------------------------------------------------------
function rellenar($texto, $largo)
{
    return $texto . str_repeat(" ", max(0, $largo - mb_strlen($texto)));
}

// ------------------------------------------------------------
// 3. Función para registrar una transacción
// ------------------------------------------------------------
function registrarTransaccion($id, $descripcion, $monto)
{
    global $transacciones; // usamos el arreglo que está fuera de la función

    $nuevaTransaccion = [
        "id"          => $id,
        "descripcion" => $descripcion,
        "monto"       => $monto
    ];

    array_push($transacciones, $nuevaTransaccion);
}

// ------------------------------------------------------------
// 4. Función para generar el estado de cuenta
// ------------------------------------------------------------
function generarEstadoDeCuenta()
{
    global $transacciones;

    // 4.1 Sumar el monto de contado
    $montoContado = 0;
    foreach ($transacciones as $transaccion) {
        $montoContado += $transaccion["monto"];
    }

    // 4.2 Cálculos
    $montoConInteres = $montoContado * (1 + TASA_INTERES);
    $cashBack        = $montoContado * TASA_CASHBACK;      // sobre el contado, antes del interés
    $montoFinal      = $montoConInteres - $cashBack;

    // 4.3 Mostrar en pantalla (HTML)
    echo "<h1>Estado de Cuenta - Tarjeta de Crédito</h1>";
    echo "<p>Fecha de generación: " . date("d/m/Y H:i") . "</p>";

    echo "<table>";
    echo "<tr><th>ID</th><th>Descripción</th><th>Monto</th></tr>";
    foreach ($transacciones as $transaccion) {
        echo "<tr>";
        echo "<td>" . $transaccion["id"] . "</td>";
        echo "<td>" . htmlspecialchars($transaccion["descripcion"]) . "</td>";
        echo "<td class='monto'>₡" . number_format($transaccion["monto"], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<table class='resumen'>";
    echo "<tr><td>Monto total de contado:</td><td class='monto'>₡" . number_format($montoContado, 2) . "</td></tr>";
    echo "<tr><td>Monto total con interés (2.6%):</td><td class='monto'>₡" . number_format($montoConInteres, 2) . "</td></tr>";
    echo "<tr><td>Cash back (0.1%):</td><td class='monto'>-₡" . number_format($cashBack, 2) . "</td></tr>";
    echo "<tr class='final'><td>Monto final a pagar:</td><td class='monto'>₡" . number_format($montoFinal, 2) . "</td></tr>";
    echo "</table>";

    // 4.4 Generar el archivo estado_cuenta.txt con los mismos datos
    $linea = str_repeat("=", 60) . "\n";
    $texto  = "ESTADO DE CUENTA - TARJETA DE CRÉDITO\n";
    $texto .= "Fecha de generación: " . date("d/m/Y H:i") . "\n";
    $texto .= $linea;
    $texto .= rellenar("ID", 6) . rellenar("Descripción", 34) . "Monto\n";
    $texto .= str_repeat("-", 60) . "\n";

    foreach ($transacciones as $transaccion) {
        $texto .= rellenar($transaccion["id"], 6)
                . rellenar($transaccion["descripcion"], 34)
                . "₡" . number_format($transaccion["monto"], 2) . "\n";
    }

    $texto .= $linea;
    $texto .= rellenar("Monto total de contado:", 40)         . "₡"  . number_format($montoContado, 2) . "\n";
    $texto .= rellenar("Monto total con interés (2.6%):", 40) . "₡"  . number_format($montoConInteres, 2) . "\n";
    $texto .= rellenar("Cash back (0.1%):", 40)               . "-₡" . number_format($cashBack, 2) . "\n";
    $texto .= rellenar("Monto final a pagar:", 40)            . "₡"  . number_format($montoFinal, 2) . "\n";

    // __DIR__ = la carpeta donde está este archivo index.php
    file_put_contents(__DIR__ . "/estado_cuenta.txt", $texto);

    echo "<p class='aviso'>✔ Se generó el archivo <strong>estado_cuenta.txt</strong> en la carpeta del proyecto.</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Práctica Programada 3 - Estado de Cuenta</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; color: #222; }
        h1 { color: #1a3d6d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { padding: 8px 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #1a3d6d; color: white; }
        .monto { text-align: right; }
        .resumen td:first-child { font-weight: bold; }
        .final td { font-size: 1.15em; font-weight: bold; background: #eef3fa; }
        .aviso { color: #2e7d32; }
    </style>
</head>
<body>
<?php
// ------------------------------------------------------------
// 5. Simulación: registrar varias transacciones
// ------------------------------------------------------------
registrarTransaccion(1, "Super cartago", 45000);
registrarTransaccion(2, "Bomba Uno", 30000);
registrarTransaccion(3, "La casona", 18500);
registrarTransaccion(4, "La bomba", 12750);
registrarTransaccion(5, "Suscripción Spotify", 6500);

// ------------------------------------------------------------
// 6. Generar y mostrar el estado de cuenta
// ------------------------------------------------------------
generarEstadoDeCuenta();
?>
</body>
</html>