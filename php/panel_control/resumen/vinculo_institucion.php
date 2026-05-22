<?php
    session_start(); // Iniciar la sesión
    require_once "../../config.php";
    $db = new database();
    $conex = $db->conectar();

    if (isset($_POST['buscar'])){
        $categoria = $_POST['categoria'];

        // cambiando categoria del resumen
        $_SESSION['vinculo_institucion'] = $categoria;
       
        header("location: ../../../paginas/panel control/panel_administrador_inicio.php");
        exit();
    }