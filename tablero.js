// Variable global para almacenar la instancia de la gráfica
var pieChart;

var asesorChart;

function updateChart(rangof, asesor, categoria) {
  $.ajax({
    url: "consulta.php",
    type: "POST",
    data: {
      rangof: rangof,
      asesor: asesor,
      categoria: categoria,
    },
    dataType: "json",
    success: function (data) {
      // Limpiar la gráfica si ya existe una instancia
      if (pieChart) {
        pieChart.destroy();
      }
      if (asesorChart) {
        asesorChart.destroy();
      }

      // Reiniciar los valores de los spans a cero
      $("#finalizado").text(0);
      $("#proceso").text(0);
      $("#pendiente").text(0);
      $("#validando").text(0);
      $("#recurrente").text(0);

      // Crear la nueva gráfica
      var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
      pieChart = new Chart(pieChartCanvas, {
        type: "pie",
        data: data.categorias,
        options: {
          maintainAspectRatio: false,
          responsive: true,
        },
      });
      var asesorChartCanvas = $("#asesorChart").get(0).getContext("2d");
      asesorChart = new Chart(asesorChartCanvas, {
        type: "bar",
        data: data.asesores,
      });

      // Actualizar los valores de los spans
      $("#tickets").text(data.count);
      $.each(data.estados, function (index, estado) {
        if (estado.nombre == "Finalizado") {
          var span = document.getElementById("finalizado");
          span.textContent = estado.cantidad;
        } else if (estado.nombre == "En proceso") {
          var span = document.getElementById("proceso");
          span.textContent = estado.cantidad;
        } else if (estado.nombre == "Pendientes") {
          var span = document.getElementById("pendiente");
          span.textContent = estado.cantidad;
        } else if (estado.nombre == "Validando") {
          var span = document.getElementById("validando");
          span.textContent = estado.cantidad;
        } else if (estado.nombre == "Recurrente") {
          var span = document.getElementById("recurrente");
          span.textContent = estado.cantidad;
        }
      });
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
      // Manejar el error de la solicitud AJAX
      alert(
        "Error al actualizar los datos. Por favor, inténtalo de nuevo más tarde."
      );
    },
  });
}

$(function () {
  // Inicializar los valores de asesor y categoría
  var asesor = $("#asesor").val();
  var rangof = $("#rangof").val();
  var categoria = $("#categoria").val();

  // Llamar a la función updateChart al cargar la página
  updateChart(rangof, asesor, categoria);

  // Agregar evento de cambio para el selector de asesor
  $("#asesor").change(function () {
    rangof = $("#rangof").val();
    categoria = $("#categoria").val();
    asesor = $(this).val();
    updateChart(rangof, asesor, categoria);
  });

  // Agregar evento de cambio para el selector de categoría
  $("#categoria").change(function () {
    rangof = $("#rangof").val();
    asesor = $("#asesor").val();
    categoria = $(this).val();
    updateChart(rangof, asesor, categoria);
  });
  $("#filterIconLink").on("click", function () {
    document.getElementById("categoria").selectedIndex = 0;
    document.getElementById("asesor").selectedIndex = 0;

    // Establecer el valor del input con id "rangof" como vacío
    document.getElementById("rangof").value = "";
    updateChart("", "", "");
  });
  $(document).ready(function () {
    $("#reporte").on("click", function () {
      // Obtener el valor de rangof
      var rangof = $("#rangof").val();

      // Mostrar SweetAlert de descargando
      Swal.fire({
        title: "Tu reporte se esta descargando",
        text: "Revisa en la parte de descargas para ver el proceso de tu archivo..",
        icon: "info",
        showConfirmButton: true,
        allowOutsideClick: false,
      });

      // Abrir una nueva ventana para manejar la descarga del archivo
      var downloadWindow = window.open(
        "reporte.php?rangof=" + rangof,
        "_blank"
      );
    });
  });

  $('input[id="rangof"]').daterangepicker({
    autoUpdateInput: false,
    locale: {
      cancelLabel: "Limpiar",
      applyLabel: "Aplicar",
      format: "YYYY-MM-DD",
      daysOfWeek: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
      monthNames: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ],
    },
  });

  $('input[id="rangof"]').on("apply.daterangepicker", function (ev, picker) {
    $(this).val(
      picker.startDate.format("YYYY-MM-DD") +
        " - " +
        picker.endDate.format("YYYY-MM-DD")
    );
    var fecha = $(this).val();
    asesor = $("#asesor").val();
    categoria = $("#categoria").val();
    updateChart(fecha, asesor, categoria);
  });

  $('input[id="rangof"]').on("cancel.daterangepicker", function (ev, picker) {
    $(this).val("");
    asesor = $("#asesor").val();
    categoria = $("#categoria").val();
    updateChart("", asesor, categoria);
  });
});
