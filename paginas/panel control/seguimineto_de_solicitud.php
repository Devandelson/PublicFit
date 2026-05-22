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

    // obteniendo datos de los usuarios, dependiendo del tipo de admin
    if ($_SESSION['perfil_usuario'] == "s_admin") {
        $consulta = $conex->prepare(
            "SELECT * 
            FROM seguimiento_solicitudes
            WHERE LOWER(TRIM(tipo_solicitud)) = 'institucion'"
        );
    } else {
        $consulta = $conex->prepare(
            "SELECT * 
            FROM seguimiento_solicitudes
            WHERE LOWER(TRIM(tipo_solicitud)) = 'usuario'"
        );
    }

    
    $consulta->execute();
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Obteniendo grupos creados
    // $consulta_grupos = $conex->prepare("SELECT grupo FROM usuario where LOWER(perfil) = 'usuario' AND estado_cuenta <> 'inactiva' GROUP BY grupo ASC");
    // $consulta_grupos->execute();
    // if ($consulta_grupos->rowCount() > 0){
    //     $resultado_grupo = $consulta_grupos->fetchAll(PDO::FETCH_ASSOC);
    // } else {
    //     $resultado_grupo = array();
    // } 
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
    
    <link rel="stylesheet" href="../../css/panel_administrador/datos.css">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>

    <!-- Sweetalert2 for alert message -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
</head>
<body>
    <!-- <div class="contenedor_loader" id="contenedor_loader">
        From Uiverse.io by mobinkakei 
        <div id="wifi-loader">
            <svg class="circle-outer" viewBox="0 0 86 86">
                <circle class="back" cx="43" cy="43" r="40"></circle>
                <circle class="front" cx="43" cy="43" r="40"></circle>
                <circle class="new" cx="43" cy="43" r="40"></circle>
            </svg>
            <svg class="circle-middle" viewBox="0 0 60 60">
                <circle class="back" cx="30" cy="30" r="27"></circle>
                <circle class="front" cx="30" cy="30" r="27"></circle>
            </svg>
            <svg class="circle-inner" viewBox="0 0 34 34">
                <circle class="back" cx="17" cy="17" r="14"></circle>
                <circle class="front" cx="17" cy="17" r="14"></circle>
            </svg>
            <div class="text" data-text="Searching"></div>
        </div>
    </div> -->

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
                            <li><a href="datos.php">Visualización</a></li>             
                            <li><a href="datos_inactivos.php">Cuentas canceladas</a></li>             
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
                        <h4><?php echo $perfil_usuario ?></h4>
                    </div>
                </div>
            </section>
        </nav>

        <div class="contenedor_frm">
            <!-- Formulario para el envio de datos para aprobar o cancelar la solicitud (Especificamente para que el correo funcione) -->
            <form action="../../php/solicitud/editar_solicitud.php" method="post" class="formulario_envio_solicitud" id="formulario_envio_solicitud">
                <input type="text" name="id_solicitud" id="id_solicitud_frm">
                <input type="text" name="estado_solicitud" id="detalles_solicitud_frm">
                <input type="text" name="direccion" id="direccion">
            </form>

            <div class="content">
                <div class="titulo">
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>          
                    <i class="fa-solid fa-table-list"></i>
                    <h1>Seguimiento de solicitud</h1>
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

                        <?php 
                              if ($_SESSION['perfil_usuario'] == "s_admin"){} else {
                              $categorias = explode("," , rtrim($_SESSION['categorias'], ","));
                        ?>
                        <select class="group-select">
                            <!-- Filtrando los grupos dependiendo del tipo de admin -->
                            <?php foreach($categorias as $row){ ?>
                                <option><?php echo $row ?></option>
                            <?php } ?>
                        </select>
                        <?php } ?>
    
                        
                        <div class="radio-group">
                            <label><input type="radio" name="filter" checked> <span></span>Nombre</label>
                            <label><input type="radio" name="filter"> <span></span>Correo</label>
                        </div>
                    </div>
                </div>
    
                <div class="contenedor_tabla scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <?php if ($_SESSION['perfil_usuario'] == "admin"){ ?>
                                <th>Grupo</th>
                                <?php } ?>
                                <th>Aprobado</th>
                                <th>Cancelado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filtrando datos del usuario -->
                            <?php foreach ($usuarios as $index => $row) {
                                if ($_SESSION['perfil_usuario'] == "admin"){
                                    $vinculo_institucion = json_decode($row['detalle_solicitud'], true)['vinculo_institucion'];
                                    
                                } else {
                                    $vinculo_institucion = "";
                                }
                            ?>
                            <tr>    
                                <?php 
                                    if ($_SESSION['nombre_usuario'] != $vinculo_institucion && $_SESSION['perfil_usuario'] == "admin"){}else {
                                ?>
                                <td class="id_dato">
                                    <?php echo $row['id_solicitud'] ?>
                                </td>

                                <td>
                                    <div class="datos_basicos">
                                        <!--Foto del usuario-->
                                        <img src="<?php echo "../../" . $row['imagen'] ?>" alt="Perfil" class="perfil">
                                        <?php echo $row['nombre'] ?>
                                    </div>
                                </td>

                                <td><?php 
                                    $detalle_solicitud = json_decode($row['detalle_solicitud'], true);
                                    echo $detalle_solicitud['correo'];
                                ?></td>

                                <?php if ($_SESSION['perfil_usuario'] == "admin"){ ?>
                                    <th>
                                        <?php 
                                            $detalle_solicitud = json_decode($row['detalle_solicitud'], true);
                                            echo $detalle_solicitud['grupo'];
                                        ?>
                                    </th>
                                <?php } ?>
                                  
                                <td class="estado_solicitud">
                                    <label class="checkbox-container">
                                        <input class="custom-checkbox" type="radio" id="estado_solicitud1" name="estado_solicitud<?php echo $index ?>" value="aprobado" 
                                            <?php echo ($row['estado_solicitud'] == "aprobado") ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>

                                <td>
                                    <label class="checkbox-container">
                                        <input class="custom-checkbox" type="radio" id="estado_solicitud2" name="estado_solicitud<?php echo $index ?>" value="pendiente" 
                                            <?php echo ($row['estado_solicitud'] == "pendiente") ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>

      
                                <td>
                                    <div class="controles">
                                        <button class="btns edit-btn" id="btn_cambios">
                                            <a href="#">
                                                Enviar datos
                                            </a>
                                        </button>
                                        <button class="btns edit-btn">
                                            <a href="seguimineto_de_solicitud_vista.php?id_dato=<?php echo $row['id_solicitud'] ?>">
                                                Ver datos
                                            </a>
                                        </button>
                                    </div>                
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <!-- AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Sweetalert2 for alert message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- JS personalizado -->
    <script src="../../js/panel control/datos.js"></script>
    <script src="../../js/btn_mode.js"></script>
    <script src="../../js/solicitud/enviar_cambios.js"></script>
</body>
</html>
