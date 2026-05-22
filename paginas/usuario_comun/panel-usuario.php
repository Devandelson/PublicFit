<?php
    session_start(); // Iniciar la sesión
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    if (isset($_SESSION['id_usuario'])){
        $id_usuario = $_SESSION['id_usuario'];

        // obteniendo datos del usuario
        $consulta = $conex->prepare("SELECT * from usuario where id_usuario = ?");
        $consulta->execute([$id_usuario]);

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    } else {
        header("location: ../../index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control usuario</title>

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../../css/btn_modo.css">
    <link rel="stylesheet" href="../../css/notificacion.css">
    <link rel="stylesheet" href="../../css/normalize.css">
    <link rel="stylesheet" href="../../css/panel_usuario/style.css">


    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>

    <!-- AOS (Libreria de animacion de entrada) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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

    <!-- Boton de cambiar modo claro o oscuro -->
    <div class="contendor_btn_modo">
        <!-- From Uiverse.io by ClawHack1 --> 
        <div class="toggle-switch">
            <input class="toggle-input" id="toggle" type="checkbox">
            <label class="toggle-label" for="toggle"></label>
        </div>
    </div>
        
    <header class="informacion_basica">
        <img src="../../imagenes/fondo_usuario.svg" alt="fondo_usuario" class="fondo">
        <span class="fondo"></span>

        <!-- Imagen -->
        <img src="<?php echo "../../" . $resultado['imagen'] ?>" alt="foto_usuario" class="perfil">

        <!-- Nombre -->
        <h2><?php echo $resultado['nombre']; ?></h2>
    </header>

    <main>
        <h1>Información de cuenta</h1>

        <div class="subcontenedor_informacion">
            <!-- Información de la tarjeta -->
            <div class="contenedor_info">
                <div class="item_info monto" data-aos="flip-down">
                    <div class="info_monto">
                        <h3>Monto de tarjeta SD Go</h3>
                        
                        <div class="subontenedor">        
                            <p>
                                <!-- Condicion si todavia no se le a colocado un monto si o no -->
                                <!-- para los usuarios nuevos -->
                                <?php if ($resultado['monto'] == "" || $resultado['monto'] == 0){ ?>
                                    <h4>0 DOP</h4>
                                    <i class="fa-solid fa-clock-rotate-left"></i> Peniente
                                <?php } else { ?>
                                    <h4><?php echo $resultado['monto']; ?> DOP</h4>
                                    <i class="fa-solid fa-calendar-days"></i> 
                                    <?php echo $resultado['ultima_fecha_recarga']; ?>
                                <?php } ?>      
                            </p>
                        </div>
                    </div>

                    <div class="img">
                        <img src="../../imagenes/ilustracion_dinero_sin_fondo.png" alt="">
                    </div>
                </div>

                <!-- Btn cerrar sesion -->
                <div class="control_cuenta">
                    <div class="btn_cerrar_sesion btn1">
                        <a href="../../php/login/cerrar_sesion.php">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>

            <!-- Datos modificables -->
            <form method="post" action="button_actualizar.php" class="mas_detalle_cuenta">
                <div class="contenedor_casillas" data-aos="fade-up">
                    <div class="item_casilla">
                        <p><i class="fa-regular fa-envelope"></i> Correo Electronico</p>
                        <input type="text" name="correo" value="<?php echo $resultado['correo']; ?>">
                    </div>

                    <div class="item_casilla">
                        <p><i class="fa-solid fa-map-location-dot"></i> Detalle de ruta</p>
                        <textarea name="detalle_ruta_usuario"><?php echo $resultado['detalle_ruta_usuario']; ?></textarea>
                    </div>
                </div>

                <div class="contenedor_mapa" data-aos="fade-down">
                    <div class="item_mapa mapa">
                        <img src="../../imagenes/mapa.jpg" alt="">
                    </div>

                    <div class="item_mapa detalle_mapa">
                        <p>Colina los doctores CTC.</p>
                        <button type="submit" class="btn_update btn1">Actualizar datos</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </main>

    <!-- AOS (Libreria de animacion de entrada) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        AOS.init();
    </script>

    <script src="../../js/notificacion.js"></script>
    <script src="../../js/btn_mode.js"></script>
</body>
</html>