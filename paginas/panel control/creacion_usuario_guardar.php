<?php 
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    if (isset($_SESSION['imagen_usuario'])){
        $imagen_usuario = $_SESSION['imagen_usuario'];
        $perfil_usuario = $_SESSION['perfil_usuario'];
        $nombre_usuario = $_SESSION['nombre_usuario'];

        // Realizando los calculos a traves de consultas para mostrar la estadistica en pantalla
        // -- usuarios
        $consulta_usuarios = $conex->prepare("SELECT COUNT(*) as total_usuarios FROM usuario WHERE LOWER(perfil) = 'usuario'");
        $consulta_usuarios->execute();
        $resultado_usuarios = $consulta_usuarios->fetch(PDO::FETCH_ASSOC);

        // -- recargas
        $consulta_recargas = $conex->prepare("SELECT SUM(monto) as total_recargas FROM registro_recarga");
        $consulta_recargas->execute();
        $resultado_recargas = $consulta_recargas->fetch(PDO::FETCH_ASSOC);
    } else {
        header("location: ../../index.php");
        exit();
    }

    // Obteniendo grupos creados
    $consulta_grupos = $conex->prepare("SELECT grupo FROM usuario GROUP BY grupo ASC");
    $consulta_grupos->execute();
    if ($consulta_grupos->rowCount() > 0){
        $resultado_grupo = $consulta_grupos->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $resultado_grupo = array();
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
                <li class="item_menu">
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
                            <li><a href="datos.php">Visualización</a></li>
                            <?php if ($_SESSION['perfil_usuario'] != "s_admin" ) { ?>
                                <li><a href="datos_inactivos.php">Cuentas canceladas</a></li>   
                            <?php } ?>            
                        </ul>
                    </div>
                </label>

                <li class="item_menu">
                    <a href="seguimineto_de_solicitud.php">
                        <i class="fa-solid fa-wallet"></i> Seguimiento de solicitud
                    </a>
                </li>
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
                        <h4><?php 
                            if ($perfil_usuario == "s_admin") {
                                echo "Super administrador";
                            } else {
                                echo $perfil_usuario;
                            }
                        ?></h4>
                    </div>
                </div>
            </section>
        </nav>

        <section class="contenedor_frm">
            <form method="post" enctype="multipart/form-data" action="../../php/panel_control/dato/guardar_usuario.php" class="creacion_usuario">
                <!-- Encabezado -->
                <header>
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>    
                    
                    <i class="fa-solid fa-user-plus"></i>
                    <p>Creación de usuarios</p>
                </header>
    
                <!-- Contenedor de los diferentes datos -->
                <div class="contenedor_casillas">
                    <!-- =========== Mostrando datos -->
                    <!-- Grupo -->
                    <div class="item_contenedor item_input">
                        <p>*Grupo:</p>
                        <input type="text" name="grupo"  list="grupos">

                        <datalist id="grupos">
                            <?php foreach ($resultado_grupo as $grupo) { ?>
                                <option value="<?php echo $grupo['grupo'] ?>">
                            <?php } ?>
                        </datalist>
                    </div>

                    <!-- Proceso de registro -->
                    <div class="item_contenedor item_input">
                        <p>Proceso de registro:</p>
                        <select name="proceso_registro">
                            <option value="proceso">En proceso</option>
                            <option value="completo">Completo</option>
                        </select>
                    </div>

                    <!-- Nombre -->
                    <div class="item_contenedor item_input">
                        <p>*Nombre:</p>
                        <input type="text" name="nombre" >
                    </div>

                    <!-- Contraseña -->
                    <div class="item_contenedor item_input">
                        <p>*Contraseña:</p>
                        <input type="password" name="contraseña">
                    </div>

                    <!-- Correo -->
                    <div class="item_contenedor item_input">
                        <p>*Correo:</p>
                        <input type="text" name="correo">
                    </div>

                    <!-- Identificador institucion -->
                    <div class="item_contenedor item_input">
                        <p>*Identificador de la institución:</p>
                        <select name="vinculo_institucion">
                            <option value="<?php echo $_SESSION['nombre_usuario']; ?>" selected><?php echo $_SESSION['nombre_usuario']; ?></option>
                        </select>
                    </div>

                    <!-- Monto tarjeta SD Go -->
                    <div class="item_contenedor item_input">
                        <p>Monto a recargar:</p>
                        <input type="text" name="monto">
                    </div>

                    <!-- Estado tarjeta -->
                    <div class="item_contenedor item_input">
                        <p>Estado de la tarjeta:</p>
                        <select name="estado_tarjeta">
                            <option value="Monto insuficiente">Monto insuficiente</option>
                            <option value="Bien">Bien</option>
                        </select>
                    </div>

                    <!-- Ruta informal del usuario -->
                    <div class="item_contenedor item_input">
                        <p>*Ruta del usuario:</p>
                        <textarea name="detalle_ruta_usuario"></textarea>
                    </div>   

                    <!-- Imagen -->
                    <div class="item_contenedor imagen">
                        <p>*Imagen del usuario:</p>

                        <div class="subcontenedor_imagen">
                            <div class="visualizador" id="visualizador_imagen">
                                <img src="../../imagenes/image.png" alt="imagen usuario">
                            </div>

                            <label for="imagen_usuario" class="btn_imagen_usuario btn1">
                                <i class="fa-solid fa-images"></i> Cambiar imagen
                                <input type="file" name="imagen_nueva" id="imagen_usuario">
                            </label>
                        </div>
                    </div>

                    <!-- <div class="item_contenedor mapa">
                        <p class="titulo_mapa">Detalles de ruta del usuario:</p>
                        <img src="../../imagenes/mapa.jpg" alt="">
                        <p> echo $resultado_datos['detalle_ruta_api'] ?></p>
                    </div> -->
                </div>

                <!-- Opciones que ofrece el formulario -->
                <div class="controloles">
                    <button type="submit" name="btn_guardar" class="item_control btn1">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </section>
    </main>

    <!-- Js personalizado -->
    <script src="../../js/btn_mode.js"></script>
    <script src="../../js/notificacion.js"></script>
    <script src="../../js/panel control/creacion_usuario_modificar.js"></script>
</body>
</html>