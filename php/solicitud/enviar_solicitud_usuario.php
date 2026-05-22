<?php 
    require_once "../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    try{
        if (isset($_POST['nombre'])) {
            // Verificar si alguno de los campos está vacío
            if (empty($_POST['nombre']) 
            || empty($_POST['contraseña']) 
            || empty($_POST['correo']) 
            || empty($_POST['telefono']) 
            || empty($_POST['direccion'])
            || empty($_POST['vinculo_institucion'])
            || empty($_POST['transporte']) || $_POST['transporte'] == "[]"
            ) {
                // Preparar el mensaje de error
                $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
                $_SESSION['respuesta_n'] = "Los datos no se pudieron guardar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../paginas/login/solicitud_usuario.php");
                exit();
            }

            // comprobando contraseñas
            if ($_POST['contraseña'] == $_POST['c_contraseña']){} else {
                // Preparar el mensaje de error
                $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                $_SESSION['titulo_n'] = "Las contraseñas no coinciden. Intenta de nuevo.";
                $_SESSION['respuesta_n'] = "";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../paginas/login/solicitud_usuario.php");
                exit();   
            } 

            $nombre_usuario = $_POST['nombre'];
            $contraseña = $_POST['contraseña'];
            $correo = $_POST['correo'];
            $direccion = $_POST['direccion'];
            $perfil = "usuario";
            $proceso_registro = "incompleto";
            $estado_cuenta = "activa";
            $estado_monto = "...";
            $monto = $_POST['transporte'];

            $vinculo_institucion = $_POST['vinculo_institucion'];
            $grupo = $_POST['grupo'];

            $tipo_solicitud = "usuario";
            $estado_solicitud = "pendiente";
            $telefono = $_POST['telefono'];
            
            $file = $_FILES['imagen_usuario'];

            // conditions of the update image
            if (!empty($_FILES['imagen_usuario']['tmp_name'])){
                // new image
                $ruta_temporal = $file['tmp_name'];
                $ubicacion_temporal = "../../archivos_subidos/";
                $stringAleatorio = substr(uniqid(),0,6);
            
                if ($file['size'] > 500 * 1024){
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Tamaño de Imagen Excedido";
                    $_SESSION['respuesta_n'] = "El archivo de imagen supera el límite permitido de 500KB. Por favor, cargue una imagen que cumpla con las restricciones de tamaño.";        
            
                    header("location: ../../paginas/login/solicitud_usuario.php");
                }
            
                $tipos_admitidos = ["image/png" , "image/jpeg" , "image/jpg"];
                if (!in_array($file['type'] , $tipos_admitidos)){
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Formato de Imagen No Válido";
                    $_SESSION['respuesta_n'] = "Solo se aceptan imágenes en formato JPG, JPEG o PNG. Por favor, cargue un archivo con uno de estos formatos.";            
            
                    header("location: ../../paginas/login/solicitud_usuario.php");
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
                $_SESSION['respuesta_n'] = "Los datos no se pudieron guardar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../paginas/login/solicitud_usuario.php");
                exit();
            }

            // antes de guardar, verificar los datos:
            //-- Correo:
            $consulta_correo = $conex->prepare("SELECT * FROM usuario WHERE correo = ?");
            $consulta_correo->execute([$correo]);

            if ($consulta_correo->rowCount() > 0){
                    // mensaje malo
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Correo ya registrado";
                    $_SESSION['respuesta_n'] = "Ya existe una cuenta asociada a este correo. Por favor, comuníquese con la institución correspondiente para obtener asistencia.";     
    
                    // Redirigir al formulario de modificacion de datos
                    header("location: ../../paginas/login/solicitud_usuario.php");
                    exit();
            } else {}

            // estableciendo grupo aleatorio 
            $vinculo_institucion;
            // -- buscando institucion seleccionada
            $institucion = $conex->prepare("SELECT * FROM institucion WHERE nombre_institucion = ?");
            $institucion->execute([$vinculo_institucion]);
            $result_i = $institucion->fetch(PDO::FETCH_ASSOC);
            $grupos = explode("," , substr($result_i['grupos'], 0, -1));

            // Obtener una clave aleatoria del arreglo
            $claveAleatoria = array_rand($grupos);
            // Obtener el valor correspondiente a la clave aleatoria
            $grupoAleatorio = trim($grupos[$claveAleatoria]);

            // guardando detalles de la solicitud
            $detalle_solicitud = [
                "nombre_usuario" => $nombre_usuario,
                "contrasena" => $contraseña,
                "correo" => $correo,
                "detalle_ruta_usuario" => $direccion,
                "perfil" => "usuario",
                "proceso_registro" => "incompleto",
                "estado_cuenta" => "activa",
                "estado_monto" => "...",
                "monto" => $monto,
                "grupo" => $grupoAleatorio,
                "tipo_solicitud" => "usuario",
                "ultima_fecha_recarga" => "",     
                "telefono" => $telefono,
                "imagen" => $ubicacion_img_bd,
                "vinculo_institucion" => $vinculo_institucion,
                "estado_solicitud" => $estado_solicitud 
            ];   
            
            $detalle_solicitud = json_encode($detalle_solicitud);

            // guardando la solicitud del usuario
            $solicitud = $conex->prepare("INSERT INTO seguimiento_solicitudes(imagen, nombre, tipo_solicitud, direccion, estado_solicitud, detalle_solicitud) VALUES (?,?,?,?,?,?)");

            $solicitud->execute([$ubicacion_img_bd, $nombre_usuario, $tipo_solicitud, $direccion, $estado_solicitud, $detalle_solicitud]);

            // mensaje bueno
            $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
            $_SESSION['titulo_n'] = "Solicitud Enviada Correctamente";
            $_SESSION['respuesta_n'] = "Su solicitud ha sido enviada con éxito. Recibirá un correo con el estado de su solicitud. Le agradecemos su paciencia.";        

            // Redirigir al formulario de modificacion de datos
            header("location: ../../index.php");
            exit();
        }
    } catch (Exception $e){
        echo $e->getMessage();
    }