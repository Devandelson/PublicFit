<?php 
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    if (isset($_POST['institucion'])){
        $institucion = $_POST['institucion'];

        $grupos = $conex->prepare("SELECT * FROM institucion WHERE nombre_institucion = ?");
        $grupos->execute([$institucion]);
        $resultado = $grupos->fetch(PDO::FETCH_ASSOC);
        $resultado = $resultado['grupos'];
        $resultado = explode("," , substr($resultado, 0,-1));

        echo json_encode($resultado);
    }