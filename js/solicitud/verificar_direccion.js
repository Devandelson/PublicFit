let btn_register_solicitud = document.getElementById("btn_register_solicitud");
btn_register_solicitud.addEventListener("click", async (e) => {
    e.preventDefault();

    let direccion = document.getElementById("direccion");
    if (direccion.value.trim() !== "") {
        // Llamamos a la función geocode de forma asíncrona y esperamos el resultado
        let resultado = await geocode(direccion.value);
        if (!resultado) {
            alert('No se pudieron geocodificar las direcciones. Verifica que estén correctas.');
        } else {
            let frm_solicitud = document.getElementById("frm_solicitud");
            frm_solicitud.submit();
        }
    } else {
        Swal.fire({
            icon: "warning",
            title: "Campo obligatorio",
            text: "El campo 'Dirección' está vacío. Por favor, complételo antes de enviar la solicitud."
        });
    }
});

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
