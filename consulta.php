<?php
require "cnx.php";

$rangof = $_POST['rangof'];
$estado = $_POST['estado'];
$categoria = $_POST['categoria'];
$subcategoria = $_POST['subcategoria'];
$usuario = $_POST['usuario'];
$plaza = $_POST['plaza'];
$medio = $_POST['medio'];
$area = $_POST['area'];

$sql_filtro_subcategoria = "select sub.id_subcategoria,sub.nombre from ticket as t
inner join subcategoria as sub on t.id_subcategoria=sub.id_subcategoria

";

// Construir la parte común de la consulta SQL para las categorías
$sql_categoria_base = "SELECT c.nombre, COUNT(t.id_ticket) AS cantidad 
                       FROM [dbo].[ticket] AS t
                       INNER JOIN categoria AS c ON t.id_categoria = c.id_categoria
                       WHERE t.estatus <> 1";

$sql_subcategoria_base = "SELECT sub.nombre, COUNT(t.id_ticket) AS cantidad 
                       FROM [dbo].[ticket] AS t
                       INNER JOIN subcategoria AS sub ON t.id_subcategoria = sub.id_subcategoria
                       WHERE t.estatus <> 1";

$sql_estado_base = "SELECT te.nombre, COUNT(t.id_ticket) AS cantidad 
FROM [dbo].[ticket] AS t
INNER JOIN tipo_estado AS te ON t.id_tipo_estado = te.id_tipo_estado
WHERE t.estatus <> 1";


// Construir la consulta SQL para las categorías
$sql_categoria = $sql_categoria_base;
$sql_subcategoria = $sql_subcategoria_base;
$sql_estado = $sql_estado_base;
$sql_count = "SELECT COUNT(id_ticket) as cantidad 
              FROM [dbo].[ticket] as t
              WHERE estatus <> 1";
if (!empty($estado)) {
    $sql_categoria .= " AND t.id_tipo_estado = '$estado'";
    $sql_subcategoria .= " AND t.id_tipo_estado = '$estado'";
    $sql_estado .= " AND t.id_tipo_estado = '$estado'";
    $sql_count .= " AND t.id_tipo_estado = '$estado'";
}
if (!empty($medio)) {
    $sql_categoria .= " AND t.id_tipo_medio = '$medio'";
    $sql_subcategoria .= " AND t.id_tipo_medio = '$medio'";
    $sql_estado .= " AND t.id_tipo_medio = '$medio'";
    $sql_count .= " AND t.id_tipo_medio = '$medio'";
}
if (!empty($area)) {
    if ($area == 'vacio') {
        $sql_categoria .= " AND t.areaCategoria = ''";
        $sql_subcategoria .= " AND t.areaCategoria = ''";
        $sql_estado .= " AND t.areaCategoria = ''";
        $sql_count .= " AND t.areaCategoria = ''";
    } else {
        $sql_categoria .= " AND t.areaCategoria = '$area'";
        $sql_subcategoria .= " AND t.areaCategoria = '$area'";
        $sql_estado .= " AND t.areaCategoria = '$area'";
        $sql_count .= " AND t.areaCategoria = '$area'";
    }
}
if (!empty($usuario)) {
    $sql_categoria .= " AND t.id_usuario = '$usuario'";
    $sql_subcategoria .= " AND t.id_usuario = '$usuario'";
    $sql_estado .= " AND t.id_usuario = '$usuario'";
    $sql_count .= " AND t.id_usuario = '$usuario'";
}
if (!empty($plaza)) {
    $sql_categoria .= " AND t.id_plaza = '$plaza'";
    $sql_subcategoria .= " AND t.id_plaza = '$plaza'";
    $sql_estado .= " AND t.id_plaza = '$plaza'";
    $sql_count .= " AND t.id_plaza = '$plaza'";
}
if (!empty($subcategoria)) {
    $sql_categoria .= " AND t.id_subcategoria = '$subcategoria'";
    $sql_subcategoria .= " AND t.id_subcategoria = '$subcategoria'";
    $sql_estado .= " AND t.id_subcategoria = '$subcategoria'";
    $sql_count .= " AND t.id_subcategoria = '$subcategoria'";
}
if (!empty($categoria)) {
    $sql_categoria .= " AND t.id_categoria = '$categoria'";
    $sql_subcategoria .= " AND t.id_categoria = '$categoria'";
    $sql_estado .= " AND t.id_categoria = '$categoria'";
    $sql_count .= " AND t.id_categoria = '$categoria'";
    $sql_filtro_subcategoria .= " WHERE t.id_categoria = '$categoria'";
}
if (!empty($rangof)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rangof);
    if ($fechaInicio === $fechaFin) {
        $sql_categoria .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
        $sql_subcategoria .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
        $sql_estado .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
        $sql_count .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
    } else {
        $sql_categoria .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
        $sql_subcategoria .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
        $sql_estado .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
        $sql_count .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
    }
}
$sql_categoria .= " GROUP BY c.nombre";
$sql_subcategoria .= " GROUP BY sub.nombre order by cantidad desc";
$sql_estado .= " GROUP BY te.nombre";
$sql_filtro_subcategoria .= " group by sub.id_subcategoria,sub.nombre";

// Ejecutar las consultas
$resultado_categoria = sqlsrv_query($cnx, $sql_categoria);
$resultado_subcategoria = sqlsrv_query($cnx, $sql_subcategoria);
$resultado_estado = sqlsrv_query($cnx, $sql_estado);
$resultado_count = sqlsrv_query($cnx, $sql_count);
$resultado_filtro_subcategoria = sqlsrv_query($cnx, $sql_filtro_subcategoria);

// Procesar los resultados
$categorias = [];
$cantidades = [];
$subcategorias = [];
$c_subcategorias = [];
$estados = [];
$c_estados = [];
$filtro_subcategorias = [];
$array_count = sqlsrv_fetch_array($resultado_count);
$count = $array_count['cantidad'];

while ($categoria = sqlsrv_fetch_array($resultado_categoria)) {
    $categorias[] = $categoria['nombre'];
    $cantidades[] = $categoria['cantidad'];
}
while ($filtro_sub = sqlsrv_fetch_array($resultado_filtro_subcategoria)) {
    $filtro_subcategorias[] = ['id' => $filtro_sub['id_subcategoria'], 'nombre' => utf8_encode($filtro_sub['nombre'])];
}
while ($subcategoria = sqlsrv_fetch_array($resultado_subcategoria)) {
    $subcategorias[] = utf8_encode($subcategoria['nombre']); // o utf8_decode
    $c_subcategorias[] = $subcategoria['cantidad'];
}

while ($array_estado = sqlsrv_fetch_array($resultado_estado)) {
    $estados[] = $array_estado['nombre'];
    $c_estados[] = $array_estado['cantidad'];
}



function generarColorAleatorio()
{
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

// Construir el arreglo de colores aleatorios para las barras
$colores_e = [];
$colores_c = [];
$colores_sub = [];
for ($i = 0; $i < count($estados); $i++) {
    $colores_e[] = generarColorAleatorio();
}
for ($i = 0; $i < count($categorias); $i++) {
    $colores_c[] = generarColorAleatorio();
}
for ($i = 0; $i < count($subcategorias); $i++) {
    $colores_sub[] = generarColorAleatorio();
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
    'estados' => [
        'labels' => $estados,
        'datasets' => [
            [
                'label' => "Cantidad",
                'data' => $c_estados,
                'backgroundColor' => $colores_e,
            ]
        ]
    ],
    'subcategorias' => [
        'labels' => $subcategorias,
        'datasets' => [
            [
                'label' => "Cantidad",
                'data' => $c_subcategorias,
                'backgroundColor' => $colores_sub,
            ]
        ]
    ],
    'count' => $count,
    'filtro_subcategoria' => $filtro_subcategorias,
    'rangof' => $rangof
];

// Devolver los datos como JSON
echo json_encode($data);
