function detalleTransporte() {
    // funcionalidad para colocar los detalles en un input y este mismo ser enviado para guardar los datos.

    // condiciones que indican actualizar los datos
    // ----- Input de tipo numero, cada vez que cambie:
    let inputs_cantidad_transporte = document.querySelectorAll("#cantidad_transporte");
    inputs_cantidad_transporte.forEach((btn) => {
        btn.addEventListener("change", (e) => {
            let nombre = btn.closest(".item_eleccion").querySelector("p").innerHTML.trim();
            let cantidad_transporte = e.target.value;

            let detalles = [nombre, cantidad_transporte];

            // verificar si ya existen los datos para actualizar
            let index = detalle_transporte.findIndex(item => item[0] === nombre);
            if (index !== -1) {
                detalle_transporte[index] = detalles;
            } else {
                detalle_transporte.push(detalles);
            }
            let transporte_input = document.getElementById("transporte_input");
            transporte_input.value = JSON.stringify(detalle_transporte);
        });
    });

    // -- eliminar el transporte
    let eliminar_seleccion = document.querySelectorAll("#eliminar_seleccion");
    eliminar_seleccion.forEach((btn) => {
        btn.addEventListener("click", () => {
            let nombre = btn.closest(".item_eleccion").querySelector("p").innerHTML.trim();

            // eliminar el transporte, del array que tiene todo los datos
            let index = detalle_transporte.findIndex(item => item[0] === nombre);
            if (index !== -1) {
                detalle_transporte.splice(index, 1);
            }

            // actualizar los datos
            let transporte_input = document.getElementById("transporte_input");
            transporte_input.value = JSON.stringify(detalle_transporte);

            // eliminar el elemento del DOM
            let visualizador_elecciones = document.getElementById("visualizador_elecciones");
            visualizador_elecciones.removeChild(btn.closest(".item_eleccion"));
        });
    });
}

// funcionalidad de mostrar o eliminar los transportes seleccionados
let transporte_select = document.getElementById("transporte_select");
let detalle_transporte = [];
transporte_select.addEventListener("change", (e) => {
    // variables
    let valor = e.target.value.trim();
    let visualizador_elecciones = document.getElementById("visualizador_elecciones");
    let transporte_input = document.getElementById("transporte_input");

    // verificar si ya ha sido añadida
    if (detalle_transporte.some(item => item[0] === valor)) {
        return;
    }

    // -- creando estructura
    let button = document.createElement("div");
    button.classList.add("item_eleccion");

    button.innerHTML = `
        <p>${valor}</p>
        <input type="number" id="cantidad_transporte" value="1" min="1">
        <i class="fa-solid fa-trash ani1_icon" id="eliminar_seleccion"></i>
    `;

    visualizador_elecciones.append(button);

    // guardar elemento
    detalle_transporte.push([valor, 0]);

    detalleTransporte();
    transporte_input.value = JSON.stringify(detalle_transporte);
});
