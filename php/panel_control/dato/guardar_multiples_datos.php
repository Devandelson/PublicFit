<?php
session_start();
require_once "../../config.php";
require '../../../vendor/autoload.php'; // Asegúrate de que PHPMailer está instalado y cargado correctamente

require '../../PHPMailer/Exception.php';
require '../../PHPMailer/PHPMailer.php';
require '../../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir la librería PhpSpreadsheet (asegúrate de que el autoload esté disponible)
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$db = new database();
$conex = $db->conectar();

if (isset($_FILES['archivo'])) {
    // Verificar que el archivo es un Excel
    $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($_FILES['archivo']['tmp_name']);
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);

    // Cargar el archivo Excel
    $filePath = $_FILES['archivo']['tmp_name'];
    $spreadsheet = $reader->load($filePath);

    // Obtener la primera hoja del archivo
    $sheet = $spreadsheet->getActiveSheet();

    // Obtener los encabezados de las columnas (primera fila)
    $headers = [];
    foreach ($sheet->getRowIterator(1, 1) as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        foreach ($cellIterator as $cell) {
            $headers[] = $cell->getValue();
        }
    }

    // Leer las filas de datos, empezando desde la segunda fila
    $datos = [];
    foreach ($sheet->getRowIterator(2) as $row) {
        // Evitar filas vacías
        $rowData = [];
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $i = 0;
        $emptyRow = true;  // Variable para verificar si la fila está vacía

        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            $rowData[$headers[$i]] = $value;
            if (!empty($value)) {
                $emptyRow = false; // Si encontramos algún valor no vacío, marcamos la fila como no vacía
            }
            $i++;
        }

        // Solo agregar la fila si no está vacía
        if (!$emptyRow) {
            $datos[] = $rowData;
        }
    }

    // Convertir los datos a JSON para enviarlos al cliente
    $usuarios_registrados = 0; // Contador de usuarios registrados

    // bucle de validacion de datos
    foreach ($datos as $row) {
        // Validar que los datos no estén vacíos
        if (empty($row['nombre']) || empty($row['correo']) || empty($row['perfil']) || empty($row['grupo']) || empty($row['proceso_registro']) || empty($row['identificador_institucion'])) {
            // datos no vacíos.
            $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
            $_SESSION['titulo_n'] = "Error al Guardar los Datos";
            $_SESSION['respuesta_n'] = "El usuario ". $row['nombre'] . " tiene algún campo vacío. Por favor, verifique y ajuste el archivo antes de intentarlo nuevamente.";
            
            header("location: ../../../paginas/panel control/datos.php");
            exit();            
        }

        $nombre_usuario = $row['nombre'];
        $contraseña = substr(uniqid(), 0, 6);
        $correo = $row['correo'];
        $monto = NULL;
        $grupo = $row['grupo'];
        $detalle_ruta_usuario = NULL;
        $ultima_fecha_recarga = NULL;
        $perfil = "usuario";
        $proceso_registro = $row['proceso_registro'];
        $identificador_institucion = $row['identificador_institucion'];
        $estado_cuenta = "activa";

        $detalle_ruta_api = "";
        $estado_monto = "...";

        $detalle_correos = "";

        // -- verificando datos
        // correo:
        $consulta_correo = $conex->prepare("SELECT * FROM usuario where correo = ?");
        $consulta_correo->execute([$correo]);

        if ($consulta_correo->rowCount() > 0) {
            // el correo ya esta en uso
            $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
            $_SESSION['titulo_n'] = "Error al Guardar los Datos";
            $_SESSION['respuesta_n'] = "Este correo ($correo) ya existen en el sistema. Por favor, verifique y ajuste el archivo antes de intentarlo nuevamente.";

            header("location: ../../../paginas/panel control/datos.php");
            exit();
        }

        // -- verificar institucion
        $consulta_institucion = $conex->prepare("SELECT * FROM institucion where nombre_institucion = ?");
        $consulta_institucion->execute([$identificador_institucion]);

        if ($consulta_institucion->rowCount() > 0) {
        } else {
            // institucion no existe.
            $_SESSION['icono_n'] = "fa-solid fa-circle-xmark";
            $_SESSION['titulo_n'] = "Error al Guardar los Datos";
            $_SESSION['respuesta_n'] = "El usuario ($nombre_usuario) no está vinculado a la institución o empresa registrada en el sistema. Por favor, verifique y ajuste el archivo antes de intentarlo nuevamente.";
            
            header("location: ../../../paginas/panel control/datos.php");
            exit();            
        }
    }

    // bucle de guardar los datos
    foreach ($datos as $row) {
        $nombre_usuario = $row['nombre'];
        $contraseña = substr(uniqid(), 0, 6);
        $correo = $row['correo'];
        $monto = NULL;
        $grupo = $row['grupo'];
        $detalle_ruta_usuario = NULL;
        $ultima_fecha_recarga = NULL;
        $perfil = "usuario";
        $proceso_registro = $row['proceso_registro'];
        $identificador_institucion = $row['identificador_institucion'];
        $estado_cuenta = "activa";

        $estado_monto = "...";

        // Insertar en la base de datos con manejo de errores
        try {
            $consulta = $conex->prepare("INSERT INTO `usuario`
            (
                `nombre`,
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
                `identificador_institucion`
            ) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Validar si la imagen existe
            $imagen = "archivos_subidos/avatar.png"; 

            $consulta->execute([
                $nombre_usuario,
                $correo,
                $contraseña,
                $monto,
                $ultima_fecha_recarga,
                $perfil,
                $grupo,
                $detalle_ruta_usuario,
                $estado_monto,
                $proceso_registro,
                $imagen,
                $estado_cuenta,
                $identificador_institucion,
            ]);


            // Enviar correo al usuario
            // antes de mandar un mensaje de correo electronico  
            if (enviarCorreo($correo, $nombre_usuario, $contraseña, $identificador_institucion)){} else {
                $detalle_correos_erroneos .= "● " . $correo . "<br>";
            }
        } catch (Exception $e) {
            echo "Error al registrar usuario con correo $correo: " . $e->getMessage() . "<br>";
        }
    }

    $_SESSION['icono_n'] = "fa-solid fa-circle-check icono_activo";
    $_SESSION['titulo_n'] = "Usuarios Registrados Correctamente";  

    if (isset($detalle_correos_erroneos)){
        $_SESSION['respuesta_n'] = "Todos los usuarios han sido registrados correctamente en el sistema. <br> Detalles de correos errores: <br>" . $detalle_correos_erroneos;  
    } else {
        $_SESSION['respuesta_n'] = "Todos los usuarios han sido registrados correctamente en el sistema.";  
    }

    header("location: ../../../paginas/panel control/datos.php");
    exit();
}

function enviarCorreo($destinatario, $nombre, $contraseña, $institucion)
{
    $mail = new PHPMailer(true);

    try {
        /* -- variables -- */
        $asunto = "🎉 ¡Felicidades! Tu solicitud ha sido aprobada en " . $institucion . " 🚀";

        $mensaje = ' 
        <div style="font-family: Arial, sans-serif; max-width: 600px; padding: 20px; border-radius: 10px; background: #f4f4f4;">
            <h2 style="color: #000;">Hola <strong>' .  $nombre. '</strong>,</h2>
            <p style="font-size: 16px;">Te damos la bienvenida a <strong>' . $institucion . '</strong>.</p>
            <p style="font-size: 18px; font-weight: bold;">Su cuenta se ha creado satisfactoriamente.</p>
            <p>Para acceder a la misma, su contraseña asignada es: <strong>' .  $contraseña . '</strong></p>
            <p>Por favor, cambia tu contraseña luego de haber iniciado sesión.</p>
            <br>
            <a href="http://localhost:8080/index.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Iniciar Sesión</a>
            <br><br>
            <p>Saludos,<br><strong>El equipo</strong></p>
        </div>';              
    
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
    
        //Server settings
        $mail->SMTPDebug = 0;               //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'Anderson08062006@gmail.com';                     //SMTP username
        $mail->Password   = 'sptfxytkafnvmspd';                               //SMTP password
        $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('Anderson08062006@gmail.com', 'Codetrakers Corporation');
        $mail->addAddress($destinatario);     //Add a recipient
    
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
        return true;
    } catch (Exception $e) {
        return false;
    }
}
