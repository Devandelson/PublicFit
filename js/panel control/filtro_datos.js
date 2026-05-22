// funcionalidad de mandar la petición para eliminar un usuario
let btn_eliminar_registro = document.querySelectorAll("#btn_eliminar_registro");
btn_eliminar_registro.forEach((btn) => {
    btn.addEventListener("click", () => {
        let contenedor_dato = btn.closest("tr");
        let id_dato = contenedor_dato.querySelector(".id_usuario").innerHTML;

        // mandar mensaje antes de eliminar el usuario.
        Swal.fire({
            title: "¿Desea eliminar este usuario?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar"
        }).then((result) => {

            // if confirm asnwer, so user delete
            if (result.isConfirmed) {

                $.ajax({
                    // parametes
                    url: '../../php/panel_control/dato/eliminar_usuario.php', // url of send
                    type: 'POST', // type of send
                    data: {
                        id_user: id_dato,
                    },
                    // correct answer
                    success: function(response){
                        Swal.fire({
                            title: "¡Eliminado con éxito!",
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Ok"
                        }).then((result) => {
                            if (result.isConfirmed){
                                location.reload();
                            }
                        });
                    },
                    // bad answer
                    error: function(xhr){
                        console.error(xhr.responseText);
                    }
                });

            } else {
                Swal.fire({
                    title: "Acción cancelada",
                    icon: "info"
                });  
            }
        });
    });
});

// Filtro
let contenedor_loader = document.getElementById("contenedor_loader");

document.addEventListener("DOMContentLoaded", function () {
    const filtroGrupo = document.querySelector(".group-select");
    const filtroTipo = document.querySelectorAll('input[name="filter"]');
    const inputBusqueda = document.querySelector(".buscador");
    const filas = document.querySelectorAll("tbody tr");

    function filtrarTabla() {
        const tipoFiltro = document.querySelector('input[name="filter"]:checked')
            .parentNode.textContent.trim().toLowerCase();
        const grupoSeleccionado = filtroGrupo.value.toLowerCase();
        const textoBusqueda = inputBusqueda.value.toLowerCase();

        filas.forEach(fila => {
            const nombre = fila.querySelector(".datos_basicos").textContent.toLowerCase().trim();
            const correo = fila.querySelector("td:nth-child(2)").textContent.toLowerCase().trim();

            // Obtener el grupo de la fila dinámicamente
            const grupoFila = fila.querySelector(".grupo").textContent.toLowerCase().trim();  
            const coincideGrupo = (grupoFila == grupoSeleccionado) ? true : false;

            // Filtrar por el criterio seleccionado
            const coincideFiltro = tipoFiltro === "nombre" ? nombre.includes(textoBusqueda) : correo.includes(textoBusqueda);

            // Mostrar u ocultar la fila según los filtros
            fila.style.display = (coincideFiltro && coincideGrupo) ? "" : "none";
        });
    }

    // Eventos para actualizar el filtro en tiempo real
    filtroGrupo.addEventListener("change", filtrarTabla);
    filtroTipo.forEach(radio => radio.addEventListener("change", filtrarTabla));
    inputBusqueda.addEventListener("input", filtrarTabla);

    filtrarTabla();
});