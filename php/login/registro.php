<?php
    session_start(); // Iniciar la sesión
    require_once "../config.php";
    $db = new database();
    $conn = $db->conectar();

    if (isset($_POST['btn_register'])) {
        if (empty($_POST['detalles'])) {
            // Preparar el mensaje de error
            $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
            $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
            $_SESSION['respuesta_n'] = "Los datos no se pudieron validar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de validar los datos. Gracias.";

            // Redirigir al formulario de modificacion de datos
            header("location: ../../paginas/login/registro-usuario.php");
            exit();
        } else {
            //datos del usuario
            $id_usuario = $_SESSION['id_usuario'];
            $detalles = $_POST['detalles'];

            // Actualizar los detalles de ruta en la cuenta del usuario logueado
            $sql = $conn->prepare("UPDATE usuario SET detalle_ruta_usuario = :detalles, proceso_registro = 'completo' WHERE id_usuario = :id_usuario");

            $sql->bindParam(':detalles', $detalles);
            $sql->bindParam(':id_usuario', $id_usuario);

            if ($sql->execute()) {
                // Redirigir si el login es exitoso
                $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
                $_SESSION['titulo_n'] = "Registro Exitoso";
                $_SESSION['respuesta_n'] = "¡Bien hecho! Has completado el registro correctamente. Ahora puedes iniciar sesión.";

                header('location:../../paginas/usuario_comun/panel-usuario.php');
            } else {

            }
        }
    }
?>