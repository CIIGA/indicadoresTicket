<?php

require "cnx.php";
$rangof = $_GET['rangof'];

// Construir la parte común de la consulta SQL para las categorías
$sql_ticket_base = "SELECT 
ROW_NUMBER() OVER (ORDER BY t.fecha_captura DESC) AS numero_fila,
c.nombre AS c_nombre, 
s.nombre AS s_nombre,
u.nombre AS u_nombre, 
a.nombre AS a_nombre,
e.nombre AS e_nombre, 
p.nombre AS p_nombre,
tm.nombre AS tm_nombre, 
CASE 
    WHEN CONVERT(DATE, t.fecha_pendiente) = '1999-09-09' THEN NULL 
    ELSE CONVERT(DATE, t.fecha_pendiente) 
END AS fecha_pendiente,
CASE 
    WHEN CONVERT(DATE, t.fecha_en_proceso) = '1999-09-09' THEN NULL 
    ELSE CONVERT(DATE, t.fecha_en_proceso) 
END AS fecha_en_proceso,
CASE 
    WHEN CONVERT(DATE, t.fecha_validando) = '1999-09-09' THEN NULL 
    ELSE CONVERT(DATE, t.fecha_validando) 
END AS fecha_validando,
CASE 
    WHEN CONVERT(DATE, t.fecha_finalizado) = '1999-09-09' THEN NULL 
    ELSE CONVERT(DATE, t.fecha_finalizado) 
END AS fecha_finalizado,
t.descripcion,
CASE 
    WHEN CONVERT(DATE, t.fecha_captura) = '1999-09-09' THEN NULL 
    ELSE CONVERT(DATE, t.fecha_captura) 
END AS fecha_captura,
t.solucion
FROM 
ticket AS t 
INNER JOIN 
categoria AS c ON t.id_categoria = c.id_categoria
INNER JOIN 
subcategoria AS s ON t.id_subcategoria = s.id_subcategoria
INNER JOIN 
usuario AS u ON t.id_usuario = u.id_usuario 
INNER JOIN 
asesor AS a ON t.id_asesor = a.id_asesor
INNER JOIN 
tipo_estado AS e ON t.id_tipo_estado = e.id_tipo_estado
INNER JOIN 
prioridad AS p ON t.id_prioridad = p.id_prioridad
INNER JOIN 
tipo_medio AS tm ON t.id_tipo_medio = tm.id_tipo_medio
WHERE 
t.estatus <> 1";

$sql_ticket = $sql_ticket_base;
if (!empty($rangof)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rangof);
    if ($fechaInicio === $fechaFin) {
        $sql_ticket .= " AND CONVERT(date, t.fecha_captura) = '$fechaInicio'";
    } else {
        $sql_ticket .= " AND CONVERT(date, t.fecha_captura) BETWEEN '$fechaInicio' AND '$fechaFin'";
    }
}
$sql_ticket .= " order by t.fecha_captura desc";

$resultado_ticket = sqlsrv_query($cnx, $sql_ticket);
$hasRows = sqlsrv_has_rows($resultado_ticket);


header('Set-Cookie: fileDownload=true; path=/');
header('Cache-Control: max-age=60, must-revalidate');
header("Pragma: public");
header("Expires: 0");
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=Reporte_Tickets $rangof.xls");
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        th,
        td {

            
            border: 1px solid #707070;
            border-spacing: 0;
        }

        
    </style>
</head>

<body>

    <table class="" style="font-size: 11px; ">
        <thead>
            <tr>
                <th class="bg_th">Fila</th>
                <th class="bg_th">Categoria</th>
                <th class="bg_th">subcategoria</th>
                <th class="bg_th">Usuario</th>
                <th class="bg_th">Asesor</th>
                <th class="bg_th">Estado</th>
                <th class="bg_th">Prioridad</th>
                <th class="bg_th">Medio</th>
                <th class="bg_th">Fecha Pendiente</th>
                <th class="bg_th">Fecha en proceso</th>
                <th class="bg_th">Fecha Validando</th>
                <th class="bg_th">Fecha Finalizado</th>
                <th class="bg_th">Descripcion</th>
                <th class="bg_th">Fecha Captura</th>
                <th class="bg_th">Solución</th>

            </tr>
        </thead>
        <tbody>
            <?php
            if ($hasRows) {
                while ($ticket = sqlsrv_fetch_array($resultado_ticket, SQLSRV_FETCH_ASSOC)) {
            ?>
                    <tr>
                        <td><?= utf8_encode($ticket['numero_fila']) ?></td>
                        <td><?= utf8_encode($ticket['c_nombre']) ?></td>
                        <td><?= utf8_encode($ticket['s_nombre']) ?></td>
                        <td><?= utf8_encode($ticket['u_nombre']) ?></td>
                        <td><?= utf8_encode($ticket['a_nombre']) ?></td>
                        <td><?= utf8_encode($ticket['e_nombre']) ?></td>
                        <td><?= utf8_encode($ticket['p_nombre']) ?></td>
                        <td><?= utf8_encode($ticket['tm_nombre']) ?></td>
                        <td><?= ($ticket['fecha_pendiente']) ? date('d-m-Y', strtotime($ticket['fecha_pendiente']->format('Y-m-d'))) : "" ?></td>
                        <td><?= ($ticket['fecha_en_proceso']) ? date('d-m-Y', strtotime($ticket['fecha_en_proceso']->format('Y-m-d'))) : "" ?></td>
                        <td><?= ($ticket['fecha_validando']) ? date('d-m-Y', strtotime($ticket['fecha_validando']->format('Y-m-d'))) : "" ?></td>
                        <td><?= ($ticket['fecha_finalizado']) ? date('d-m-Y', strtotime($ticket['fecha_finalizado']->format('Y-m-d'))) : "" ?></td>
                        <td><?= utf8_encode($ticket['descripcion']) ?></td>
                        <td><?= ($ticket['fecha_captura']) ? date('d-m-Y', strtotime($ticket['fecha_captura']->format('Y-m-d'))) : "" ?></td>
                        <td><?= utf8_encode($ticket['solucion']) ?></td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="15">No hay información</td>
                </tr>
            <?php
            }
            ?>

        </tbody>
    </table>

</body>

</html>