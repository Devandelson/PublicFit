<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control usuario</title>

    <link rel="icon" href="../../imagenes/logo.ico" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../../css/notificacion.css">
    <link rel="stylesheet" href="../../css/normalize.css">
    <link rel="stylesheet" href="../../css/registro_usuario/stile.css">

        <!-- CSS boostrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- --- Notificacion --- -->
    <!-- funcionalidad de envio de una respuesta a traves de php -->
    <?php if (isset($_SESSION['icono_n'])){ ?>
    <div class="capa_notificacion ani_entrada_notificacion">
        <div class="contenedor_notificacion" id="contenedor_notificacion">
            <div class="encabezado">
                <i class="<?php echo $_SESSION['icono_n'] ?>"></i>
                <p class="titulo"><?php echo $_SESSION['titulo_n'] ?></p>
            </div>
            
            <p class="contenido"><?php echo $_SESSION['respuesta_n'] ?></p>
            <button class="btn1" id="btn_cerrar_notificacion">Okey</button>
        </div>
    </div>
    <?php
        unset($_SESSION['icono_n'], $_SESSION['titulo_n'], $_SESSION['respuesta_n']); 
        } else { ?>
        <div class="capa_notificacion">
            <div class="contenedor_notificacion" id="contenedor_notificacion">
                <div class="encabezado">
                    <i class=""></i>
                    <p class="titulo">Titulo</p>
                </div>
                
                <p class="contenido">Contenido</p>
                <button class="btn1" id="btn_cerrar_notificacion">Aceptar</button>
            </div>
        </div>
    <?php } ?>

    <main>
        <form class="item_registro contenido_registro" method="POST" action="../../php/login/registro.php">
            <div class="contenedor_wave"></div>

            <h1><i class="fa-solid fa-paste"></i> Detalles del usuario</h1>

            <div class="contenedor_casillas">
                <div class="item_casilla">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <textarea placeholder="Detalles de ruta" name="detalles"></textarea>
                </div>
            </div>

            <div class="map"></div>

            <input type="submit" value="Guardar" class="btn_register btn1" name="btn_register">
        </form>

        <div class="item_registro contenedor_img">
            <img src="../../imagenes/IMG_7403.webp" alt="">
        </div>
    </main>

    <!-- Js personalizado -->
    <script src="../../js/notificacion.js"></script>
</body>
</html>
