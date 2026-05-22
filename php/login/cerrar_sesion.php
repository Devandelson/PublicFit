<?php 
    session_start();
    unset( $_SESSION['nombre_usuario'],  $_SESSION['perfil_usuario'],  $_SESSION['id_usuario'],
    $_SESSION['imagen_usuario'],$_SESSION['datos_visualizacion'],$_SESSION['vinculo_institucion']);
    header("location: ../../index.php");
