<?php 
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    // vallidando usuario
    if (isset($_POST['institucion'])){
       $filtro_institucion = $_POST['institucion'];

       if (empty($_POST['categoria'])){}

       $filtro_categoria = $_POST['categoria'];

       $consulta_datos = $conex->prepare("SELECT * FROM usuario WHERE identificador_institucion = ? AND grupo = ?");
       $consulta_datos->execute([$filtro_institucion, $filtro_categoria]);
       $resultado = $consulta_datos->fetchAll(PDO::FETCH_ASSOC);

       $_SESSION['datos_visualizacion'] = $resultado;
       header("location: ../../../paginas/panel control/datos.php");
    }