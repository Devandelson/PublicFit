// funciondad para abrir la notificacion con los datos solicitados
let contenedor_notificacion = document.getElementById("contenedor_notificacion");

function abri_notificacion(titulo, contenido, estado,tipo_aviso){
    let icono = "";

    if (estado == 1){
        icono = "fa-solid fa-circle-check icono_activo";
    } else {
        icono = "fa-solid fa-circle-xmark";
    }

    let titulo_notificacion = contenedor_notificacion.children[0].querySelector(".titulo");
    titulo_notificacion.innerHTML = titulo;

    let contenido_notificacion = contenedor_notificacion.querySelector(".contenido");
    contenido_notificacion.innerHTML = contenido;
    
    let logo_notificacion = contenedor_notificacion.children[0].children[0];
    logo_notificacion.className = "";
    logo_notificacion.setAttribute("class" , `${icono}`)

    contenedor_notificacion.parentElement.classList.add("ani_entrada_notificacion");
}

// funcionalidad para cerrar la notificacion
let btn_cerrar_notificacion = document.getElementById("btn_cerrar_notificacion");
btn_cerrar_notificacion.addEventListener("click" , () => {
    contenedor_notificacion.parentElement.classList.remove("ani_entrada_notificacion");
});