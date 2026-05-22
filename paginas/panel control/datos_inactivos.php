<?php 
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    session_start();

    if (isset($_SESSION['imagen_usuario'])){
        $imagen_usuario = $_SESSION['imagen_usuario'];
        $perfil_usuario = $_SESSION['perfil_usuario'];
        $nombre_usuario = $_SESSION['nombre_usuario'];
    } else {
        header("location: ../../index.php");
        exit();
    }

     // obteniendo datos de los usuarios dependiendo del administrador
    // obteniendo datos de los usuarios
    $consulta = $conex->prepare(
        "SELECT t1.*, t2.* 
        FROM usuario AS t1
        INNER JOIN historial_inactivo AS t2 
        ON t1.id_usuario = t2.id_user 
        WHERE LOWER(t1.perfil) = 'usuario' 
        AND t1.estado_cuenta = 'inactiva' AND t1.identificador_institucion = ?"
    );

    $consulta->execute([$nombre_usuario]);
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Obteniendo grupos creados
    $consulta_grupos = $conex->prepare("SELECT grupo FROM usuario where LOWER(perfil) = 'usuario' AND identificador_institucion = ? GROUP BY grupo ASC");
    $consulta_grupos->execute([$nombre_usuario]);
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
            <div class="content">
                <div class="titulo">
                    <label for="btn_menu" class="btn_menu">
                        <i class="fa-solid fa-bars ani1_icon"></i>
                    </label>          
                    <i class="fa-solid fa-table-list"></i>
                    <h1>Visualización de cuentas inactivas</h1>
                </div>
                
                <div class="filters">
                    <div class="contenedor_buscador">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Buscador" class="buscador">
                    </div>

                    <div class="filtros">
                        <button class="filter-btn">
                            <img src="../../imagenes/filtrar.png" alt="Filtro">
                            Filtro
                            <img src="../../imagenes/caret-derecha.png" alt="Flecha derecha" class="flecha_derecha">
                        </button>

                        <select class="group-select">
                            <!-- Filtrando grupos -->
                            <?php 
                            foreach($resultado_grupo as $row){
                            ?>
                                <option><?php echo $row['grupo'] ?></option>
                            <?php } ?>
                        </select>
                        
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
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Grupo</th>
                                <th>Correo</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>Controles</th>
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
                                <td><?php echo $row['motivo'] ?></td>
                                <td><?php echo $row['fecha'] ?></td>
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

    <!-- Sweetalert2 for alert message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- JS personalizado -->
    <script src="../../js/panel control/datos.js"></script>
    <script src="../../js/btn_mode.js"></script>
</body>
</html>
