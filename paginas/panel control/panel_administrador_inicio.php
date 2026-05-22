<?php
    session_start(); // Iniciar la sesión
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    if (isset($_SESSION['imagen_usuario'])){
        $imagen_usuario = $_SESSION['imagen_usuario'];
        $perfil_usuario = $_SESSION['perfil_usuario'];
        $nombre_usuario = $_SESSION['nombre_usuario'];

        if (isset($_SESSION['vinculo_institucion'])){
            if ($_SESSION['perfil_usuario'] == "s_admin" ) {
                $vinculo_institucion = $_SESSION['vinculo_institucion'];

                // grupo_instituciones
                $consulta_instituciones2 = $conex->prepare("SELECT * FROM institucion");
                $consulta_instituciones2->execute();
                $resultado_ins2 = $consulta_instituciones2->fetchAll(PDO::FETCH_ASSOC);
                $grupos_instituciones = $resultado_ins2;
            } else {
                $vinculo_institucion = $_SESSION['vinculo_institucion'];
            }
        } else {
            if ($_SESSION['perfil_usuario'] == "s_admin" ) {
                // -- para el super admin
                $consulta_instituciones = $conex->prepare("SELECT * FROM institucion LIMIT 1");
                $consulta_instituciones->execute();
                $resultado_ins = $consulta_instituciones->fetch(PDO::FETCH_ASSOC);
                $vinculo_institucion = $resultado_ins['nombre_institucion'];

                // grupo_instituciones
                $consulta_instituciones2 = $conex->prepare("SELECT * FROM institucion");
                $consulta_instituciones2->execute();
                $resultado_ins2 = $consulta_instituciones2->fetchAll(PDO::FETCH_ASSOC);
                $grupos_instituciones = $resultado_ins2;
            } else {
                $vinculo_institucion = $nombre_usuario;
            }
        }

        // Realizando los calculos a traves de consultas para mostrar la estadistica en pantalla
        // -- total usuarios
        $consulta_usuarios = $conex->prepare("SELECT COUNT(*) as total_usuarios FROM usuario WHERE LOWER(perfil) = 'usuario' AND estado_cuenta = 'activa' AND identificador_institucion = ?");
        $consulta_usuarios->execute([$vinculo_institucion]);
        $resultado_usuarios = $consulta_usuarios->fetch(PDO::FETCH_ASSOC);

        // -- monto de recargas
        $consulta_recargas = $conex->prepare("SELECT SUM(monto) as total_recargas FROM registro_recarga WHERE identificador_institucion = ?");
        $consulta_recargas->execute([$vinculo_institucion]);
        $resultado_recargas = $consulta_recargas->fetch(PDO::FETCH_ASSOC);

        // -- registros de recargas
        $consulta_recargas_detalle = $conex->prepare("SELECT * FROM registro_recarga WHERE identificador_institucion = ? GROUP BY id_usuario ASC");
        $consulta_recargas_detalle->execute([$vinculo_institucion]);
        $resultado_recargas_detalles = $consulta_recargas_detalle->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // header("location: ../../index.php");
        // exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../css/normalize.css">

    <!-- CSS personalizado -->
    <link rel="icon" href="../../imagenes/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../css/btn_modo.css">
    <link rel="stylesheet" href="../../css/panel_administrador/estructura.css">
    <link rel="stylesheet" href="../../css/panel_administrador/menu_vertical.css">
    <link rel="stylesheet" href="../../css/panel_administrador/contenedor_frm.css">
    <link rel="stylesheet" href="../../css/panel_administrador/frm_resumen.css">
    <link rel="stylesheet" href="../../css/notificacion.css">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>
    <title>Panel del administrador inicio</title>
</head>
<body>
    <main>
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
                            <li><a href="creacion_usuario_guardar.php">Creación</a></li>
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

        <div class="contenedor_frm">
            <div class="frm_resumen">
                <header>
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>    
                    
                    <i class="fa-solid fa-wallet"></i>
                    <p>Resumen de la aplicación</p>
                </header>

                <!-- Buscador -->
                <div class="filters">
                    <form class="filtros" method="post" action="../../php/panel_control/resumen/vinculo_institucion.php">
                        <button class="filter-btn">
                            <img src="../../Imagenes/filtrar.png" alt="Filtro">
                            Filtro
                            <img src="../../Imagenes/caret-derecha.png" alt="Flecha derecha" class="flecha_derecha">
                        </button>

                        <select class="group-select" name="categoria">
                            <!-- Filtrando instituciones -->    
                            <?php if ($_SESSION['perfil_usuario'] == "s_admin" ) {
                                foreach($grupos_instituciones as $row){ ?>                   
                                <option><?php echo $row['nombre_institucion'] ?></option>
                            <?php } } else { ?>
                                <option><?php echo $vinculo_institucion ?></option>
                            <?php } ?>
                        </select>

                        <button type="submit" name="buscar" class="btn1 btn_buscar">
                            Buscar
                        </button>
                    </form>
                </div>
    
                <!-- -------- datos -->
                <div class="contenedor_datos">
                    <!-- Historial de recarga -->
                    <div class="item_dato historial scroll">
                        <h3>Historial de recargas</h3>
                        <hr>
                        <!-- Buscador -->
                        <div class="buscador">
                            <div class="filters">
                                <div class="contenedor_buscador">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" name="filter" placeholder="Buscador" class="buscador buscador_h">
                                </div>

                                <div class="filtros">
                                    <button class="filter-btn">
                                        <img src="../../Imagenes/filtrar.png" alt="Filtro">
                                        Filtro
                                        <img src="../../Imagenes/caret-derecha.png" alt="Flecha derecha" class="flecha_derecha">
                                    </button>
          
                                    <div class="radio-group">
                                        <label><input type="radio" name="filter_h" checked> <span></span>Nombre</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="contenedor_datos_historial">
                            <!-- Filtrando datos -->
                            <?php foreach($resultado_recargas_detalles as $row){ 
                                // buscando datos del usuario
                                $id_user_row = $row['id_usuario'];
                                
                                $consulta_datos_usuario = $conex->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
                                $consulta_datos_usuario->execute([$id_user_row]);

                                $resultado_datos_usuario = $consulta_datos_usuario->fetch(PDO::FETCH_ASSOC);

                                $nombre = $resultado_datos_usuario['nombre'];
                                $imagen = $resultado_datos_usuario['imagen'];
                                $consulta_datos_usuario = null;
                            ?>
                                <div class="item_historial">
                                    <div class="encabezado">
                                        <div class="id_dato"><?php echo $id_user_row ?></div>

                                        <div class="datos">
                                            <img src="<?php echo "../../" . $imagen ?>" alt="">
                                            <p><?php echo $nombre ?></p>
                                        </div>

                                        <button id="btn_expandir_recargas" class="btn1 btn_expandir_recargas">Detalles</button>
                                    </div>  

                                    <div class="detalles">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Monto</th>
                                                    <th>Institución</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>25/1/2024</td>
                                                    <td>1000 DOP</td>
                                                    <td>AnderCom</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Total de usuarios -->
                    <div class="item_numerico total_usuarios">
                        <div class="dato_item datos">
                            <h3>Total de usuarios</h3>
                            <p>Registrados</p>
    
                            <p class="calculo">
                                <?php echo $resultado_usuarios['total_usuarios']; ?>
                            </p>
                        </div>
                        <div class="dato_item imagen">
                            <img src="../../imagenes/ilustracion_grupo_sin_fondo.png" alt="">
                        </div>
                    </div>
    
                    <!-- Total de recargas -->
                    <div class="item_numerico total_recargas">
                        <div class="dato_item datos">
                            <h3>Total de recargas</h3>
                            <p>Registradas</p>
    
                            <p class="calculo">
                                <?php echo number_format($resultado_recargas['total_recargas']); ?>
                                DOP
                            </p>
                        </div>
                        <div class="dato_item imagen">
                            <img src="../../imagenes/tarjeta_ilustracion.png" alt="">
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </main>
    
    <!-- AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Js personalizado -->
    <script src="../../js/btn_mode.js"></script>
    <script src="../../js/notificacion.js"></script>
    <script src="../../js/resumen/detalles_recarga.js"></script>
    <script src="../../js/resumen/buscador.js"></script>
</body>
</html>