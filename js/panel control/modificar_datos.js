// estado cuenta, desplegable
let estado_cuenta = document.getElementById('estado_cuenta');
estado_cuenta.addEventListener('change', (e) => {
     let valor = e.target.value;
     if (valor == "inactiva"){
      estado_cuenta.classList.add("inactivo");
     } else {
      estado_cuenta.classList.remove("inactivo");
     }
});

function estado_cuenta_funcion(valor) {
  if (valor == "inactiva"){
    estado_cuenta.classList.add("inactivo");
   } else {
    estado_cuenta.classList.remove("inactivo");
   }
};

estado_cuenta_funcion(estado_cuenta.options[estado_cuenta.selectedIndex].text);

// btn eliminar cuenta (peticion a un archivo php a traves de AJAX)
let eliminar_cuenta = document.getElementById('eliminar_cuenta');
eliminar_cuenta.addEventListener('click', () => {
  Swal.fire({
    // atributos
    title: "¿Está seguro de que desea eliminar este usuario?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar"
  }).then((result) => {
    // respuesta correcta
    // if confirm asnwer, so publication delete
    if (result.isConfirmed) {
        // data
        let id_user_info = document.getElementById("id_usuario").value;

        $.ajax({
            // parametes
            url: '../../php/panel_control/dato/eliminar_usuario.php',
            type: 'POST', // type of send
            data: {
              id_user: id_user_info,
            },
            // correct answer
            success: function(response){    
                Swal.fire({
                    title: "El usuario ha sido eliminado con éxito",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ok"
                }).then((result) => {
                  if (result.isConfirmed){
                      location.href = "datos.php";
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

