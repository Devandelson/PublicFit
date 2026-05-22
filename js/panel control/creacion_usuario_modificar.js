  // ===================================== Codigo de imagen
  let btn_img = document.getElementById("imagen_usuario");
  let img = document.getElementById("visualizador_imagen").querySelector("img");

  btn_img.addEventListener("change", function (e) {
      let files = e.target.files;
      let cantidad_img = 1;
      let blocked_image_types = ["image/jpeg", "image/jpg", "image/png"];
      let max_size = 500 * 1024; // 500KB
  
      // Validar cantidad de imágenes
      if (files.length !== cantidad_img) {
          if (files.length > cantidad_img) {
            Swal.fire({
                icon: "error",
                title: "Límite de Imágenes",
                text: `Solo puedes seleccionar hasta ${cantidad_img} imagen.`,
            });

          } else {
                Swal.fire({
                    icon: "error",
                    title: "Selección de Imágenes",
                    text: `Debes seleccionar ${cantidad_img} imagen.`,
                });
          }
          return;
      }
  
      let file = files[0]; // Solo hay un archivo debido a la validación anterior
  
      // Validar tamaño de la imagen
      if (file.size > max_size) {
            img.classList.add("ani-img");
            setTimeout(() => {
                img.classList.remove("ani-img");
            }, 120);

            Swal.fire({
                icon: "error",
                title: "Error al Subir Imágenes",
                text: `La imagen excede el límite de 500KB. Por favor, reduzca el tamaño y vuelva a intentarlo.`,
            });

          return;
      }
  
      // Validar tipo de la imagen
      if (!blocked_image_types.includes(file.type)) {
            img.classList.add("ani-img");
            setTimeout(() => {
                img.classList.remove("ani-img");
            }, 120);

            Swal.fire({
                icon: "error",
                title: "Error al Subir Imágenes",
                text: `Por favor, asegúrese de que la imagen sea en formato JPG, JPEG o PNG.`,
            });

        return;
      }
  
      // Cargar la imagen si todas las validaciones pasan
      let reader = new FileReader();
      reader.onload = function (e) {
          img.classList.add("ani-img");
          setTimeout(() => {
              img.src = e.target.result;
              img.classList.remove("ani-img");
          }, 200);
      };
      reader.readAsDataURL(file);
  });
  