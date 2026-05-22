<?php
    session_start(); // Iniciar la sesión
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    if (isset($_POST['id_usuario'])){
        $id_usuario = $_POST['id_usuario'];

        // obteniendo datos del usuario
        $consulta_datos = $conex->prepare("SELECT * FROM registro_recarga WHERE id_usuario = ?");
        $consulta_datos->execute([$id_usuario]);
        $resultado = $consulta_datos->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultado);
    }