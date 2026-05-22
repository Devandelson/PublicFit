<?php 
    require_once "../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    //Load Composer's autoloader
    require '../PHPMailer/Exception.php';
    require '../PHPMailer/PHPMailer.php';
    require '../PHPMailer/SMTP.php';

    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\PHPMailer;


    try{
        if (isset($_POST['id_solicitud'])){
            $id_solicitud = $_POST['id_solicitud'];
            $estado_solicitud = $_POST['estado_solicitud'];
            $direccion = $_POST['direccion'];

            // ---- recuperando datos para futuras acciones:
            $consulta_datos = $conex->prepare("SELECT * FROM seguimiento_solicitudes WHERE id_solicitud = ?");
            $consulta_datos->execute([$id_solicitud]);
            $resultado_datos = $consulta_datos->fetch(PDO::FETCH_ASSOC);

            // de parte de la insticion
            if ($resultado_datos['tipo_solicitud'] == "institucion"){       
                $detalle_solicitud = json_decode($resultado_datos['detalle_solicitud'], true);

                // guardando datos, si ya se confirmo la solicitud
                $guardar_datos = $conex->prepare("INSERT INTO `institucion`
                (`nombre_institucion`,
                `contraseña_institucion`,
                `correo`,
                `telefono`,
                `estado_registro`,
                `direccion`,
                `tipo_institucion`,
                `grupos`,
                imagen
                )
                VALUES
                (?,?,?,?,?,?,?,?,?)
                ");

                // antes de mandar un mensaje de correo electronico  
                if ( $estado_solicitud  != "pendiente") {                
                    /* -- variables -- */
                    $asunto = "🎉 ¡Felicidades! Tu solicitud ha sido aprobada en Fimovit 🚀";

                    $mensaje = ' 
                    <div style="font-family: Arial, sans-serif; max-width: 600px; padding: 20px; border-radius: 10px; background: #f4f4f4;">
                        <h2 style="color: #000;">Hola <strong>' .  $detalle_solicitud['nombre_usuario']. '</strong>,</h2>
                        <p style="font-size: 16px;">Te damos la bienvenida a <strong>CodeTrackers</strong>.</p>
                        <p style="font-size: 18px; font-weight: bold;">Su cuenta se ha creado satisfactoriamente.</p>
                        <p>Para acceder a la misma, su contraseña asignada es: <strong>' .  $detalle_solicitud['contrasena'] . '</strong></p>
                        <p>Por favor, cambia tu contraseña luego de haber iniciado sesión.</p>
                        <br>
                        <a href="http://localhost:8080/index.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Iniciar sesión</a>
                        <br><br>
                        <p>Saludos,<br><strong>El equipo</strong></p>
                    </div>'; 

                    $estado_registro = "completo";

                    $guardar_datos->execute([$detalle_solicitud['nombre_usuario'], $detalle_solicitud['contrasena'],
                    $detalle_solicitud['correo'],
                    $detalle_solicitud['telefono'],
                    $estado_registro,
                    $detalle_solicitud['direccion'],
                    $detalle_solicitud['tipo_institucion'],
                    $detalle_solicitud['grupo'],
                    $detalle_solicitud['imagen']]);
                } else {                    
                    // antes de mandar un mensaje de correo electronico   
                    /* -- variables -- */
                    $asunto = "ℹ️ Información sobre tu solicitud en Fimovit";

                    $mensaje = ' 
                    <div style="font-family: Arial, sans-serif; max-width: 600px; padding: 20px; border-radius: 10px; background: #f4f4f4;">
                        <h2 style="color: #000;">Hola <strong>' .  $detalle_solicitud['nombre_usuario'] . '</strong>,</h2>
                        <p style="font-size: 16px;">Gracias por tu interés en <strong>Fimovit</strong>.</p>
                        <p style="font-size: 18px; font-weight: bold;">Lamentamos informarte que, tras revisar tu solicitud, en esta ocasión no ha sido aprobada.</p>
                        <p>Si deseas obtener más información o realizar una nueva solicitud en el futuro, estaremos encantados de atenderte.</p>
                        <br>
                        <p>Saludos cordiales,<br><strong>El equipo de Fimovit</strong></p>
                    </div>'; 
                }

                // mensaje bueno
                $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
                $_SESSION['titulo_n'] = "Estado de la Solicitud Editado Correctamente";
                $_SESSION['respuesta_n'] = "El estado de la solicitud ha sido editado con éxito. Los cambios se han guardado correctamente. Gracias por su gestión.";   

                // Eliminando solicitud tras la decisión
                $consulta_cambios = $conex->prepare("DELETE FROM seguimiento_solicitudes WHERE id_solicitud = ?");
                $consulta_cambios->execute([$id_solicitud]);

                //--- mandar un correo   

                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);
            
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'Anderson08062006@gmail.com';                     //SMTP username
                $mail->Password   = 'sptfxytkafnvmspd';                               //SMTP password
                $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
                $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            
                //Recipients
                $mail->setFrom('Anderson08062006@gmail.com', 'Codetrakers Corporation');
                $mail->addAddress($detalle_solicitud['correo']);     //Add a recipient
            
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );  

                //Content
                $mail->isHTML(true);   //Set email format to HTML
                $mail->CharSet = 'UTF-8';
            
                $mail->Subject = $asunto;
                $mail->Body    = $mensaje;

                $mail->send();

                // Redirigir al formulario de modificacion de datos
                header("location: ../../paginas/panel control/seguimineto_de_solicitud.php");
                exit();
            }

            // --------- De parte del (Usuario)
            if ($estado_solicitud == "aprobado" && $resultado_datos['tipo_solicitud'] == "usuario"){       
                $detalle_solicitud = json_decode($resultado_datos['detalle_solicitud'], true);

                if ( $estado_solicitud  != "pendiente") {
                    // guardando datos, si ya se confirmo la solicitud
                    $guardar_datos = $conex->prepare("INSERT INTO `usuario`
                    (`nombre`,
                    `correo`,
                    `contrasena`,
                    `monto`,
                    `ultima_fecha_recarga`,
                    `perfil`,
                    `grupo`,
                    `detalle_ruta_usuario`,
                    `estado_monto`,
                    `proceso_registro`,
                    `imagen`,
                    `estado_cuenta`,
                    identificador_institucion
                    )
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

                    $estado_registro = "completo";

                    // estableciendo monto
                    // base de datos de costes
                    $consulta_transporte = $conex->prepare("SELECT * FROM transporte");
                    $consulta_transporte->execute();
                    $resultado_t = $consulta_transporte->fetchAll(PDO::FETCH_ASSOC);

                    // Decodificar el JSON de monto
                    $monto = json_decode($detalle_solicitud['monto'], true);

                    $monto_total = 0;
                    foreach ($monto as $row) {
                        $nombre = $row[0];  // Nombre del transporte
                        $cantidad = (int) $row[1]; // Cantidad de viajes

                        foreach ($resultado_t as $item) {
                            if ($item['nombre'] == $nombre) {
                                $monto_total += $cantidad * $item['coste'];
                                break; // No es necesario seguir buscando si ya lo encontramos
                            }
                        }
                    }

                    $monto_total *= 20; // Multiplicación final

                    $direccion_completa = "Desde " . $detalle_solicitud['detalle_ruta_usuario'] . "\n siga la siguiente ruta: " . $direccion;

                    // Ejecutar la consulta con los valores de $detalle_solicitud
                    $guardar_datos->execute([
                        $detalle_solicitud['nombre_usuario'],       // nombre
                        $detalle_solicitud['correo'],               // correo
                        $detalle_solicitud['contrasena'],           // contrasena
                        $monto_total,                // monto
                        $detalle_solicitud['ultima_fecha_recarga'], // ultima_fecha_recarga (no proporcionado, usar valor predeterminado si es necesario)
                        $detalle_solicitud['perfil'],               // perfil
                        $detalle_solicitud['grupo'],                // grupo
                        $direccion_completa, // detalle_ruta_usuario (no proporcionado, usar valor predeterminado si es necesario)
                        $detalle_solicitud['estado_monto'],         // estado_monto (no proporcionado, usar valor predeterminado si es necesario)
                        $estado_registro,                           // proceso_registro
                        $detalle_solicitud['imagen'],               // imagen
                        $detalle_solicitud['estado_cuenta'],         // estado_cuenta
                        $detalle_solicitud['vinculo_institucion']         // estado_cuenta
                    ]);
                    //--- mandar un correo   

                    // antes de mandar un mensaje de correo electronico   
                    /* -- variables -- */
                    $asunto = "🎉 ¡Felicidades! Tu solicitud ha sido aprobada en " . $detalle_solicitud['vinculo_institucion'] . " 🚀";

                    $mensaje = ' 
                    <div style="font-family: Arial, sans-serif; max-width: 600px; padding: 20px; border-radius: 10px; background: #f4f4f4;">
                        <h2 style="color: #000;">Hola <strong>' .  $detalle_solicitud['nombre_usuario']. '</strong>,</h2>
                        <p style="font-size: 16px;">Te damos la bienvenida a <strong>CodeTrackers</strong>.</p>
                        <p style="font-size: 18px; font-weight: bold;">Su cuenta se ha creado satisfactoriamente.</p>
                        <p>Para acceder a la misma, su contraseña asignada es: <strong>' .  $detalle_solicitud['contrasena'] . '</strong></p>
                        <p>Por favor, cambia tu contraseña luego de haber iniciado sesión.</p>
                        <br>
                        <a href="http://localhost:8080/index.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Iniciar sesión</a>
                        <br><br>
                        <p>Saludos,<br><strong>El equipo</strong></p>
                    </div>';            
                } else {
                    /* -- variables -- */
                    $asunto = "ℹ️ Información sobre tu solicitud en " . $detalle_solicitud['vinculo_institucion'];

                    $mensaje = ' 
                    <div style="font-family: Arial, sans-serif; max-width: 600px; padding: 20px; border-radius: 10px; background: #f4f4f4;">
                        <h2 style="color: #000;">Hola <strong>' .  $detalle_solicitud['nombre_usuario'] . '</strong>,</h2>
                        <p style="font-size: 16px;">Gracias por tu interés en <strong>' . $detalle_solicitud['vinculo_institucion'] . '</strong>.</p>
                        <p style="font-size: 18px; font-weight: bold;">Lamentamos informarte que, tras revisar tu solicitud, en esta ocasión no ha sido aprobada.</p>
                        <p>Apreciamos tu tiempo y esfuerzo, y te invitamos a intentarlo nuevamente en el futuro si lo deseas.</p>
                        <br>
                        <p>Si necesitas más información, no dudes en contactarnos.</p>
                        <br>
                        <p>Saludos cordiales,<br><strong>El equipo de ' . $detalle_solicitud['vinculo_institucion'] . '</strong></p>
                    </div>';
                }
                 
            
                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);
            
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'Anderson08062006@gmail.com';                     //SMTP username
                $mail->Password   = 'sptfxytkafnvmspd';                               //SMTP password
                $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
                $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            
                //Recipients
                $mail->setFrom('Anderson08062006@gmail.com', 'Codetrakers Corporation');
                $mail->addAddress($detalle_solicitud['correo']);     //Add a recipient
            
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );  

                //Content
                $mail->isHTML(true);   //Set email format to HTML
                $mail->CharSet = 'UTF-8';
            
                $mail->Subject = $asunto;
                $mail->Body    = $mensaje;

                $mail->send();              

                // Eliminando solicitud tras la decisión
                $consulta_cambios = $conex->prepare("DELETE FROM seguimiento_solicitudes WHERE id_solicitud = ?");
                $consulta_cambios->execute([$id_solicitud]);

                // Redirigir al formulario de modificacion de datos
                header("location: ../../paginas/panel control/seguimineto_de_solicitud.php");
                exit();
            }
        }

    } catch (Exception $e){
        echo $e->getMessage();
    }