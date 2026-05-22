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
    if (isset($_GET['id_usuario']) || isset($_SESSION['id_usuario'])){
        $_SESSION['id_usuario'] = (isset($_GET['id_usuario'])) 
        ? $_GET['id_usuario'] : $_SESSION['id_usuario'];

        $id_user = $_SESSION['id_usuario'];

        // para luego filtrar los datos, mediante el id.
        $consulta_datos = $conex->prepare("SELECT * FROM usuario where id_usuario = ?");
        $consulta_datos->execute([$id_user]);
        $resultado_datos = $consulta_datos->fetch(PDO::FETCH_ASSOC);

        // obtener datos de historial de inactividad
        $consulta_inactividad = $conex->prepare("SELECT * From historial_inactivo WHERE id_user = ?");
        $consulta_inactividad->execute([$id_user]);
        $result_inactividad = $consulta_inactividad->fetch(PDO::FETCH_ASSOC);
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
                            <?php if ($_SESSION['perfil_usuario'] != "s_admin" ) { ?>    
                                <li><a href="datos_inactivos.php" class="item_bloqueado">Cuentas canceladas</a></li>    
                            <?php } ?>           
                        </ul>
                    </div>
                </label>

                <li class="item_menu item_bloqueado">
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
            <form action="../../php/panel_control/dato/modificar_usuario.php" method="POST" class="creacion_usuario" enctype="multipart/form-data">
                <!-- Encabezado -->
                <header>
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>    
                    
                    <i class="fa-solid fa-user-pen"></i>
                    <p>Modificación de Datos del Usuario</p>
                </header>
    
                <!-- Contenedor de los diferentes datos -->
                <div class="contenedor_casillas">

                    <!-- =========== Mostrando datos -->
                    <!-- Identificador -->
                    <div class="item_contenedor item_input">
                        <p>*ID:</p>
                        <input type="text" name="id_usuario" value="<?php echo $resultado_datos['id_usuario'] ?>" id="id_usuario">
                    </div>

                    <!-- Grupo -->
                    <div class="item_contenedor item_input">
                        <p>*Grupo:</p>
                        <input type="text" name="grupo" value="<?php echo $resultado_datos['grupo'] ?>">
                    </div>

                    <!-- Proceso de registro -->
                    <div class="item_contenedor item_input">
                        <p>Proceso de registro:</p>
                        <input type="text" name="proceso_registro" value="<?php echo $resultado_datos['proceso_registro'] ?>">
                    </div>

                    <!-- Nombre -->
                    <div class="item_contenedor item_input">
                        <p>*Nombre:</p>
                        <input type="text" name="nombre" value="<?php echo $resultado_datos['nombre'] ?>">
                    </div>

                    <!-- Contraseña -->
                    <div class="item_contenedor item_input">
                        <p>*Contraseña:</p>
                        <input type="password" name="contraseña" value="<?php echo $resultado_datos['contrasena'] ?>">
                    </div>

                    <!-- Correo -->
                    <div class="item_contenedor item_input">
                        <p>*Correo:</p>
                        <input type="text" name="correo" value="<?php echo $resultado_datos['correo'] ?>">
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
                        <p>*Monto actual tarjeta SD Go:</p>
                        <input type="text" name="monto" value="<?php echo $resultado_datos['monto'] ?>">
                    </div>

                    <!-- Estado tarjeta -->
                    <div class="item_contenedor item_input">
                        <p>*Estado de la tarjeta:</p>
                        <select name="estado_tarjeta">
                            <option value="Monto insuficiente">Monto insuficiente</option>
                            <option value="Bien">Bien</option>
                        </select>
                    </div>

                    <!-- Ruta informal del usuario -->
                    <div class="item_contenedor item_input">
                        <p>*Ruta del usuario:</p>
                        <textarea name="detalle_ruta_usuario"><?php echo $resultado_datos['detalle_ruta_usuario'] ?></textarea>
                    </div>   

                    <!-- Imagen -->
                    <div class="item_contenedor imagen">
                        <p>*Imagen del usuario:</p>

                        <div class="subcontenedor_imagen">
                            <div class="visualizador" id="visualizador_imagen">
                                <img src="<?php echo "../../" . $resultado_datos['imagen'] ?>" alt="imagen usuario">
                            </div>

                            <label for="imagen_usuario" class="btn_imagen_usuario btn1">
                                <i class="fa-solid fa-images"></i> Cambiar imagen
                                <input type="file" name="imagen_nueva" id="imagen_usuario">
                            </label>
                        </div>
              
                        <input type="hidden" name="imagen_antigua" value="<?php echo $resultado_datos['imagen'] ?>">
                    </div>

                    <!-- <div class="item_contenedor mapa">
                        <p class="titulo_mapa">Detalles de ruta del usuario:</p>
                        <img src="../../imagenes/mapa.jpg" alt="">
                        <p> echo $resultado_datos['detalle_ruta_api'] ?></p>
                    </div> -->

                    <!-- Opción de confirmacion de guardar datos en el historial de recargas -->
                    <div class="item_contenedor item_input">
                        <p>¿Guardar en el historial de recargas?:</p>
                        <select name="validacion_recarga">
                            <option value="si">Si</option>
                            <option value="no" selected>No</option>
                        </select>
                    </div>

                    <!-- Opción de desactivar cuenta del usuario -->
                    <div class="item_contenedor item_input estado_cuenta">
                        <p>Estado de la cuenta:</p>
                        <select name="estado_cuenta" id="estado_cuenta">
                            <?php  
                                if ($resultado_datos['estado_cuenta'] == "inactiva"){
                                    echo 
                                    "<option value='activa'>Activa</option>
                                    <option value='inactiva' selected>Inactiva</option>";
                                } else {
                                    echo 
                                    "<option value='activa' selected>Activa</option>
                                    <option value='inactiva'>Inactiva</option>";
                                }
                            ?>
                        </select>

                        <textarea name="comentario_estado_cuenta" placeholder="Por favor, especifique cuál es la razón."><?php echo (empty($result_inactividad['motivo'])) ? "" : $result_inactividad['motivo']?></textarea>
                    </div>
                </div>

                <!-- Opciones que ofrece el formulario -->
                <div class="controloles">
                    <button type="submit" name="btn_modificar" class="item_control btn1">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Actualizar datos
                    </button>

                    <!-- Condición de finalizar o cancelar proceso -->
                    <?php if (isset($_SESSION['finalizacion_proceso'] ) && $_SESSION['finalizacion_proceso']  == true){ ?>
                        <button type="button" class="item_control btn1 finalizar_proceso">
                            <a href="../../php/panel_control/dato/cancelar_proceso_modificar.php">
                                <i class="fa-solid fa-info"></i>
                                Finalizar proceso
                            </a>
                        </button>
                    <?php } else { ?>
                        <button type="button" class="item_control btn1 cancelar_proceso">
                            <a href="../../php/panel_control/dato/cancelar_proceso_modificar.php">
                                <i class="fa-solid fa-ban"></i>
                                Cancelar proceso
                            </a>
                        </button>
                    <?php } ?>

                    <button type="button" name="btn_eliminar" class="item_control btn1 btn_eliminar" id="eliminar_cuenta">
                        <i class="fa-solid fa-user-xmark"></i>
                        Eliminar usuario
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