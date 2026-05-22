// funcionalidad de ver detalles de recarga
let btn_expandir_recargas = document.querySelectorAll("#btn_expandir_recargas");
btn_expandir_recargas.forEach((btn) => {
    btn.addEventListener("click" , () => {
        let contenedor = btn.closest(".item_historial");
        let contenedor_detalle_recarga = contenedor.querySelector(".detalles");
        let id_dato = contenedor.querySelector(".encabezado").querySelector(".id_dato").innerHTML.trim();
        let contenedor_detalle_recarga_tabla = contenedor_detalle_recarga.children[0].querySelector("tbody");

        // antes de mostrar el historial, buscar datos de historial con AJAX
        $.ajax({
            // Parametros
            url: '../../php/panel_control/resumen/datos_historial.php',
            type: 'POST', // type of send
            data: {
                id_usuario: id_dato
            },
            // respuesta correcta
            success: function(response){
                // answer of the server, example:
                const data = JSON.parse(response);
                console.log(data);

                // creando un bucle para los multiples datos de recargas
                contenedor_detalle_recarga_tabla.innerHTML = "";
                
                data.forEach((item) => {
                    let fecha = item.fecha_recarga;
                    let monto = item.monto;
                    let institucion = item.identificador_institucion;
    
                    // imprimiendo datos
                    // -- primero creando estructura
                    let contenedor_fila = document.createElement("tr");
                    let item_columna1 = document.createElement("td");
                    item_columna1.innerHTML = fecha;
    
                    let item_columna2 = document.createElement("td");
                    item_columna2.innerHTML = monto;
    
                    let item_columna3 = document.createElement("td");
                    item_columna3.innerHTML = institucion;
    
                    contenedor_fila.append(item_columna1,item_columna2,item_columna3);
    
                    // colocar en el dom
                    contenedor_detalle_recarga_tabla.append(contenedor_fila);
                })
            },
            // mala respuesta
            error: function(xhr){
                console.error(xhr.responseText);
            }
        });

        contenedor_detalle_recarga.classList.toggle("active_detalles");
    });
});