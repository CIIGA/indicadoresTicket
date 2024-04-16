// Variable global para almacenar la instancia de la gráfica
var pieChart;

var estadoChart;

var subcategoriaChart;
function llenarSelectSubcategorias(subcategorias, subcategoriaSeleccionada) {
  // Obtener el select
  var selectSubcategoria = document.getElementById("subcategoria");

  // Limpiar el select por si ya tiene opciones previas
  selectSubcategoria.innerHTML = "";

  // Agregar la opción "Todos"
  var optionTodos = document.createElement("option");
  optionTodos.value = "";
  optionTodos.text = "Todos";
  selectSubcategoria.appendChild(optionTodos);

  // Iterar sobre las subcategorías y agregarlas como opciones al select
  subcategorias.forEach(function (subcategoria) {
    var option = document.createElement("option");
    option.value = subcategoria.id;
    option.text = subcategoria.nombre;
    selectSubcategoria.appendChild(option);

    // Seleccionar la opción correspondiente a la subcategoría actual
    if (subcategoria.id == subcategoriaSeleccionada) {
      option.selected = true;
    }
  });
}

function updateChart(
  rangof,
  estado,
  categoria,
  subcategoria,
  usuario,
  plaza,
  medio,
  area
) {
  $.ajax({
    url: "consulta.php",
    type: "POST",
    data: {
      rangof: rangof,
      estado: estado,
      categoria: categoria,
      subcategoria: subcategoria,
      usuario: usuario,
      plaza: plaza,
      medio: medio,
      area: area,
    },
    dataType: "json",
    success: function (data) {
      // Limpiar la gráfica si ya existe una instancia
      if (pieChart) {
        pieChart.destroy();
      }
      if (estadoChart) {
        estadoChart.destroy();
      }
      if (subcategoriaChart) {
        subcategoriaChart.destroy();
      }
      llenarSelectSubcategorias(data.filtro_subcategoria, subcategoria);
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
      var estadoChartCanvas = $("#estadoChart").get(0).getContext("2d");
      estadoChart = new Chart(estadoChartCanvas, {
        type: "bar",
        data: data.estados,
      });
      var subcategoriaChartCanvas = $("#subcategoriaChart")
        .get(0)
        .getContext("2d");
      subcategoriaChart = new Chart(subcategoriaChartCanvas, {
        type: "horizontalBar",
        data: data.subcategorias,
      });

      // Actualizar los valores de los spans
      $("#tickets").text(data.count);
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
      // Manejar el error de la solicitud AJAX
      alert(
        "Error al actualizar los datos. Por favor, inténtalo de nuevo más tarde." +
          error
      );
    },
  });
}

$(function () {
  // Inicializar los valores de asesor y categoría
  var estado = $("#estado").val();
  var rangof = $("#rangof").val();
  var categoria = $("#categoria").val();
  var subcategoria = $("#subcategoria").val();
  var usuario = $("#usuario").val();
  var plaza = $("#plaza").val();
  var medio = $("#medio").val();
  var area = $("#area").val();

  // Llamar a la función updateChart al cargar la página
  updateChart(
    rangof,
    estado,
    categoria,
    subcategoria,
    usuario,
    plaza,
    medio,
    area
  );

  // Agregar evento de cambio para el selector de asesor
  $("#estado").change(function () {
    rangof = $("#rangof").val();
    categoria = $("#categoria").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    estado = $(this).val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart(rangof, estado, categoria, subcategoria, usuario, plaza, medio, area);
  });

  $("#medio").change(function () {
    rangof = $("#rangof").val();
    categoria = $("#categoria").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    medio = $(this).val();
    estado = $("#estado").val();
    area = $("#area").val();
    updateChart(rangof, estado, categoria, subcategoria, usuario, plaza, medio, area);
  });

  $("#area").change(function () {
    rangof = $("#rangof").val();
    categoria = $("#categoria").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    area = $(this).val();
    medio = $("#medio").val();
    estado = $("#estado").val();
    updateChart(rangof, estado, categoria, subcategoria, usuario, plaza, medio, area);
  });

  // Agregar evento de cambio para el selector de categoría
  $("#categoria").change(function () {
    rangof = $("#rangof").val();
    estado = $("#estado").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    categoria = $(this).val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart(rangof, estado, categoria, "", usuario, plaza, medio, area);
  });
  $("#usuario").change(function () {
    rangof = $("#rangof").val();
    estado = $("#estado").val();
    subcategoria = $("#subcategoria").val();
    categoria = $("#categoria").val();
    plaza = $("#plaza").val();
    usuario = $(this).val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart(rangof, estado, categoria, subcategoria, usuario, plaza, medio, area);
  });
  $("#plaza").change(function () {
    rangof = $("#rangof").val();
    estado = $("#estado").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    categoria = $("#categoria").val();
    plaza = $(this).val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart(rangof, estado, categoria, subcategoria, usuario, plaza, medio, area);
  });
  $("#subcategoria").change(function () {
    rangof = $("#rangof").val();
    estado = $("#estado").val();
    categoria = $("#categoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    subcategoria = $(this).val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart(rangof, estado, categoria, subcategoria, usuario, plaza, medio, area);
  });

  $("#filterIconLink").on("click", function () {
    document.getElementById("categoria").selectedIndex = 0;
    document.getElementById("estado").selectedIndex = 0;
    document.getElementById("subcategoria").selectedIndex = 0;
    document.getElementById("usuario").selectedIndex = 0;
    document.getElementById("plaza").selectedIndex = 0;
    document.getElementById("medio").selectedIndex = 0;
    document.getElementById("area").selectedIndex = 0;

    // Establecer el valor del input con id "rangof" como vacío
    document.getElementById("rangof").value = "";
    updateChart("", "", "", "", "", "", "", "");
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
    estado = $("#estado").val();
    categoria = $("#categoria").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart(fecha, estado, categoria, subcategoria, usuario, plaza,medio,area);
  });

  $('input[id="rangof"]').on("cancel.daterangepicker", function (ev, picker) {
    $(this).val("");
    estado = $("#estado").val();
    categoria = $("#categoria").val();
    subcategoria = $("#subcategoria").val();
    usuario = $("#usuario").val();
    plaza = $("#plaza").val();
    medio = $("#medio").val();
    area = $("#area").val();
    updateChart("", estado, categoria, subcategoria, usuario, plaza, medio, area);
  });
});
