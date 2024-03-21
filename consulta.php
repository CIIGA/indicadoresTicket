<?php
require "cnx.php";

$rangof = $_POST['rangof'];
$asesor = $_POST['asesor'];
$categoria = $_POST['categoria'];

// Construir la parte común de la consulta SQL para las categorías
$sql_categoria_base = "SELECT c.nombre, COUNT(t.id_ticket) AS cantidad 
                       FROM [dbo].[ticket] AS t
                       INNER JOIN categoria AS c ON t.id_categoria = c.id_categoria
                       WHERE t.estatus <> 1";

$sql_asesor_base = "SELECT a.nombre, COUNT(t.id_ticket) AS cantidad 
FROM [dbo].[ticket] AS t
INNER JOIN asesor AS a ON t.id_asesor = a.id_asesor
WHERE t.estatus <> 1";

// Construir la parte común de la consulta SQL para los estados
$sql_estados_base = "SELECT te.nombre as tipo_estado, COUNT(t.id_ticket) as cantidad 
                     FROM [dbo].[ticket] AS t
                     INNER JOIN tipo_estado AS te ON t.id_tipo_estado = te.id_tipo_estado
                     WHERE t.estatus <> 1";

// Construir la consulta SQL para las categorías
$sql_categoria = $sql_categoria_base;
if (!empty($asesor)) {
    $sql_categoria .= " AND t.id_asesor = '$asesor'";
}
if (!empty($categoria)) {
    $sql_categoria .= " AND t.id_categoria = '$categoria'";
}
if (!empty($rangof)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rangof);
    if ($fechaInicio === $fechaFin) {
        $sql_categoria .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
    } else {
        $sql_categoria .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
    }
}
$sql_categoria .= " GROUP BY c.nombre";

// Construir la consulta SQL para los asesores
$sql_asesor = $sql_asesor_base;
if (!empty($asesor)) {
    $sql_asesor .= " AND t.id_asesor = '$asesor'";
}
if (!empty($categoria)) {
    $sql_asesor .= " AND t.id_categoria = '$categoria'";
}
if (!empty($rangof)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rangof);
    if ($fechaInicio === $fechaFin) {
        $sql_asesor .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
    } else {
        $sql_asesor .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
    }
}
$sql_asesor .= " GROUP BY a.nombre";

// Construir la consulta SQL para el conteo total
$sql_count = "SELECT COUNT(id_ticket) as cantidad 
              FROM [dbo].[ticket] as t
              WHERE estatus <> 1";
if (!empty($asesor)) {
    $sql_count .= " AND t.id_asesor = '$asesor'";
}
if (!empty($categoria)) {
    $sql_count .= " AND t.id_categoria = '$categoria'";
}
if (!empty($rangof)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rangof);
    if ($fechaInicio === $fechaFin) {
        $sql_count .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
    } else {
        $sql_count .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
    }
}

// Construir la consulta SQL para los estados
$sql_estados = $sql_estados_base;
if (!empty($asesor)) {
    $sql_estados .= " AND t.id_asesor = '$asesor'";
}
if (!empty($categoria)) {
    $sql_estados .= " AND t.id_categoria = '$categoria'";
}
if (!empty($rangof)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rangof);
    if ($fechaInicio === $fechaFin) {
        $sql_estados .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
    } else {
        $sql_estados .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
    }
}
$sql_estados .= " GROUP BY te.nombre";

// Ejecutar las consultas
$resultado_categoria = sqlsrv_query($cnx, $sql_categoria);
$resultado_asesor = sqlsrv_query($cnx, $sql_asesor);
$resultado_count = sqlsrv_query($cnx, $sql_count);
$resultado_estados = sqlsrv_query($cnx, $sql_estados);

// Procesar los resultados
$categorias = [];
$cantidades = [];
$asesores = [];
$c_asesores = [];
$estados = [];
$array_count = sqlsrv_fetch_array($resultado_count);
$count = $array_count['cantidad'];

while ($categoria = sqlsrv_fetch_array($resultado_categoria)) {
    $categorias[] = $categoria['nombre'];
    $cantidades[] = $categoria['cantidad'];
}

while ($array_asesor = sqlsrv_fetch_array($resultado_asesor)) {
    $asesores[] = $array_asesor['nombre'];
    $c_asesores[] = $array_asesor['cantidad'];
}

while ($estado = sqlsrv_fetch_array($resultado_estados)) {
    $estados[] = ['nombre' => $estado['tipo_estado'], 'cantidad' => $estado['cantidad']];
}

function generarColorAleatorio() {
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

// Construir el arreglo de colores aleatorios para las barras
$colores_a = [];
$colores_c = [];
for ($i = 0; $i < count($asesores); $i++) {
    $colores_a[] = generarColorAleatorio();
}
for ($i = 0; $i < count($categorias); $i++) {
    $colores_c[] = generarColorAleatorio();
}
// Construir el arreglo de datos en formato JSON
$data = [
    'categorias' => [
        'labels' => $categorias,
        'datasets' => [
            [
                'data' => $cantidades,
                'backgroundColor' => $colores_c,
            ]
        ]
    ],
    'asesores' => [
        'labels' => $asesores,
        'datasets' => [
            [
                'label' => "Cantidad",
                'data' => $c_asesores,
                'backgroundColor' => $colores_a,
            ]
        ]
    ],
    'estados' => $estados,
    'count' => $count,
    'rangof' => $rangof
];

// Devolver los datos como JSON
echo json_encode($data);
