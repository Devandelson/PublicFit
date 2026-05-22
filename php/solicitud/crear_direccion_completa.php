<?php 
    require_once "../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    if (isset($_POST['id_solicitud'])){
        $id_solicitud = $_POST['id_solicitud'];

        // -- buscar la dirección del usuario
        $consulta_datos_direccion_usuario = $conex->prepare("SELECT * FROM seguimiento_solicitudes WHERE id_solicitud = ?");
        $consulta_datos_direccion_usuario->execute([$id_solicitud]);
        $result_datos = $consulta_datos_direccion_usuario->fetch(PDO::FETCH_ASSOC);

        // Validar el tipo de solicitud
        if ($result_datos['tipo_solicitud'] == "institucion"){
            $respuesta = [
                "tipo_solicitud" => "institucion"
            ];            
            echo json_encode($respuesta);
        } else {
            // extraer info (usuario)
            $detalle_solicitud = json_decode($result_datos['detalle_solicitud'], true);

            // Verificar si existen las claves 'direccion' e 'institucion'
            $direccion_usuario = $detalle_solicitud['detalle_ruta_usuario'];
            $institucion = $detalle_solicitud['vinculo_institucion'];

            // Obtener dirección de la institución solo si se dispone del nombre
            $direccion_institucion = '';
            if (!empty($institucion)) {
                $datos_institucion = $conex->prepare("SELECT * FROM institucion WHERE nombre_institucion = ?");
                $datos_institucion->execute([$institucion]);
                $result_datos_ins = $datos_institucion->fetch(PDO::FETCH_ASSOC);
                if ($result_datos_ins && isset($result_datos_ins['direccion'])) {
                    $direccion_institucion = $result_datos_ins['direccion'];
                }
            }

            $respuesta = [
                "tipo_solicitud" => "usuario",
                "direccion_user" => $direccion_usuario,
                "direccion_ins" => $direccion_institucion,
                "hola" =>  $detalle_solicitud['detalle_ruta_usuario']
            ];
            echo json_encode($respuesta);
        }
    }
?>
