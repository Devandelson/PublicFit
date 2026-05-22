<?php
    require_once "../../php/config.php";
    session_start(); // Asegúrate de iniciar la sesión si usas $_SESSION

    $db = new database();
    $conex = $db->conectar();

    if (empty($_POST['correo']) || empty($_POST['detalle_ruta_usuario'])) {
        $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
        $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
        $_SESSION['respuesta_n'] = "Los datos no se pudieron actualizar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";
        
        // Redirigir al formulario de modificación de datos
        header("location: Panel-usuario.php");
        exit();
    }

    // Asignar valores del formulario
    $correo = $_POST['correo'];
    $detalle_ruta_usuario = $_POST['detalle_ruta_usuario'];

    // Ahora se procede a modificar los datos con UPDATE
    $consulta = $conex->prepare("UPDATE usuario SET correo = ?, detalle_ruta_usuario = ? WHERE id_usuario = 1");

    if ($consulta->execute([$correo, $detalle_ruta_usuario])) {
        $_SESSION['titulo_n'] = "Datos Modificados Exitosamente";
        $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
        $_SESSION['respuesta_n'] = "Los datos se actualizaron correctamente.";
    } else {
        $_SESSION['titulo_n'] = "Error al actualizar";
        $_SESSION['icono_n'] = "fa-solid fa-circle-xmark ";
        $_SESSION['respuesta_n'] = "Hubo un problema al actualizar los datos.";
    }

    // Redirigir al formulario de usuario
    header("location: Panel-usuario.php");
    exit();
?>
