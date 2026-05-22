<?php 
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    // comprobando datos, envieados por el metodo POST
    if (isset($_POST['id_user']) && !empty($_POST['id_user'])) {
        $id_user = $_POST['id_user'];

        // primero filtrando los datos del estudiante, para eliminar cosas que estan en otro lado:
        $datos_usuario = $conex->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $datos_usuario->execute([$id_user]);
        $datos_resultado = $datos_usuario->fetch(PDO::FETCH_ASSOC);

        // -- Eliminando imagen
        $ruta_imagen = $datos_resultado['imagen'];
        if (file_exists("../../../" . $ruta_imagen)){
            unlink("../../../" . $ruta_imagen);
        }

        // eliminando recargas
        $eliminando_recarga = $conex->prepare("DELETE FROM registro_recarga WHERE id_usuario = ?");
        $eliminando_recarga->execute([$id_user]);

        // Ahora eliminando usuario
        $consulta = $conex->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $consulta->execute([$id_user]);
    }
?>