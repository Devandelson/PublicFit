// Filtro
document.addEventListener("DOMContentLoaded", function () {
    const filtroTipo = document.querySelectorAll('input[name="filter_h"]');
    const inputBusqueda = document.querySelector(".buscador_h");
    const filas = document.querySelectorAll(".item_historial");

    function filtrarTabla() {
        const textoBusqueda = inputBusqueda.value.toLowerCase();

        filas.forEach(fila => {
            const nombre = fila.querySelector(".encabezado").querySelector(".datos").children[1].textContent.toLowerCase().trim();

            // Filtrar por el criterio seleccionado
            const coincideFiltro = nombre.includes(textoBusqueda);

            // Mostrar u ocultar la fila según los filtros
            fila.style.display = (coincideFiltro) ? "" : "none";
        });
    }

    // Eventos para actualizar el filtro en tiempo real
    filtroTipo.forEach(radio => radio.addEventListener("change", filtrarTabla));
    inputBusqueda.addEventListener("input", filtrarTabla);

    filtrarTabla();
});