<?php 
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    if (isset($_POST['btn_guardar'])){
        try{
            // Verificar si alguno de los campos está vacío
            if (empty($_POST['nombre']) 
            || empty($_POST['contraseña']) 
            || empty($_POST['correo']) 
            || empty($_POST['grupo']) 
            || empty($_POST['proceso_registro'])
            || empty($_POST['vinculo_institucion'])
            || empty($_POST['estado_tarjeta'])
            ) {
                // Preparar el mensaje de error
                $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
                $_SESSION['respuesta_n'] = "Los datos no se pudieron guardar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../../paginas/panel control/creacion_usuario_guardar.php");
                exit();
            }

            // datos
            $nombre_usuario = $_POST['nombre'];
            $contraseña = $_POST['contraseña'];
            $correo = $_POST['correo'];
            $monto = NULL;
            $grupo = $_POST['grupo'];
            $detalle_ruta_usuario = $_POST['detalle_ruta_usuario'];
            $ultima_fecha_recarga = NULL;
            $perfil = "usuario";
            $proceso_registro = $_POST['proceso_registro'];
            $vinculo_institucion = $_POST['vinculo_institucion'];
            $estado_tarjeta = $_POST['estado_tarjeta'];
            $estado_cuenta = "activa";

            $file = $_FILES['imagen_nueva'];

            // condicion para validar el correo.
            $consulta_correo = $conex->prepare("SELECT * FROM usuario WHERE correo = ?");
            $consulta_correo->execute([$correo]);

            if ($consulta_correo->rowCount() > 0){
                // Preparar el mensaje de error
                $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                $_SESSION['titulo_n'] = "Correo Ya Registrado";
                $_SESSION['respuesta_n'] = "No es posible modificar los datos porque el correo ingresado ya está registrado en el sistema. Por favor, intente con otro correo o comuníquese con el usuario correspondiente. Gracias.";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../../paginas/panel control/creacion_usuario_guardar.php");
                exit();
            }

            // conditions of the update image
            if (!empty($_FILES['imagen_nueva']['tmp_name'])){
                // new image
                $ruta_temporal = $file['tmp_name'];
                $ubicacion_temporal = "../../../archivos_subidos/";
                $stringAleatorio = substr(uniqid(),0,6);
            
                if ($file['size'] > 500 * 1024){
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Tamaño de Imagen Excedido";
                    $_SESSION['respuesta_n'] = "El archivo de imagen supera el límite permitido de 500KB. Por favor, cargue una imagen que cumpla con las restricciones de tamaño.";        
            
                    header("location: ../../../paginas/panel control/creacion_usuario_guardar.php");
                }
            
                $tipos_admitidos = ["image/png" , "image/jpeg" , "image/jpg"];
                if (!in_array($file['type'] , $tipos_admitidos)){
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Formato de Imagen No Válido";
                    $_SESSION['respuesta_n'] = "Solo se aceptan imágenes en formato JPG, JPEG o PNG. Por favor, cargue un archivo con uno de estos formatos.";            
            
                    header("location: ../../../paginas/panel control/creacion_usuario_guardar.php");
                }
                
                // subir imagen nueva
                $nombre = $stringAleatorio . $file['name'];
                move_uploaded_file($ruta_temporal, $ubicacion_temporal . $nombre);
                $ubicacion_img_bd = "archivos_subidos/" . $nombre;
            } else {
                // nothing image
                // Preparar el mensaje de error
                $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
                $_SESSION['respuesta_n'] = "Los datos no se pudieron modificar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../../paginas/panel control/creacion_usuario_guardar.php");
                exit();
            }

            // ahora se procede a modificar los datos
            $consulta = $conex->prepare("INSERT INTO usuario (
                nombre,
                correo,
                contrasena,
                monto,
                ultima_fecha_recarga,
                perfil,
                grupo,
                detalle_ruta_usuario,
                estado_monto,
                proceso_registro,
                imagen,
                estado_cuenta,
                identificador_institucion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?);
            ");

            $consulta->execute([$nombre_usuario, $correo, $contraseña, $monto, $ultima_fecha_recarga, $perfil, $grupo, $detalle_ruta_usuario, $estado_tarjeta, $proceso_registro, $ubicacion_img_bd,$estado_cuenta, $vinculo_institucion]);

            // Preparar el mensaje de éxito de la operación
            $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
            $_SESSION['titulo_n'] = "Datos Guardados Exitosamente";
            $_SESSION['respuesta_n'] = "Los datos del usuario se han guardado correctamente.";

            // Redirigir al formulario de modificacion de datos
            header("location: ../../../paginas/panel control/creacion_usuario_guardar.php");
        } catch (PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }