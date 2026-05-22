  // Función para geocodificar una dirección utilizando Nominatim
  function geocode(address) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;
    return fetch(url)
      .then(response => response.json())
      .then(data => {
        if (data && data.length > 0) {
          return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
        } else {
          return null;
        }
      })
      .catch(error => {
        console.error('Error al geocodificar:', error);
        return null;
      });
  }

// Función asíncrona para calcular la ruta e instrucciones usando GraphHopper
async function calcularRuta(direccion_user, direccion_ins) {
    if (!direccion_user || !direccion_ins) {
      alert('No se pudo obtener el origen y/o destino.');
      return "";
    }
  
    // Obtener coordenadas para origen y destino usando Nominatim
    const [origenCoords, destinoCoords] = await Promise.all([
      geocode(direccion_user),
      geocode(direccion_ins)
    ]);
  
    if (!origenCoords || !destinoCoords) {
      alert('Error en la geocodificación de alguna dirección.');
      return "";
    }
  
    // Construir la URL de la API de GraphHopper
    const ghURL = `https://graphhopper.com/api/1/route?point=${origenCoords.lat},${origenCoords.lng}&point=${destinoCoords.lat},${destinoCoords.lng}&vehicle=car&locale=es&instructions=true&points_encoded=false&key=b3a0e010-56fc-47d4-bf7a-d18da214bb5f`;
  
    try {
      const response = await fetch(ghURL);
      if (!response.ok) {
        throw new Error(`Error en GraphHopper: ${response.status}`);
      }
      const data = await response.json();
      if (data.paths && data.paths.length > 0) {
        const path = data.paths[0];
        let htmlInstrucciones = '';
        path.instructions.forEach((step, index) => {
          htmlInstrucciones += `Paso ${index + 1}: ${step.text} - ${(step.distance / 1000).toFixed(2)} km. \n`;
        });
        return htmlInstrucciones;
      } else {
        return "Ruta no encontrada.";
      }
    } catch (error) {
      console.error('Error al obtener la ruta:', error);
      return "Ocurrió un error al calcular la ruta.";
    }
  }
  
  // Listener para los botones de cambios
  let btn_cambios = document.querySelectorAll("#btn_cambios");
  btn_cambios.forEach((btn) => {
    btn.addEventListener("click", () => { 
      let formulario_envio_solicitud = document.getElementById("formulario_envio_solicitud");
      let id_solicitud_frm = document.getElementById("id_solicitud_frm");
      let direccion = document.getElementById("direccion");
      let detalles_solicitud_frm = document.getElementById("detalles_solicitud_frm");
  
      // Obtener datos de la fila
      let fila = btn.closest("tr");
      let estadoSolicitud1 = fila.querySelector(".estado_solicitud").children[0].querySelector("#estado_solicitud1");
  
      Swal.fire({
        title: "Confirmación de cambios: ¿Desea continuar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, continuar"
      }).then(async (result) => {
        if (result.isConfirmed) {
          // Determinar el estado de la solicitud
          let cambio_solicitud = estadoSolicitud1.checked ? "aprobado" : "pendiente";
          let id_dato = btn.closest("tr").querySelector(".id_dato").innerHTML.trim();
          let direccion_completa = "";
  
          // Realizamos la solicitud AJAX para obtener las direcciones (usando jQuery)
          $.ajax({
            url: '../../php/solicitud/crear_direccion_completa.php',
            type: 'POST',
            data: {
              id_solicitud: id_dato
            },
            success: async function(response) {
              const data = JSON.parse(response);
            
              let tipo_solicitud = data.tipo_solicitud;

              let direccion_user = "";
              let direccion_ins = "";
  
              if (tipo_solicitud == "usuario") {
                direccion_user = data.direccion_user;
                direccion_ins = data.direccion_ins;

                // Calcular la ruta e instrucciones con GraphHopper
                direccion_completa = await calcularRuta(direccion_user, direccion_ins);
              } else {
                direccion_completa = "";
              }

              // Una vez obtenido el detalle de la ruta, se colocan los datos en el formulario
              id_solicitud_frm.value = id_dato;
              // Puedes combinar el cambio de solicitud con las instrucciones obtenidas o asignarlos a campos diferentes
              direccion.value = direccion_completa;
              detalles_solicitud_frm.value = cambio_solicitud;
  
              // Finalmente se envía el formulario
              formulario_envio_solicitud.submit();
            },
            error: function(xhr) {
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
  