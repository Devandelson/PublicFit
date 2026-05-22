<?php
    session_start();
    
    unset($_SESSION['id_usuario'], $_SESSION['finalizacion_proceso']);
    header("location: ../../../paginas/panel control/datos.php");