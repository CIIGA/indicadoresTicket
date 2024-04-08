<?php
require "cnx.php";
$sql_asesor = sqlsrv_query($cnx, "select a.id_asesor,a.nombre from ticket as t
inner join asesor as a on t.id_asesor=a.id_asesor
group by a.id_asesor,a.nombre
");
$sql_categoria = sqlsrv_query($cnx, "select c.id_categoria,c.nombre from ticket as t
inner join categoria as c on t.id_categoria=c.id_categoria
group by c.id_categoria,c.nombre
");


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Tickets</title>
    <link rel="icon" href="img/implementtaIcon.png">
    <!-- Theme style -->
    <link rel="stylesheet" href="plugins/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" id="theme-styles">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        /* Estilos para el icono */
        .filterIcon {
            transition: transform 0.3s ease;
            /* Transición suave */
        }

        /* Estilos para el icono cuando el mouse está encima */
        .filterIcon:hover {
            transform: scale(1.1);
            /* Escalar ligeramente el icono al pasar el mouse sobre él */
        }
    </style>
</head>


<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a href="#"><img src="img/logoImplementtaHorizontal.png" width="250" height="82" alt=""></a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto gap-2">
                <a class="nav-item nav-link" href="#"> Inicio
                </a>
                <a class="nav-item nav-link" href="#">
                    Salir <i class="fa-solid fa-right-from-bracket"></i></a>
            </ul>
        </div>
    </nav>
    <!-- Content Wrapper. Contains page content -->
    <div class="container col-md-11">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Filters Section -->
                <div class="row mb-3">
                    <div class="col-md-12 offset-md-1">
                        <div class="form-inline ">
                            <div class="form-group mr-3">
                                <label for="categoria" class="d-block">Categoria:</label>
                                <select class="form-control" id="categoria">
                                    <option value="">--Seleccione una opción--</option>
                                    <?php while ($categoria = sqlsrv_fetch_array($sql_categoria)) { ?>
                                        <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group mr-3">
                                <label for="asesor" class="d-block">Asesor:</label>
                                <select class="form-control" id="asesor">
                                    <option value="">--Seleccione una opción--</option>
                                    <?php while ($asesor = sqlsrv_fetch_array($sql_asesor)) { ?>
                                        <option value="<?= $asesor['id_asesor'] ?>"><?= $asesor['nombre'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group mr-3">
                                <label for="rangof" class="d-block">Fechas:</label>
                                <input type="text" class="form-control" id="rangof" value="" />
                            </div>
                            <div class="form-group mr-3">
                                <button class="btn btn-primary" id="reporte">Reporte</button>
                            </div>
                            <div class="form-group mr-3">
                                <a id="filterIconLink" title="Resetear filtros"><img class="filterIcon" width="48" height="48" src="https://img.icons8.com/fluency/48/filter--v2.png" alt="filter--v2" /></a>
                            </div>
                        </div>
                    </div>
                </div>




                <!-- /.row -->
                <div class="row">
                    <div class="col-md-5">
                        <!-- PIE CHART -->
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">Tickets por area</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-5">
                        <!-- PIE CHART -->
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Tickets por asesor</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="asesorChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-2">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fa-solid fa-ticket"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tickets</span>
                                <span class="info-box-number" id="tickets">

                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1"><i class="fa-solid fa-check-double"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Finalizado</span>
                                <span class="info-box-number" id="finalizado">

                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="info-box">
                            <span class="info-box-icon bg-primary elevation-1"><i class="fa-solid fa-gear"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">En proceso</span>
                                <span class="info-box-number" id="proceso">

                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fa-regular fa-hourglass-half"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pendientes</span>
                                <span class="info-box-number" id="pendiente">

                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="info-box">
                            <span class="info-box-icon bg-secondary elevation-1"><i class="fa-solid fa-list-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Validando</span>
                                <span class="info-box-number" id="validando">

                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="info-box">
                            <span class="info-box-icon bg-light  elevation-1"><i class="fa-solid fa-rotate-left"></i></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Recurrente</span>
                                <span class="info-box-number" id="recurrente">

                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- jQuery -->
    <!-- <script src="jquery.min.js"></script> -->
    <!-- ChartJS -->
    <script src="plugins/js/Chart.min.js"></script>
    <script src="plugins/js/adminlte.min.js"></script>
    <script src="tablero.js"></script>



</body>

</html>