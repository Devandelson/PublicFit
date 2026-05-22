<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIMOVIT | login</title>

    <link rel="icon" href="imagenes/logo.ico" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/notificacion.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/registro_usuario/stile.css">
    <link rel="stylesheet" href="css/login/estilo.css">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>

    <!-- Sweetalert2 for alert message -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
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
        <form class="item_registro contenido_registro" method="POST" action="php/login/controlador.php">
            <div class="contenedor_wave">
            </div>
            
            <h1>
                <img src="imagenes/logo.png" alt="">
                Inicio de sesión
            </h1>

            <div class="contenedor_casillas">
                <div class="item_casilla">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="text" name="correo" placeholder="Correo electrónico" required>
                </div>
                <div class="item_casilla">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                </div>
            </div>

            <input type="submit" value="Iniciar" class="btn_register btn1" name="btn_register">

            <div class="enlaces">
                <a href="paginas/login/solicitud_usuario.php">
                    <i class="fa-solid fa-users-rectangle"></i>
                    Solicitud de acceso para un usuario
                </a>
                <a href="paginas/login/solicitud_institucion.php">
                    <i class="fa-solid fa-building-user"></i>
                    Solicitud de acceso para una institución
                </a>
            </div>
        </form>

        <div class="item_registro contenedor_img">
            <img src="imagenes/IMG_7403.webp" alt="">
        </div>
    </main>

    <!-- Js personalizado -->
    <script src="js/notificacion.js"></script>

    <!-- Sweetalert2 for alert message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</body>
</html>