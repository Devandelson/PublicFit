<?php 
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    // validando usuario
    if (isset($_SESSION['imagen_usuario'])){
        $imagen_usuario = $_SESSION['imagen_usuario'];
        $perfil_usuario = $_SESSION['perfil_usuario'];
        $nombre_usuario = $_SESSION['nombre_usuario'];
    } else {
        header("location: ../../index.php");
        exit();
    }

    // validando información enviada para modificar.
    if (isset($_GET['id_dato']) || isset($_SESSION['id_dato'])){
        $_SESSION['id_dato'] = (isset($_GET['id_dato'])) 
        ? $_GET['id_dato'] : $_SESSION['id_dato'];

        $id_user = $_SESSION['id_dato'];

        // para luego filtrar los datos, mediante el id.
        $consulta_datos = $conex->prepare("SELECT * FROM seguimiento_solicitudes where id_solicitud = ?");
        $consulta_datos->execute([$id_user]);
        $resultado_datos = $consulta_datos->fetch(PDO::FETCH_ASSOC);
    } else {
        // si no se encontro ningun dato enviado, enviarlo devuelta a datos.
        header("location: datos.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../css/normalize.css">

    <link rel="icon" href="../../imagenes/logo.ico" type="image/x-icon">
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../../css/notificacion.css">
    <link rel="stylesheet" href="../../css/btn_modo.css">
    <link rel="stylesheet" href="../../css/panel_administrador/estructura.css">
    <link rel="stylesheet" href="../../css/panel_administrador/menu_vertical.css">
    <link rel="stylesheet" href="../../css/panel_administrador/contenedor_frm.css">
    <link rel="stylesheet" href="../../css/panel_administrador/creacion_usuario.css">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>
    <title>Panel del administrador inicio</title>

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
                <textarea id="input_notificacion" placeholder="Descripción"></textarea>

                <div class="controles">
                    <button class="btn1" id="btn_cerrar_notificacion_dato">Okey</button>
                    <button class="btn1" id="btn_cerrar_notificacion">Cancelar</button>
                </div>
            </div>
        </div>
    <?php } ?>

    <main>
        <!-- Boton de cambiar modo claro o oscuro -->
        <div class="contendor_btn_modo">
            <!-- From Uiverse.io by ClawHack1 --> 
            <div class="toggle-switch">
                <input class="toggle-input" id="toggle" type="checkbox">
                <label class="toggle-label" for="toggle"></label>
            </div>
        </div>

        <!-- Boton de quitar menu (en la parte del responsive) -->
        <input type="checkbox" id="btn_menu">
        <span class="capa_menu">
            <label for="btn_menu" class="btn2 btn_menu">
                <div class="button_top">
                    X
                </div>
            </label>
        </span>
        
        <nav class="menu_vertical">
            <section class="logo">
                <img src="../../imagenes/logo.png" alt="">
                <h1>APP DE RUTAS</h1>

                <hr>
            </section>

            <!-- Diferentes opciones de visualización de areas de la app -->
            <ul>
                <li class="item_menu item_bloqueado">
                    <a href="panel_administrador_inicio.php">
                        <i class="fa-solid fa-wallet"></i> Resumen
                    </a>
                </li>
                
                <label class="item_menu" id="btn_menu_desplegable">
                    <input type="checkbox" id="btn_menu_desplegable">
                    <div class="contenedor">
                        <div class="titulo">
                            <i class="fa-solid fa-user-group"></i> 
                                Usuarios 
                            <i class="ani1_icon fa-solid fa-caret-down"></i>
                        </div>
   
                        <ul>
                            <li><a href="datos.html" class="item_bloqueado">Visualización</a></li>    
                            <li><a href="datos_inactivos.php" class="item_bloqueado">Cuentas canceladas</a></li>               
                        </ul>
                    </div>
                </label>
            </ul>

            <section class="otras_opciones">
                <div class="subcontenedor">
                    <hr>
                    <button class="btn_cerrar_sesion">
                        <a href="../../php/login/cerrar_sesion.php">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
                        </a>
                    </button>
                </div>

                <!-- Datos del usuario -->
                <div class="datos_usuario">
                    <img src="<?php echo "../../" . $imagen_usuario ?>" alt="">
                    <div class="info_usuario">
                        <h3><?php echo $nombre_usuario ?></h3>
                        <h4><?php echo $perfil_usuario ?></h4>
                    </div>
                </div>
            </section>
        </nav>

        <section class="contenedor_frm">
            <form action="../../php/panel_control/dato/modificar_usuario.php" method="POST" class="creacion_usuario" enctype="multipart/form-data">
                <!-- Encabezado -->
                <header>
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>    
                    
                    <i class="fa-solid fa-table-list"></i>
                    <p>Seguimiento de solicitud - Vista detallada</p>
                </header>
    
                <!-- Contenedor de los diferentes datos -->
                <div class="contenedor_casillas">

                    <!-- =========== Mostrando datos -->
                    <!-- Identificador -->
                    <div class="item_contenedor item_input">
                        <p>ID Segumiento:</p>
                        <input type="text" name="id_usuario" value="<?php echo $resultado_datos['id_solicitud'] ?>" id="id_usuario">
                    </div>

                    <!-- Nombre -->
                    <div class="item_contenedor item_input">
                        <p>Nombre:</p>
                        <input type="text" name="grupo" value="<?php echo $resultado_datos['nombre'] ?>">
                    </div>

                    <!-- Proceso de registro -->
                    <div class="item_contenedor item_input">
                        <p>Correo:</p>
                        <input type="text" name="proceso_registro" value="<?php 
                            $detalle_solicitud = json_decode($resultado_datos['detalle_solicitud'], true);
                            echo $detalle_solicitud['correo'];
                        ?>">
                    </div>

                    <!-- Dirección -->
                    <div class="item_contenedor item_input">
                        <p>Dirección:</p>
                        <input type="text" name="nombre" value="<?php echo $resultado_datos['direccion'] ?>">
                    </div>

                    <!-- Contraseña -->
                    <div class="item_contenedor item_input">
                        <p>Estado solicitud:</p>
                        <input type="text" name="contraseña" value="<?php echo $resultado_datos['estado_solicitud'] ?>">
                    </div>

                    <!-- Correo -->
                    <div class="item_contenedor item_input">
                        <p>Teléfono:</p>
                        <input type="text" name="correo" value="<?php 
                            $detalle_solicitud = json_decode($resultado_datos['detalle_solicitud'], true);
                            echo $detalle_solicitud['telefono'];
                        ?>">
                    </div>

                    <!-- Imagen -->
                    <div class="item_contenedor imagen">
                        <p>Imagen:</p>

                        <div class="subcontenedor_imagen">
                            <div class="visualizador" id="visualizador_imagen">
                                <img src="<?php echo "../../" . $resultado_datos['imagen'] ?>" alt="imagen usuario">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opciones que ofrece el formulario -->
                <div class="controloles">
                    <button type="button" class="item_control btn1 finalizar_proceso">
                        <a href="seguimineto_de_solicitud.php">
                            <i class="fa-solid fa-arrow-left"></i>
                            Volver
                        </a>
                    </button>
                </div>
            </form>
        </section>
    </main>

    <!-- AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Sweetalert2 for alert message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Js personalizado -->
    <script src="../../js/btn_mode.js"></script>
    <script src="../../js/notificacion.js"></script>
    <script src="../../js/panel control/creacion_usuario_modificar.js"></script>
    <script src="../../js/panel control/modificar_datos.js"></script>
</body>
</html>