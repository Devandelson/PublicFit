<?php
    session_start(); // Iniciar la sesión
    require_once "../config.php";
    $db = new database();
    $conn = $db->conectar();


    // Verifica si el formulario fue enviado
    if (isset($_POST['btn_register'])) {
        // Validar si los campos están vacíos
        if (empty($_POST['correo']) || empty($_POST['contrasena'])) {
            // Preparar el mensaje de error
            $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
            $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
            $_SESSION['respuesta_n'] = "Los datos no se pudieron validar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

            // Redirigir al formulario de modificacion de datos
            header("location: ../../index.php");
            exit();
        } else {
            $usuario = $_POST['correo'];
            $clave = $_POST['contrasena'];

            // Preparar la consulta para evitar inyección SQL
            $sql = $conn->prepare("SELECT * FROM usuario WHERE correo = :usuario AND contrasena = :clave");
            $sql->bindParam(':usuario', $usuario);
            $sql->bindParam(':clave', $clave);
            $sql->execute();

            // Verificar si el usuario existe (super admin o usuario)
            if ($sql->rowCount() > 0) {
                $datos = $sql->fetch(PDO::FETCH_ASSOC);
                $perfil = strtolower($datos['perfil']);

                // despues de validar el registro, verificar si el perfil del usuario
                if($perfil == "admin"){
                   // guardando datos
                   // perfil del tipo de administrador
                   $_SESSION['perfil_usuario'] = "s_admin";
                   $_SESSION['nombre_usuario'] = $datos['nombre'];
                   $_SESSION['imagen_usuario'] = $datos['imagen'];

                    // preparando el mensaje
                    $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
                    $_SESSION['titulo_n'] = "Inicio de Sesión Exitoso";
                    $_SESSION['respuesta_n'] = "¡Bienvenido! Has iniciado sesión correctamente.";
                    header('location:../../paginas/panel control/panel_administrador_inicio.php');
                    exit(); // Asegúrate de que el script se detenga después de redirigir
                } else {
                    // antes de verificar si el usuario completo el registro
                    if(strtolower($datos['proceso_registro']) !== "completo"){
                        // Preparar el mensaje de aviso
                        $_SESSION['id_usuario'] = $datos['id_usuario'];

                        $_SESSION['icono_n'] = "fa-solid fa-circle-info icono_info";
                        $_SESSION['titulo_n'] = "Registro Incompleto";
                        $_SESSION['respuesta_n'] = "El usuario aún debe completar los últimos datos del registro. Por favor, complete la información pendiente antes de iniciar sesión. Gracias por su comprensión.";                    

                        // Redirigir al formulario de modificacion de datos
                        header("location: ../../paginas/login/registro-usuario.php");
                        exit();
                    }

                    // guardando datos
                    $_SESSION['id_usuario'] = $datos['id_usuario'];

                    // preparando el mensaje
                    $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
                    $_SESSION['titulo_n'] = "Inicio de Sesión Exitoso";
                    $_SESSION['respuesta_n'] = "¡Bienvenido! Has iniciado sesión correctamente.";

                    // Redirigir si el login es exitoso
                    header('location:../../paginas/usuario_comun/panel-usuario.php');
                    exit(); // Asegúrate de que el script se detenga después de redirigir
                }
            
            } else {
                // verificar si es una institución
                $sql = $conn->prepare("SELECT * FROM institucion WHERE correo = :usuario AND contraseña_institucion = :clave");
                $sql->bindParam(':usuario', $usuario);
                $sql->bindParam(':clave', $clave);
                $sql->execute();

                if ($sql->rowCount() > 0){
                    $datos = $sql->fetch(PDO::FETCH_ASSOC);
                    // guardando datos
                    // perfil del tipo de administrador
                    $_SESSION['perfil_usuario'] = "admin";
                    $_SESSION['nombre_usuario'] = $datos['nombre_institucion'];
                    $_SESSION['imagen_usuario'] = $datos['imagen'];
                    $_SESSION['categorias'] = $datos['grupos'];

                    // preparando el mensaje
                    $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
                    $_SESSION['titulo_n'] = "Inicio de Sesión Exitoso";
                    $_SESSION['respuesta_n'] = "¡Bienvenido! Has iniciado sesión correctamente.";
                    header('location:../../paginas/panel control/panel_administrador_inicio.php');
                    exit(); // Asegúrate de que el script se detenga después de redirigir
                } else {
                    // Preparar el mensaje de error
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Datos No Encontrados";
                    $_SESSION['respuesta_n'] = "Los datos ingresados no se encontraron en el sistema. Por favor, verifique la información e intente de nuevo. Gracias.";

                    // Redirigir al formulario de modificacion de datos
                    header("location: ../../index.php");
                    exit();
                }
            }
        }
    }
?>
