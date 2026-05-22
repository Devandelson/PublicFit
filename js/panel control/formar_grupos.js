let grupo_institucion = document.getElementById("grupo_institucion");
grupo_institucion.addEventListener(("change") , (e) => {
    let valor = e.target.value;
    let grupo_categoria = document.getElementById("grupo_categoria");

    // mandar datos
    $.ajax({
        // Parametros
        url: '../../php/panel_control/dato/obtener_grupos.php',
        type: 'POST', // type of send
        data: {
            institucion: valor
        },
        // respuesta correcta
        success: function(response){
            const data = JSON.parse(response);
            grupo_categoria.innerHTML = "";
            
            data.forEach(item => {
                let option = document.createElement("option");
                option.value = item;
                option.innerHTML = item;

                grupo_categoria.append(option);
            });
        },
        // mala respuesta
        error: function(xhr){
            console.error(xhr.responseText);
        }
    });
});