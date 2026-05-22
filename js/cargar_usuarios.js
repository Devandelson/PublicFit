  // ===================================== Codigo de subir archivo
  let btn_img = document.getElementById("archivo");
  let img = document.getElementById("imagen_archivo");
  let archivo = "";
  btn_img.addEventListener("change", function (e) {
      archivo = e.target.files;
      let cantidad_img = 1;
      let blocked_image_types = [
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // .xlsx
        "application/vnd.ms-excel", // .xls
        "application/vnd.ms-excel.sheet.macroEnabled.12", // .xlsm
        "application/vnd.ms-excel.template.macroEnabled.12", // .xltm
        "application/vnd.ms-excel.addin.macroEnabled.12", // .xlam
        "application/vnd.ms-excel.sheet.binary.macroEnabled.12" // .xlsb
      ];
      
      let max_size = 500 * 1024; // 500KB
  
      // Validar cantidad de imágenes
      if (archivo.length !== cantidad_img) {
          if (archivo.length > cantidad_img) {
            Swal.fire({
                icon: "error",
                title: "Límite de Archivos",
                text: `Solo puedes seleccionar hasta ${cantidad_img} archivo.`,
            });

          } else {
                Swal.fire({
                    icon: "error",
                    title: "Selección de archivo",
                    text: `Debes seleccionar ${cantidad_img} archivo.`,
                });
          }
          return;
      }
  
      let file = archivo[0]; // Solo hay un archivo debido a la validación anterior
  
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
                title: "Error al Subir Archivos",
                text: `Por favor, asegúrese de que el archivo esté en formato Excel.`,
            });

        return;
      }
  
      // Cargar la imagen si todas las validaciones pasan
      let reader = new FileReader();
      reader.onload = function (e) {
          img.classList.add("ani-img");
          setTimeout(() => {
              img.src = "../../imagenes/excel.png";
              let texto_archivo = document.getElementById("texto_archivo");
              texto_archivo.innerHTML = file.name;
              img.classList.remove("ani-img");
          }, 200);
      };
      reader.readAsDataURL(file);
  });
  
  // --------------------- btn para mandar la informacion de los multiples estudiantes
  // let btn_lista_usuarios = document.getElementById("btn_lista_usuarios");
  // btn_lista_usuarios.addEventListener("click", () => {
  //   const file = (archivo == "") ? "" : archivo[0];
  //   if (!file) {
  //     Swal.fire("No se ha subido ningún archivo para guardar usuarios.");
  //     return;
  //   }

  //   // antes que todo, realizar una pregunta antes de hacer el proceso
  //   Swal.fire({
  //       // atributos
  //       title: '¿Deseas guardar todos los estudiantes?',
  //       text: 'Se guardarán los estudiantes enviados a través del archivo.',
  //       icon: "warning",
  //       showCancelButton: true,
  //       confirmButtonColor: "#3085d6",
  //       cancelButtonColor: "#d33",
  //       confirmButtonText: "Sí, guardar"
  //     }).then((result) => {
  //       // respuesta correcta

  //       // if confirm asnwer, so publication delete
  //       if (result.isConfirmed) {
  //           // data
  //           const reader = new FileReader();
  //           reader.onload = function(e) {
  //             const data = new Uint8Array(e.target.result);
  //             const workbook = XLSX.read(data, { type: 'array' });
        
  //             // Obtener el nombre de la primera hoja
  //             const sheetName = workbook.SheetNames[0];
  //             const sheet = workbook.Sheets[sheetName];
        
  //             // Convertir la hoja a JSON
  //             let jsonData = XLSX.utils.sheet_to_json(sheet);
  //             let resultado_json = JSON.stringify(jsonData);

  //             // colocar pantalla de carga, para informar al usuario
  //             let contenedor_loader = document.getElementById("contenedor_loader");
  //             contenedor_loader.classList.remove("inactive_loader");

  //             // luego de obtener los datos, mandar los datos al servidor a traves de ajax
  //             $.ajax({
  //               // Parametros
  //               url: '../../php/panel_control/dato/guardar_multiples_datos.php',
  //               type: 'POST', // type of send
  //               data: {
  //                   array_datos: resultado_json
  //               },
  //               // respuesta correcta
  //               success: function(response){
  //                 console.log(response);
  //                 // despues de todo quitar la pantalla de carga
  //                 window.location.reload();
  //               },
  //               // mala respuesta
  //               error: function(xhr){
  //                   console.error(xhr.responseText);
  //               }
  //             });
  //           };
  //           reader.readAsArrayBuffer(file);

  //       } else {
  //           Swal.fire({
  //               title: "Acción cancelada",
  //               icon: "info"
  //           });  
  //       }
  //     });
  // })

  // ------------------------- Funcionalidad de abrir y cerrar la ventana
  let btn_guardar_usuarios = document.querySelectorAll("#btn_guardar_usuarios");
  btn_guardar_usuarios.forEach((btn) => {
    btn.addEventListener("click" , () => {
      let contenedor_guardar_usuarios = document.getElementById("contenedor_guardar_usuarios");
      contenedor_guardar_usuarios.classList.toggle("inactive_g_u");
    });
  }); 