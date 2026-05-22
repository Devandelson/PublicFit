// funcionalidad de mostrar o eliminar los transportes seleccionados
let agregar_grupo = document.getElementById("agregar_grupo");
agregar_grupo.addEventListener("click", (e) => {
    // variables
    let grupos_institucion = document.getElementById("detalle_grupo");
    let grupos = document.getElementById("grupos");
    let grupo_input = document.getElementById("grupo_input");

    if (grupo_input.value.trim() != ""){
        // verificar si ya ha sido añadida

        // -- creando estructura
        let button = document.createElement("div");
        button.classList.add("item_grupo");

        let p = document.createElement("p");
        p.innerHTML = grupo_input.value;

        let i = document.createElement("i");
        i.classList.add("fa-solid", "fa-trash" , "ani1_icon");

        button.append(p, i);

        i.addEventListener("click" , () => {
            grupos.removeChild(button);
            grupos_institucion.value = grupos_institucion.value.replace(`${grupo_input.value} ,`, "");
        });

        grupos.append(button);

        // guardar elemento en el input que guarda todos los datos
        grupos_institucion.value +=  grupo_input.value + " ,";
    } 
});
