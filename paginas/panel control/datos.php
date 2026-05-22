<?php 
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    // vallidando usuario
    if (isset($_SESSION['imagen_usuario'])){
        $imagen_usuario = $_SESSION['imagen_usuario'];
        $perfil_usuario = $_SESSION['perfil_usuario'];
        $nombre_usuario = $_SESSION['nombre_usuario'];
    } else {
        header("location: ../../index.php");
        exit();
    }

    // obteniendo datos de los usuarios dependiendo del administrador
    if ($_SESSION['perfil_usuario'] == "s_admin"){
        $consulta = $conex->prepare(
            "SELECT * 
            FROM usuario
            WHERE LOWER(TRIM(perfil)) = 'usuario' 
            AND LOWER(TRIM(estado_cuenta)) <> 'inactiva'"
        );

        $consulta->execute();

        // Obteniendo nombre de institucion, instituciones
        $consulta_grupos_institucion = $conex->prepare("SELECT nombre_institucion FROM institucion");
        $consulta_grupos_institucion->execute();
        
        if ($consulta_grupos_institucion->rowCount() > 0){
            $resultado_grupo_institucion = $consulta_grupos_institucion->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $resultado_grupo_institucion = array();
        } 
    } else {
        $consulta = $conex->prepare(
            "SELECT * FROM usuario
            WHERE LOWER(TRIM(perfil)) = 'usuario' 
            AND LOWER(TRIM(estado_cuenta)) <> 'inactiva' AND identificador_institucion = ?"
        );
        
        $consulta->execute([$_SESSION['nombre_usuario']]);

        // Obteniendo grupos creados, categorias institucion
        $consulta_grupos = $conex->prepare("SELECT grupo FROM usuario where LOWER(perfil) = 'usuario' AND estado_cuenta <> 'inactiva' AND identificador_institucion = ? GROUP BY grupo ASC");
        $consulta_grupos->execute([$_SESSION['nombre_usuario']]);

        if ($consulta_grupos->rowCount() > 0){
            $resultado_grupo = $consulta_grupos->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $resultado_grupo = array();
        } 
    }

    if (isset($_SESSION['datos_visualizacion'])){
        $usuarios = $_SESSION['datos_visualizacion'];
    } else {
        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIMOVIT</title>
    <link rel="stylesheet" href="../../css/normalize.css">

    <link rel="icon" href="../../imagenes/logo.ico" type="image/x-icon">
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../../css/btn_modo.css">
    <link rel="stylesheet" href="../../css/loader.css">
    <link rel="stylesheet" href="../../css/panel_administrador/estructura.css">
    <link rel="stylesheet" href="../../css/panel_administrador/menu_vertical.css">
    <link rel="stylesheet" href="../../css/panel_administrador/contenedor_frm.css">
    <link rel="stylesheet" href="../../css/cargar_usuarios.css">
    <link rel="stylesheet" href="../../css/notificacion.css">
    
    <link rel="stylesheet" href="../../css/panel_administrador/datos.css">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>

    <!-- Sweetalert2 for alert message -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
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

        <!-- Ventana de para subir los multiples datos del usuario -->
        <form method="post" action="../../php/panel_control/dato/guardar_multiples_datos.php" class="contenedor_guardar_usuarios inactive_g_u" id="contenedor_guardar_usuarios" enctype="multipart/form-data">
            <span class="btn_c_guardar_usuarios" id="btn_guardar_usuarios"><i class="fa-solid fa-x"></i></span>

            <div class="subcontenedor_guardar_usuarios">
                <h3>Cargar archivo</h3>
                <p> <b>⚠️ Requisitos del archivo</b> <br>
                    ● El archivo debe ser de Excel (.xlsx o .xls) y contener únicamente estos encabezados, en este orden: <br> 

                    ● nombre, correo, perfil, grupo, proceso_registro, imagen, identificador_institucion. <br>

                    ● No se permiten columnas adicionales. <br>

                    ● La columna imagen debe contener una ruta válida a un archivo de imagen visible (JPG, PNG, etc.).
                </p>

                <div for="archivo" class="contentedor_archivo">
                    <img src="../../imagenes/file.png" alt="" id="imagen_archivo">
                    <p id="texto_archivo"></p>
                </div>
                <input type="file" id="archivo" name="archivo">

                <div class="controles">
                    <label for="archivo" id="btn_subir_archivo" class="btn_subir_archivo btn1">
                        <i class="fa-solid fa-file-import"></i> Subir archivo
                    </label>

                    <button type="submit" class="btn_lista_usuarios btn1"><i class="fa-solid fa-file-import"></i> Cargar datos</button>
                </div>
            </div>
        </form>

        <!-- Menu -->
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
            <div class="content">
                <div class="titulo">
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>          
                    <i class="fa-solid fa-table-list"></i>
                    <h1>Visualización de datos</h1>
                </div>
                
                <div class="filters">
                    <div class="contenedor_buscador">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Buscador" class="buscador">
                    </div>

                    <div class="filtros">
                        <button class="filter-btn">
                            <img src="../../Imagenes/filtrar.png" alt="Filtro">
                            Filtro
                            <img src="../../Imagenes/caret-derecha.png" alt="Flecha derecha" class="flecha_derecha">
                        </button>

                        <?php if ($_SESSION['perfil_usuario'] == "s_admin" ) { ?>
                            <form action="../../php/panel_control/dato/datos_visualizacion.php" method="post">
                                <select class="group-select-institucion" name="institucion" id="grupo_institucion">
                                    <!-- Filtrando instituciones -->
                                    <option value="Seleccione una institución">Seleccione una institución</option>
                                    <?php 
                                    foreach($resultado_grupo_institucion as $row){
                                    ?>
                                        <option><?php echo $row['nombre_institucion'] ?></option>
                                    <?php } ?>
                                </select>

                                <select class="group-select-categoria" name="categoria" id="grupo_categoria">
                                    <!-- Filtrando grupo de instituciones, con JS -->
                                </select>

                                <button class="btn1 btn_buscar">Buscar categoria</button>
                            </form>
                        <?php } else { ?>
                            <select class="group-select">
                                <!-- Filtrando instituciones -->
                                <?php 
                                foreach($resultado_grupo as $row){
                                ?>
                                    <option><?php echo $row['grupo'] ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
    
                        
                        <div class="radio-group">
                            <label><input type="radio" name="filter" checked> <span></span>Nombre</label>
                            <label><input type="radio" name="filter"> <span></span>Correo</label>
                        </div>

                        <button id="btn_guardar_usuarios" class="btn_lista_datos btn1"><i class="fa-solid fa-file-import"></i> Importar lista</button>
                    </div>
                </div>
    
                <div class="contenedor_tabla scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Grupo</th>
                                <th>Correo</th>
                                <?php if ($_SESSION['perfil_usuario'] != "s_admin" ) {} else { ?>
                                    <th>Institución</th>
                                <?php } ?>
                                <th>Estado</th>
                                <th>Monto</th>
                                <?php if ($_SESSION['perfil_usuario'] == "s_admin" ) {} else { ?>
                                    <th>Control</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                              <!-- Filtrando datos del usuario -->
                              <?php foreach ($usuarios as $row) { ?>
                                <tr>
                                    <td class="id_usuario">
                                        <?php echo $row['id_usuario'] ?>
                                    </td>
                                    <td>
                                        <div class="datos_basicos">
                                            <!--Foto del usuario-->
                                            <img src="<?php echo "../../" . $row['imagen'] ?>" alt="Perfil" class="perfil">
                                            <?php echo $row['nombre'] ?>
                                        </div>
                                    </td>

                                    <td class="grupo">
                                        <?php echo $row['grupo'] ?>
                                    </td>
        
                                    <td><?php echo $row['correo'] ?></td>
                                    <?php if ($_SESSION['perfil_usuario'] != "s_admin" ) { ?> 
                                        <td>
                                            <div class="estado_tarjeta">
                                                <span class="<?php echo strtolower($row['estado_monto']); ?>"></span>
                                                <p><?php echo $row['estado_monto'] ?></p>
                                            </div>
                                        </td>

                                        <td><?php echo $row['monto'] ?> DOP</td>
                                    <?php } else { ?>
                                        <td><?php echo $row['identificador_institucion'] ?></td>
                                        <td>
                                            <div class="estado_tarjeta">
                                                <span class="<?php echo strtolower($row['estado_monto']); ?>"></span>
                                                <p><?php echo $row['estado_monto'] ?></p>
                                            </div>
                                        </td>

                                        <td><?php echo $row['monto'] ?> DOP</td>
                                    <?php } ?>
     
                                    <?php if ($_SESSION['perfil_usuario'] == "s_admin" ) {} else { ?>
                                        <td>
                                            <div class="controles">
                                                <button class="btns edit-btn">
                                                    <a href="../../paginas/panel control/creacion_usuario_modificar.php?id_usuario=<?php echo $row['id_usuario'] ?>">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                        Editar
                                                    </a>
                                                </button>
                                            </div>                
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <!-- AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Leer archivos excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- Sweetalert2 for alert message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- JS personalizado -->

    <script src="../../js/btn_mode.js"></script>
    <script src="../../js/cargar_usuarios.js"></script>
    <script src="../../js/notificacion.js"></script>

    <?php if ($_SESSION['perfil_usuario'] == "s_admin" ) { ?>
        <script src="../../js/panel control/formar_grupos.js"></script>
        <script src="../../js/panel control/datos.js"></script>
    <?php } else { ?>
        <script src="../../js/panel control/filtro_datos.js"></script>
    <?php } ?>
</body>
</html>
