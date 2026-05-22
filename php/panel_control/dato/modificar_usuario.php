<?php 
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    if (isset($_POST['btn_modificar'])){
        try{
                // Verificar si alguno de los campos está vacío
                if (empty($_POST['nombre']) || empty($_POST['contraseña']) || empty($_POST['correo']) || empty($_POST['imagen_antigua']) || empty($_POST['grupo']) || empty($_POST['proceso_registro']) || empty($_POST['id_usuario']) 
                || empty($_POST['estado_tarjeta']) ) {
                    // Preparar el mensaje de error
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
                    $_SESSION['respuesta_n'] = "Los datos no se pudieron modificar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

                    // Redirigir al formulario de modificacion de datos
                    header("location: ../../../paginas/panel control/creacion_usuario_modificar.php");
                    exit();
                }

                // datos
                $id_usuario = $_POST['id_usuario'];
                $nombre_usuario = $_POST['nombre'];
                $contraseña = $_POST['contraseña'];
                $correo = $_POST['correo'];
                $monto = (!empty($_POST['monto'])) ? $_POST['monto'] : NULL;
                $grupo = $_POST['grupo'];
                $detalle_ruta_usuario = $_POST['detalle_ruta_usuario'];
                $ultima_fecha_recarga = date("Y-m-d");
                $perfil = "usuario";
                $proceso_registro = $_POST['proceso_registro'];
                $comentario = $_POST['comentario_estado_cuenta'];
                $estado_cuenta = $_POST['estado_cuenta'];
                $estado_tarjeta = $_POST['estado_tarjeta'];
                
                $estado_monto = "Bien";

                $file = $_FILES['imagen_nueva'];
                $imagen_antigua = $_POST['imagen_antigua'];

                // conditions of the update image
                if (!empty($_FILES['imagen_nueva']['tmp_name'])){
                    // new image
                    $ruta_temporal = $file['tmp_name'];
                    $ubicacion_temporal = "../../../archivos_subidos/";
                    $stringAleatorio = substr(uniqid(),0,6);
                
                    if ($file['size'] > 1 * 1024){
                        $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                        $_SESSION['titulo_n'] = "Tamaño de Imagen Excedido";
                        $_SESSION['respuesta_n'] = "El archivo de imagen supera el límite permitido de 500KB. Por favor, cargue una imagen que cumpla con las restricciones de tamaño.";        
                
                        header("location: ../../../paginas/panel control/creacion_usuario_modificar.php");
                    }
                
                    $tipos_admitidos = ["image/png" , "image/jpeg" , "image/jpg"];
                    if (!in_array($file['type'] , $tipos_admitidos)){
                        $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                        $_SESSION['titulo_n'] = "Formato de Imagen No Válido";
                        $_SESSION['respuesta_n'] = "Solo se aceptan imágenes en formato JPG, JPEG o PNG. Por favor, cargue un archivo con uno de estos formatos.";            
                
                        header("location: ../../../paginas/panel control/creacion_usuario_modificar.php");
                    }

                    // eliminar imagen antigua
                    if (file_exists("../../../" . $imagen_antigua)){
                        unlink("../../../" . $imagen_antigua);
                    }
                    
                    // subir imagen nueva
                    $nombre = $stringAleatorio . $file['name'];
                    move_uploaded_file($ruta_temporal, $ubicacion_temporal . $nombre);
                    $ubicacion_img_bd = "archivos_subidos/" . $nombre;
                } else if (!empty($imagen_antigua)){
                    // old image
                    $ubicacion_img_bd = $imagen_antigua;
                } else {
                    // nothing image
                    // Preparar el mensaje de error
                    $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
                    $_SESSION['titulo_n'] = "Campos Requeridos Vacíos";
                    $_SESSION['respuesta_n'] = "Los datos no se pudieron modificar debido a campos vacíos. Por favor, complete todos los campos requeridos antes de guardar los datos. Gracias.";

                    // Redirigir al formulario de modificacion de datos
                    header("location: ../../../paginas/panel control/creacion_usuario_modificar.php");
                    exit();
                }

                // ahora se procede a modificar los datos
                $consulta = $conex->prepare("UPDATE usuario
                    SET
                    nombre = ?,
                    correo = ?,
                    contrasena = ?,
                    monto = ?,
                    ultima_fecha_recarga = ?,
                    perfil = ?,
                    grupo = ?,
                    detalle_ruta_usuario = ?,
                    estado_monto = ?,
                    proceso_registro = ?,
                    imagen = ?,
                    estado_cuenta = ?
                    WHERE id_usuario = ?;
                ");

                

                $consulta->execute([$nombre_usuario, $correo, $contraseña, $monto, $ultima_fecha_recarga, $perfil, $grupo, $detalle_ruta_usuario, $estado_tarjeta, $proceso_registro, $ubicacion_img_bd,$estado_cuenta, $id_usuario]);

                // -------- ahora guardando cambios de monto de tarjeta SD Go

                // pero antes de verificar si se desea guardar en el historial de recargas
                $validacion_recarga = $_POST['validacion_recarga'];

                if ($validacion_recarga == "si"){
                    $consulta_recarga = $conex->prepare("INSERT INTO registro_recarga (
                        grupo,
                        id_usuario,
                        fecha_recarga,
                        monto,
                        identificador_institucion
                        ) VALUES (?, ?, ?, ?, ?);
                    ");
                    $consulta_recarga->execute([$grupo, $id_usuario, $ultima_fecha_recarga, $monto, $_SESSION['nombre_usuario']]);
                }

                // antes de guardar el motivo primero evaluar si ya tiene registros para no duplicar el id
                if ($estado_cuenta == "inactiva"){   
                    // -- datos
                    $fecha = date("d/m/Y");

                    $detectar_registros = $conex->prepare("SELECT * FROM historial_inactivo WHERE id_user = ?");
                    $detectar_registros->execute([$id_usuario]);
                    $result_deteccion = $detectar_registros->fetch(PDO::FETCH_ASSOC);

                    if ($detectar_registros->rowCount() > 0){
                        // modificando motivos de por que se desactivo la cuenta
                        $guardar_motivos = $conex->prepare("UPDATE historial_inactivo SET 
                        motivo = ?, fecha = ? WHERE id_user = ?");
                        $guardar_motivos->execute([$comentario, $fecha, $id_usuario]);
                    } else {
                        // guardando motivos de por que se desactivo la cuenta
                        $guardar_motivos = $conex->prepare("INSERT INTO historial_inactivo(motivo, id_user, fecha) VALUES (?,?,?)");
                        $guardar_motivos->execute([$comentario, $id_usuario, $fecha]);
                    }
                }


                // Preparar el mensaje de éxito de la operación
                $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
                $_SESSION['titulo_n'] = "Datos Modificados Exitosamente";
                $_SESSION['respuesta_n'] = "Los datos del usuario se han modificado correctamente. Puede verificar los cambios en la visualización de datos o visualización de cuentas inactivas.";

                // Redirigir al formulario de modificacion de datos
                header("location: ../../../paginas/panel control/creacion_usuario_modificar.php");
                $_SESSION['finalizacion_proceso'] = true;
        } catch (PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }
