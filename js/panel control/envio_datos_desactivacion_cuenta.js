let btn_eliminar = document.getElementById('eliminar_usuario');
btn_eliminar.addEventListener('click', () => {
    // mandar mensaje de validacion
    abri_notificacion(
      "¿Está seguro de que desea desactivar esta cuenta?",
      "Por favor, especifique la razón.",
      2,
      "avíso"
    );
});
